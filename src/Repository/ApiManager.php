<?php

namespace App\Repository;

use App\Entity\Anagraphical;
use App\Entity\Church;
use App\Entity\Staff;
use App\Entity\IdentityDocument;
use App\Entity\Event;
use App\Entity\SportMatch;
use App\Entity\Team;
use App\Entity\TeamMember;
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
        return new Serializer([
            new ObjectNormalizer(
                new ClassMetadataFactory(new AttributeLoader()),
                null, 
                null,
                new ReflectionExtractor()
            ), 
            new ArrayDenormalizer(),
            new GetSetMethodNormalizer(), 
            new DateTimeNormalizer()
        ], [
            new JsonEncoder()
        ]);
    }

    private function get(string $resource, $options = []):ResponseInterface
    {
        $headers = [
            'App-Bearer' => self::apiBearer()
        ];
        foreach ($options as $name => $value)
        {
            $headers['Data-Param-' . $name] = $value;
        }
        return $this->client->request(
            'GET', 
            self::apiUrl() . '?resource=' . $resource,
            [ 'headers' => $headers ]
        );
    }

    private function getObjectCollection(string $collectionName, string $className, $params = []): array
    {
        //try {
            $response = $this->get($collectionName, $params);
            $arr = $response->getContent();
            $serializer = $this->getSerializer();
    
            return $serializer->deserialize($arr, $className . "[]", 'json', [
                AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => true,
                AbstractNormalizer::REQUIRE_ALL_PROPERTIES => false,
            ]);
        //}
        //catch (\Exception) {
        //    return array();
        //}
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
        return $this->getObjectCollection('teams-members', TeamMember::class);
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

    /**
     * @param string $email
     * @return Anagraphical[]
     */
    public function ManagedAnagraphicals(string $email): array
    {
        return $this->getObjectCollection('managed-anagraphicals', Anagraphical::class, [
            'Email' => $email
        ]);
    }

    /**
     * @return Staff[]
     */
    public function Staff(): array
    {
        return $this->getObjectCollection('staff-list', Staff::class);
    }

    public function Church(int $id): ?Church
    {
        $churches = $this->getObjectCollection('church', Church::class, [
            'id' => $id
        ]);
        if (count($churches) === 0) {
            return null;
        }
        $church = $churches[0];
        $staff = $this->Staff();
        $church->Staff = array_filter($staff, function (Staff $s) use($church) {
            return $s->ChurchId === $church->Id;
        });
        return $church;
    }
}