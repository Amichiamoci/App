<?php

namespace App\Entity;

class TeamMember
{
    public string $FullName;
    public int $Id;
    public function getFullName(): string { return $this->FullName; }
    public function getId(): int { return $this->Id; }

    public int $TeamId;
    public function getTeamId(): int { return $this->TeamId; }

    public string $Sex = '?';
    public function getSex(): string { return $this->Sex; }
    
    public array $Problems = [];
    public function hasProblems(): bool { return count($this->Problems) > 0; }
    public function getProblems(): array { return $this->Problems; }
    public function getProblemsList(): string { return join(' ', $this->getProblems()); }
}