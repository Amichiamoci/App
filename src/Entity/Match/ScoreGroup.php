<?php

namespace App\Entity\Match;

class ScoreGroup
{
    public array $Id = [];
    public function getId(): array { return $this->Id; }
    public array $Home = [];
    public function getHome(): array { return $this->Home; }
    public array $Guest = [];
    public function getGuest(): array { return $this->Guest; }

    public function getParsed(): array {
        $length = $this->getCount();

        $arr = [];
        for ($i = 0; $i < $length; $i++)
        {
            $score = new Score();
            $score->Id = (int)$this->Id[$i];
            $score->Home = $this->Home[$i];
            $score->Guest = $this->Guest[$i];

            $arr[] = $score;
        }
        return $arr;
    }

    public function getCount(): int { return min(count($this->Id), count($this->Home), count($this->Guest)); }

    public function hasAny(): bool { return $this->getCount() > 0; }
}