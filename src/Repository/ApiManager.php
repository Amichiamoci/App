<?php

namespace App\Repository;

use Symfony\Component\Serializer\Context\Normalizer\DateTimeNormalizerContextBuilder;
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

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Component\HttpClient\HttpOptions;

class ApiManager
{
    public function __construct(
        private HttpClientInterface $client,
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

    public function _getObjectCollection(
        string $collectionName, 
        string $className, 
        array $params = [],
    ): array
    {
        try {
            $response = $this->get(resource: $collectionName, options: $params);
            $arr = $response->getContent();
            $serializer = $this->getSerializer();
    
            return $serializer->deserialize(
                data: $arr, 
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

    use Api\SportManager;
    use Api\SubscriptionManager;
    use Api\StaffManager;
    use Api\ChurchesManager;

}