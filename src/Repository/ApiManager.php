<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\SportMatch;
use App\Entity\Team;
use App\Entity\TeamMember;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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
            self::apiUrl() . '?resource=' . $resource,
            [
                'headers' => [
                    'App-Bearer-Token' => self::apiBearer()
                ]
            ]);
    }

    private function getObjectCollection(string $collectionName, string $className): array
    {
        try {
            $response = $this->get($collectionName);
            $arr = $response->toArray();
            $serializer = $this->getSerializer();
    
            $return_arr = array();
            foreach ($arr as $obj)
            {
                $return_arr[] = $serializer->deserialize($obj, $className, 'json', [
                    AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => true,
                    AbstractNormalizer::REQUIRE_ALL_PROPERTIES => false
                ]);
            }
            return $return_arr;
        }
        catch (\Exception) {
            return array();
        }
    }

    public function Matches(): array
    {
        return $this->getObjectCollection('matches', SportMatch::class);
    }

    /**
     * @return Event[]
     */
    public function Events(): array
    {
        return $this->getObjectCollection('events', Event::class);
    }

    /**
     * @return TeamMember[]
     */
    public function TeamMembers(): array
    {
        return $this->getObjectCollection('distinte', TeamMember::class);
    }

    /**
     * @return Team[]
     */
    public function Teams(): array
    {
        // Load form API
        $members = $this->TeamMembers();
        $teams = $this->getObjectCollection('teams-info', Team::class);

        // Join collections
        foreach ($teams as &$team)
        {
            $team->Members = array_filter($members, function (TeamMember $m) use($team) {
                return $m->TeamId === $team->Id;
            });
        }
        unset($team);

        // Return
        return $teams;
    }

    public function Team(int $id): ?Team
    {
        $filtered = array_filter($this->Teams(), function(Team $t) use($id) {
            return $t->Id === $id;
        });
        if (count($filtered) === 0)
        {
            return null;
        }
        return $filtered[0];
    }
}