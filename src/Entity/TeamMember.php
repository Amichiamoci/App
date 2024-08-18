<?php

namespace App\Entity;

class TeamMember
{
    public string $FullName;
    public int $Id;
    public int $TeamId;

    public string $Sex = '?';
    public array $Problems = [];

    public function hasProblems(): bool
    {
        return count($this->Problems) > 0;
    }
}