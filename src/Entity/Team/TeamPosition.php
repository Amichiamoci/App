<?php

namespace App\Entity\Team;

class TeamPosition extends Team
{
    public ?int $Points;
    public ?int $MatchesToPlay;
    public ?int $PlannedMatches;
    public function hasPoints(): bool { return isset($this->Points); }
    public function hasMatchesToPlay(): bool { return isset($this->MatchesToPlay); }
    public function hasPlannedMatches(): bool { return isset($this->PlannedMatches); }
    public function getPoints(): ?int { return $this->Points; }
    public function getMatchesToPlay(): ?int { return $this->MatchesToPlay; }
    public function getPlannedMatches(): ?int { return $this->PlannedMatches; }
}