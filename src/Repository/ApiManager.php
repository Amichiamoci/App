<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\SportMatch;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ApiManager
{
    public function __construct(
        private HttpClientInterface $client,
    ) {  }

    //
    // Methods
    //

    public static function apiUrl(): string
    {
        return $_ENV["API_URL"] ?? "";
    }

    private static function apiBearer(): string
    {
        return $_ENV["APP_SECRET"] ?? "";
    }
    private static function getSerializer(): Serializer
    {
        return new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
    }

    private function get(string $resource):ResponseInterface
    {
        return $this->client->request(
            'GET', 
            self::apiUrl() . '/' . $resource,
            [
                'headers' => [
                    'App-Bearer-Token' => self::apiBearer()
                ]
        ]);
    }

    private function getObjectCollection(string $collectionName, string $className): array
    {
        $response = $this->get($collectionName);
        $arr = $response->toArray();
        $serializer = $this->getSerializer();

        $return_arr = array();
        foreach ($arr as $obj)
        {
            $return_arr[] = $serializer->deserialize($obj, $className, 'json');
        }
        return $return_arr;
    }

    public function Matches(): array
    {
        return $this->getObjectCollection('matches', SportMatch::class);
    }

    public function Events(): array
    {
        return $this->getObjectCollection('events', Event::class);
    }
}