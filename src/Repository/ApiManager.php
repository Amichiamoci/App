<?php

namespace App\Repository;

use App\Entity\ApiError;
use Exception;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Serializer;

use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Component\HttpClient\HttpOptions;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ApiManager
{
    public function __construct(
        private HttpClientInterface $client,

        #[Autowire(service: 'cache.amichiamoci_api')]
        private CacheInterface $cache,
        
        private readonly string $environment = 'dev',
    ) { 
        $this->client = $this->client->withOptions(
            options: (new HttpOptions())
                ->setBaseUri(uri: getenv(name: 'API_URL'))
                ->setHeader(key: 'App-Bearer', value: getenv(name: 'API_TOKEN'))
                ->toArray()
        );
    }

    private function isProd(): bool
    {
        return $this->environment === 'prod';
    }

    //
    // Methods
    //

    private static function getSerializer(): Serializer
    {
        return new Serializer(
            normalizers: [
                new ObjectNormalizer(
                    classMetadataFactory: new ClassMetadataFactory(loader: new AttributeLoader()),
                    nameConverter: null, 
                    propertyAccessor: null,
                    propertyTypeExtractor: new ReflectionExtractor()
                ), 
                new ArrayDenormalizer(),
                new GetSetMethodNormalizer(), 
                new DateTimeNormalizer(defaultContext: [
                    DateTimeNormalizer::FORMAT_KEY => \DateTime::RFC3339,
                ]),
            ], 
            encoders: [
                new JsonEncoder()
            ],
        );
    }

    private function get(string $resource, array $options = []): ResponseInterface
    {
        $http_options = new HttpOptions();
        foreach ($options as $name => $value)
        {
            $http_options = $http_options->setHeader(key: 'Data-Param-' . $name, value: $value);
        }

        return $this->client->request(
            method: 'GET', 
            url: '?resource=' . $resource,
            options: $http_options->toArray(),
        );
    }

    private function loadWebCollection(
        string $collectionName, 
        string $className, 
        array $params = [],
    ):array
    {
        try {
            $response = $this->get(resource: $collectionName, options: $params);
            $data = $response->getContent(throw: false);
            $serializer = $this->getSerializer();

            if ($response->getStatusCode() >= 500)
            {
                $server_exception = $serializer->deserialize(
                    data: $data, 
                    type: ApiError::class, 
                    format: 'json', 
                    context: [
                        AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => true,
                        AbstractNormalizer::REQUIRE_ALL_PROPERTIES => false,
                    ],
                );
                throw new Exception(
                    message: $server_exception->BuildMessage(),
                );
            }
    
            return $serializer->deserialize(
                data: $data, 
                type: $className . "[]", 
                format: 'json', 
                context: [
                    AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => true,
                    AbstractNormalizer::REQUIRE_ALL_PROPERTIES => false,
                ],
            );
        }
        catch (\Throwable $ex) {
            if (!$this->isProd())
            {
                throw $ex;
            }
            return [];
        }
    }

    private static function cache_compute_key(
        string $collectionName, 
        int $duration,
        array $params = [],
    ): string {
        $cache_key = $collectionName;
        if (count(value: $params) !== 0)
        {
            $hash = sha1(string: serialize(value: $params) . $duration);
            $cache_key .= "-$duration-$hash";
        }
        return $cache_key;
    }

    public function _cacheInvalidate(
        string $collectionName, 
        array $params = [],
        int $duration = 3600,
    ): bool {
        $key = self::cache_compute_key(
            collectionName: $collectionName, 
            params: $params, 
            duration: $duration,
        );
        return $this->cache->delete(key: $key);
    }

    public function _getObjectCollection(
        string $collectionName, 
        string $className, 
        array $params = [],
        int|bool $cache = true,
    ): array
    {
        if ($cache === false || $cache <= 0)
        {
            return $this->loadWebCollection(
                collectionName: $collectionName, 
                className: $className, 
                params: $params,
            );
        }
        if ($cache === true)
        {
            $cache = 3600; // 1h cache
        }

        return $this->cache->get(
            key: self::cache_compute_key(
                collectionName: $collectionName, 
                params: $params, 
                duration: $cache,
            ), 
            callback: function (ItemInterface $item) use($collectionName, $className, $params, $cache): array {
                $item->expiresAfter(time: $cache); 
                return $this->loadWebCollection(
                    collectionName: $collectionName, 
                    className: $className, 
                    params: $params,
                );
            },
        );
        
    }

    use Api\SportManager;
    use Api\SubscriptionManager;
    use Api\StaffManager;
    use Api\ChurchesManager;

}