<?php

namespace App\Entity;
use Symfony\Component\Serializer\Annotation\Ignore;

class Tourney
{
    public int $Id;
    public string $Name;
    public function getId(): int { return $this->Id; }
    public function getName(): string { return $this->Name; }

    public string $Sport;
    public int $SportId;
    public function getSport(): string { return $this->Sport; }
    public function getSportId(): int { return $this->SportId; }

    public string $Type;
    public function getType(): string { return $this->Type; }

    #[Ignore]
    public array $Matches = [];
    public function getMatches(): array { return $this->Matches; }
    public function hasMatches(): bool { return count($this->Matches) > 0; }

    public array $Teams = [];
    public function getTeams(): array { return $this->Teams; }
    public function hasTeams(): bool { return count($this->Teams) > 0; }

    #[Ignore]
    public array $Leaderboard = [];
    public function getLeaderboard(): array { return $this->Leaderboard; }
    public function hasLeaderboard(): bool { return count($this->Leaderboard) > 0; }

}