<?php

namespace App\Entity\Match;

use App\Entity\Match\ScoreGroup;
use App\Entity\Team\Team;

class SportMatch
{
    public int $Id;
    public function getId(): int { return $this->Id; }

    public int $TourneyId;
    public string $TourneyName;
    public function getTourneyId(): int { return $this->TourneyId; }
    public function getTourneyName(): string { return $this->TourneyName; }

    public int $SportId;
    public string $SportName;
    public function getSportId(): int { return $this->SportId; }
    public function getSportName(): string { return $this->SportName; }

    public ?string $Date;
    public ?string $Time;
    public function hasDate(): bool { return is_string($this->Date); }
    public function getDate(): ?string { return $this->Date; }
    public function hasTime(): bool { return is_string($this->Time); }
    public function getTime(): ?string { return $this->Time; }

    // Field
    public ?MatchField $Field = null;
    public function hasField(): bool { return isset($this->Field); }
    public function getField(): ?MatchField { return $this->Field; }

    //
    // Teams and results
    //
    public ?Team $HomeTeam;
    public function hasHomeTeam(): bool { return isset($this->HomeTeam); }
    public function getHomeTeam(): ?Team { return $this->HomeTeam; }


    public ?Team $GuestTeam;
    public function hasGuestTeam(): bool { return isset($this->GuestTeam); }
    public function getGuestTeam(): ?Team { return $this->GuestTeam; }

    public ScoreGroup $Scores;
    public function hasScores(): bool { return $this->Scores->hasAny(); }
    public function getScores(): ScoreGroup { return $this->Scores; }

    public function canHaveOtherResults(): bool 
    {
        if (
            !str_contains(strtolower($this->SportName), 'pallavolo') &&
            !str_contains(strtolower($this->SportName), 'volley')
        ) {
            return !$this->hasScores();
        }

        return $this->Scores->getCount() < 5;
    }

    public function getScoresRow(): string
    {
        return join('; ', 
            array_map(function(Score $s) {
                return $s->getHome() . " - " . $s->getGuest();
            }, $this->getScores()->getParsed())
        );
    }
}
