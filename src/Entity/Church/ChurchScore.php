<?php

namespace App\Entity\Church;

class ChurchScore extends Church
{
    public int $Score;
    public int $Position;
    public function getScore(): int { return $this->Score; }
    public function getPosition(): int { return $this->Position; }
}