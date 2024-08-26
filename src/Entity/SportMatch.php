<?php

namespace App\Entity;

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
    public ?MatchFiled $Field = null;
    public function hasField(): bool { return isset($this->Field); }
    public function getField(): ?MatchFiled { return $this->Field; }



    //
    // Teams and results
    //
    public ?Team $HomeTeam;
    public ?string $HomeTeamScore;
    public function hasHomeTeam(): bool { return isset($this->HomeTeam); }
    public function hasHomeTeamScore(): bool { return is_string($this->HomeTeamScore); }
    public function getHomeTeam(): ?Team { return $this->HomeTeam; }
    public function getHomeTeamScore(): ?string { return $this->HomeTeamScore; }


    public ?Team $GuestTeam;
    public ?string $GuestTeamScore;
    public function hasGuestTeam(): bool { return isset($this->GuestTeam); }
    public function hasGuestTeamScore(): bool { return is_string($this->GuestTeamScore); }
    public function getGuestTeam(): ?Team { return $this->GuestTeam; }
    public function getGuestTeamScore(): ?string { return $this->GuestTeamScore; }
}
