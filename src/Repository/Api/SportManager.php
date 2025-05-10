<?php

namespace App\Repository\Api;
use App\Entity\Match\SportMatch;
use App\Entity\Match\TodaySportMatch;
use App\Entity\Match\Score;
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
     * @return TeamMember[]
     */
    public function TeamMembers(): array
    {
        return $this->_getObjectCollection(
            collectionName: 'teams-members', 
            className: TeamMember::class,
        );
    }

    /**
     * @return Team[]
     */
    public function Teams(): array
    {
        // Load form API
        $members = $this->TeamMembers();
        $teams = $this->_getObjectCollection(collectionName: 'teams-info', className: Team::class);

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

    //
    // Matches handling
    //
    
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
            ]
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
            ]
        );

        $tourney->Leaderboard = $this->_getObjectCollection(
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
        return $this->_getObjectCollection(
            collectionName: 'tournament-sport', 
            className: Tournament::class, 
            params: [
                'Sport' => $sport
            ]
        );
    }

    public function TodayAndYesterdayMatches(): array
    {
        $array = $this->_getObjectCollection(
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

    //
    // Results handling
    //

    public function DeleteResult(int $id): bool
    {
        $result = $this->_getObjectCollection(
            collectionName: 'delete-match-result', 
            className: 'string', 
            params: [
                'Id' => $id,
            ],
            cache: false,
        );
        return count(value: $result) === 0;
    }

    public function AddResult(int $id, string $home, string $guest): ?Score
    {
        $scores = $this->_getObjectCollection(
            collectionName: 'new-match-result', 
            className: Score::class, 
            params: [
                'Id' => $id,
                'Home' => $home,
                'Guest' => $guest,
            ],
            cache: false,
        );
        if (count(value: $scores) === 0)
        {
            return null;
        }

        return array_values(array: $scores)[0];
    }

}