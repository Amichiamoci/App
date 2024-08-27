<?php

namespace App\Repository;

use App\Entity\TodaySaint;
use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TodaySaintManager
{
    public function __construct(
        private HttpClientInterface $client,
    ) {  }

    private const URL = 'https://www.santodelgiorno.it/santi.json';

    public function Get(): array
    {
        try {
            $response = $this->client->request('GET', self::URL);
            $array = $response->toArray();
            return array_map(function($r): TodaySaint
            {
                return new TodaySaint(
                    $r['nome'],
                    $r['tipologia'],
                    $r['default'],
                    Description: array_key_exists('descrizione', $r) ?
                        $r['descrizione'] : null,
                    Link: array_key_exists('permalink', $r) ?
                        $r['permalink'] : null,
                    Image: array_key_exists('urlimmagine', $r) ?
                        $r['urlimmagine'] : null,
                );
            }, $array);
        } catch (Exception) {
            return [];
        }
    }

    public function Default(): ?TodaySaint
    {
        $defaults = array_filter($this->Get(), function(TodaySaint $s) {
            return $s->getIsDefault();
        });
        if (count($defaults) === 0)
        {
            return null;
        }
        return array_values($defaults)[0];
    }
}