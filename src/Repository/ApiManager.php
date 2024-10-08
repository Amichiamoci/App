<?php

namespace App\Repository;

use App\Entity\Anagraphical;
use App\Entity\Staff;
use App\Entity\Tourney;
use App\Entity\Church\Church;
use App\Entity\Church\ChurchScore;
use App\Entity\Match\SportMatch;
use App\Entity\Match\TodaySportMatch;
use App\Entity\Match\Score;
use App\Entity\Match\ScoreGroup;
use App\Entity\Team\Team;
use App\Entity\Team\TeamMember;
use App\Entity\Team\TeamPosition;

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
        try {
            $response = $this->get($collectionName, $params);
            $arr = $response->getContent();
            $serializer = $this->getSerializer();
    
            return $serializer->deserialize($arr, $className . "[]", 'json', [
                AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => true,
                AbstractNormalizer::REQUIRE_ALL_PROPERTIES => false,
            ]);
        }
        catch (\Exception) {
            return array();
        }
    }

    /**
     * @param string $sport
     * @return SportMatch[]
     */
    public function Matches(string $sport): array
    {
        return $this->getObjectCollection('today-matches-sport', SportMatch::class, [
            'Sport' => $sport,
        ]);
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
        return array_values($filtered)[0];
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

    /**
     * @param string $email
     * @return TodaySportMatch[]
     */
    public function TodayMatchesOfUser(string $email): array
    {
        if (empty($email))
        {
            throw new \InvalidArgumentException('Given email was empty!');
        }

        return $this->getObjectCollection('today-matches-of', TodaySportMatch::class, [
            'Email' => $email,
        ]);
    }

    public function Tourney(int $id): ?Tourney
    {
        $t = $this->getObjectCollection('tourney', Tourney::class, [
            'Id' => $id,
        ]);
        if (count($t) === 0)
        {
            return null;
        }

        $tourney = array_values($t)[0];

        $tourney->Matches = $this->getObjectCollection('tourney-matches', SportMatch::class, [
            'Id' => $id
        ]);

        $tourney->Leaderboard = $this->getObjectCollection('tourney-leaderboard', TeamPosition::class, [
            'Id' => $id
        ]);

        return $tourney;
    }

    public function TourneyFromSport(string $sport): array
    {
        return $this->getObjectCollection('tourney-sport', Tourney::class, [
            'Sport' => $sport
        ]);
    }

    public function TodayAndYesterdayMatches(): array
    {
        $array = $this->getObjectCollection('today-yesterday-matches', SportMatch::class);
        if (count($array) === 0)
        {
            return [];
        }

        $keys = array_values(array_unique(array_map(function(SportMatch $m){
            return $m->SportName;
        }, $array)));

        $finalArray = [];
        foreach ($keys as $key)
        {
            $finalArray[$key] = array_filter($array, function(SportMatch $m) use($key) {
                return $m->SportName === $key;
            });
        }
        return $finalArray;
    }

    public function DeleteResult(int $id): bool
    {
        $result = $this->getObjectCollection('delete-match-result', 'string', [
            'Id' => $id,
        ]);
        return count($result) === 0;
    }

    public function AddResult(int $id, string $home, string $guest) : ?Score
    {
        $scores = $this->getObjectCollection('new-match-result', Score::class, [
            'Id' => $id,
            'Home' => $home,
            'Guest' => $guest,
        ]);
        if (count($scores) === 0)
        {
            return null;
        }

        return array_values($scores)[0];
    }

    public function Leaderboard(): array
    {
        return $this->getObjectCollection('leaderboard', ChurchScore::class);
    }
}