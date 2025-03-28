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
use App\Entity\Tournament;
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
                new DateTimeNormalizer()
            ], 
            encoders: [
                new JsonEncoder()
            ]
        );
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
            method: 'POST', 
            url: self::apiUrl() . '?resource=' . $resource,
            options: [ 'headers' => $headers ]
        );
    }

    private function getObjectCollection(string $collectionName, string $className, $params = []): array
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
                ]
            );
        }
        catch (\Throwable) {
            return [];
        }
    }

    /**
     * @param string $sport
     * @return SportMatch[]
     */
    public function Matches(string $sport): array
    {
        return $this->getObjectCollection(
            collectionName: 'today-matches-sport', 
            className: SportMatch::class, 
            params: [
                'Sport' => $sport,
            ]
        );
    }

    /**
     * @return TeamMember[]
     */
    public function TeamMembers(): array
    {
        return $this->getObjectCollection(collectionName: 'teams-members', className: TeamMember::class);
    }

    /**
     * @return Team[]
     */
    public function Teams(): array
    {
        // Load form API
        $members = $this->TeamMembers();
        $teams = $this->getObjectCollection(collectionName: 'teams-info', className: Team::class);

        // Join collections
        foreach ($teams as &$team)
        {
            $team->Members = array_filter(array: $members, callback: function (TeamMember $m) use($team): bool {
                return $m->TeamId === $team->Id;
            });
        }
        unset($team);

        // Return
        return $teams;
    }

    public function Team(int $id): ?Team
    {
        $filtered = array_filter(array: $this->Teams(), callback: function (Team $t) use($id): bool {
            return $t->Id === $id;
        });
        if (count(value: $filtered) === 0)
        {
            return null;
        }
        return array_values(array: $filtered)[0];
    }

    /**
     * @param string $email
     * @return Anagraphical[]
     */
    public function ManagedAnagraphicals(string $email): array
    {
        return $this->getObjectCollection(
            collectionName: 'managed-anagraphicals', 
            className: Anagraphical::class,
            params: [
                'Email' => $email
            ]
        );
    }

    /**
     * @return Staff[]
     */
    public function Staff(): array
    {
        return $this->getObjectCollection(collectionName: 'staff-list', className: Staff::class);
    }

    public function Church(int $id): ?Church
    {
        $churches = $this->getObjectCollection(
            collectionName: 'church', 
            className: Church::class, 
            params: [
                'id' => $id
            ]
        );
        if (count(value: $churches) === 0) {
            return null;
        }
        $church = $churches[0];
        $staff = $this->Staff();
        $church->Staff = array_filter(array: $staff, callback: function (Staff $s) use($church): bool {
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
            throw new \InvalidArgumentException(message: 'Given email was empty!');
        }

        return $this->getObjectCollection(
            collectionName: 'today-matches-of',
            className: TodaySportMatch::class, 
            params: [
                'Email' => $email,
            ]
        );
    }

    public function Tournament(int $id): ?Tournament
    {
        $t = $this->getObjectCollection(collectionName: 'tournament', className: Tournament::class, params: [
            'Id' => $id,
        ]);
        if (count(value: $t) === 0)
        {
            return null;
        }

        $tourney = array_values(array: $t)[0];

        $tourney->Matches = $this->getObjectCollection(
            collectionName: 'tournament-matches', 
            className: SportMatch::class, 
            params: [
                'Id' => $id
            ]
        );

        $tourney->Leaderboard = $this->getObjectCollection(
            collectionName: 'tournament-leaderboard', 
            className: TeamPosition::class, 
            params: [
                'Id' => $id
            ]
        );

        return $tourney;
    }

    public function TournamentFromSport(string $sport): array
    {
        return $this->getObjectCollection(
            collectionName: 'tourney-sport', 
            className: Tournament::class, 
            params: [
                'Sport' => $sport
            ]
        );
    }

    public function TodayAndYesterdayMatches(): array
    {
        $array = $this->getObjectCollection(
            collectionName: 'today-yesterday-matches', 
            className: SportMatch::class
        );
        if (count(value: $array) === 0)
        {
            return [];
        }

        $keys = array_values(array: array_unique(array: array_map(callback: function (SportMatch $m): string {
            return $m->SportName;
        }, array: $array)));

        $finalArray = [];
        foreach ($keys as $key)
        {
            $finalArray[$key] = array_filter(array: $array, callback: function (SportMatch $m) use($key): bool {
                return $m->SportName === $key;
            });
        }
        return $finalArray;
    }

    public function DeleteResult(int $id): bool
    {
        $result = $this->getObjectCollection(
            collectionName: 'delete-match-result', 
            className: 'string', 
            params: [
                'Id' => $id,
            ]
        );
        return count(value: $result) === 0;
    }

    public function AddResult(int $id, string $home, string $guest): ?Score
    {
        $scores = $this->getObjectCollection(collectionName: 'new-match-result', className: Score::class, params: [
            'Id' => $id,
            'Home' => $home,
            'Guest' => $guest,
        ]);
        if (count(value: $scores) === 0)
        {
            return null;
        }

        return array_values(array: $scores)[0];
    }

    public function Leaderboard(): array
    {
        return $this->getObjectCollection(collectionName: 'leaderboard', className: ChurchScore::class);
    }
}