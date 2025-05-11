<?php

namespace App\Repository;

use App\Entity\TodaySaint;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class TodaySaintManager
{
    public function __construct(
        private HttpClientInterface $client,
        private CacheInterface $cache,
    ) {  }

    private const URL = 'https://www.santodelgiorno.it/santi.json';

    private function loadFromSource(): array
    {
        try {
            $response = $this->client->request(method: 'GET', url: self::URL);
            $array = $response->toArray();
            return array_map(callback: function (array $r): TodaySaint
            {
                return new TodaySaint(
                    Name: $r['nome'],
                    Typology: $r['tipologia'],
                    Default: $r['default'],
                    Description: array_key_exists(key: 'descrizione', array: $r) ?
                        $r['descrizione'] : null,
                    Link: array_key_exists(key: 'permalink', array: $r) ?
                        $r['permalink'] : null,
                    Image: array_key_exists(key: 'urlimmagine', array: $r) ?
                        $r['urlimmagine'] : null,
                );
            }, array: $array);
        } catch (\Throwable) {
            return [];
        }
    }

    public function Get(): array
    {
        return $this->cache->get(
            key: 'saint-of-the-day-' . date(format: 'Y-m-d'), 
            callback: function (ItemInterface $item): array {
                $item->expiresAfter(time: 24 * 3600); // 1 day 
                return $this->loadFromSource();
            },
        );
    }

    public function Default(): ?TodaySaint
    {
        $defaults = array_filter(array: $this->Get(), callback: function (TodaySaint $s): bool {
            return $s->getIsDefault();
        });
        if (count(value: $defaults) === 0)
        {
            return null;
        }
        return array_values(array: $defaults)[0];
    }
}