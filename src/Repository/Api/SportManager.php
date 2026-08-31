<?php

namespace App\Repository\Api;

use App\Entity\Match\MatchField;
use App\Entity\Match\SportMatch;
use App\Entity\Match\TodaySportMatch;
use App\Entity\Team\Team;
use App\Entity\Team\TeamMember;
use App\Entity\Team\TeamPosition;
use App\Entity\Tournament;

trait SportManager
{
    //
    // Teams handling
    //

    /**
     * @return Team[]
     */
    public function Teams(): array
    {
        return $this->_getObjectCollection(collectionName: 'teams-info', className: Team::class);
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

    //
    // Matches handling
    //

    /**
     * Duration of the cache, set to 5 minutes
     * @var int
     */
    const int MATCHES_CACHE = 300;
    
    /**
     * @param string $sport
     * @return SportMatch[]
     */
    public function Matches(string $sport): array
    {
        return $this->_getObjectCollection(
            collectionName: 'today-matches-sport', 
            className: SportMatch::class, 
            params: [
                'Sport' => $sport,
            ],
            cache: self::MATCHES_CACHE / 2,
        );
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

        return $this->_getObjectCollection(
            collectionName: 'today-matches-of',
            className: TodaySportMatch::class, 
            params: [
                'Email' => $email,
            ],
        );
    }

    public function Tournament(int $id): ?Tournament
    {
        $t = $this->_getObjectCollection(
            collectionName: 'tournament', 
            className: Tournament::class, 
            params: [
                'Id' => $id,
            ],
        );
        if (count(value: $t) === 0)
        {
            return null;
        }

        $tourney = array_values(array: $t)[0];

        $tourney->Matches = $this->_getObjectCollection(
            collectionName: 'tournament-matches', 
            className: SportMatch::class, 
            params: [
                'Id' => $id
            ],
            cache: self::MATCHES_CACHE / 2,
        );

        $tourney->Leaderboard = $this->_getObjectCollection(
            collectionName: 'tournament-leaderboard', 
            className: TeamPosition::class, 
            params: [
                'Id' => $id
            ],
            cache: self::MATCHES_CACHE / 2,
        );

        return $tourney;
    }

    public function TournamentFromSport(string $sport): array
    {
        return $this->_getObjectCollection(
            collectionName: 'tournament-sport', 
            className: Tournament::class, 
            params: [
                'Sport' => $sport
            ]
        );
    }

    //
    // Fields handling
    //
    
    /**
     * @return MatchField[]
     */
    public function Fields(): array
    {
        return $this->_getObjectCollection(
            collectionName: 'all-fields', 
            className: MatchField::class, 
        );
    }

    public function Field(int $id): ?MatchField
    {
        $filtered = array_filter(
            array: $this->Fields(), 
            callback: function (MatchField $f) use($id): bool {
                return $f->Id === $id;
            },
        );
        if (count(value: $filtered) === 0)
        {
            return null;
        }
        return array_values(array: $filtered)[0];
    }
}