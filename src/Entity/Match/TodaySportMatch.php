<?php

namespace App\Entity\Match;

class TodaySportMatch extends SportMatch
{
    public string $WhoPlays;
    public function getWhoPlays(): string { return $this->WhoPlays; }
}