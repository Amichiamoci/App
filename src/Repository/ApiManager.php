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
    // Private variables
    //
    private Serializer $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

    //
    // Methods
    //

    public static function apiUrl(): string
    {
        return $_ENV["API_URL_BASE"] ?? "";
    }

    private static function apiBearer(): string
    {
        return $_ENV["API_BEARER"] ?? "";
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


    public function Matches(): array
    {
        $response = $this->get('matches');
        $arr = $response->toArray();
        $return_arr = array();
        foreach ($arr as $obj)
        {
            $return_arr[] = self::$serializer->deserialize($obj, SportMatch::class, 'json');;
        }
        return $return_arr;
    }

    public function Events(): array
    {
        $response = $this->get('events');
        $arr = $response->toArray();
        $return_arr = array();
        foreach ($arr as $obj)
        {
            $return_arr[] = self::$serializer->deserialize($obj, Event::class, 'json');;
        }
        return $return_arr;
    }
}