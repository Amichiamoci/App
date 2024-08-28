<?php

namespace App\Entity\Match;

class Score
{
    public int $Id;
    public function getId(): int { return $this->Id; }
    public string $Home;
    public function getHome(): string { return $this->Home; }
    public string $Guest;
    public function getGuest(): string { return $this->Guest; }

    public function __construct(int $id, string $home, string $guest)
    {
        $this->Id = $id;
        $this->Home = $home;
        $this->Guest = $guest;
    }
}