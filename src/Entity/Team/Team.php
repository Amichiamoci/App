<?php

namespace App\Entity\Team;
use Symfony\Component\Serializer\Annotation\Ignore;

class Team
{
    public string $Name;
    public int $Id;
    public function getName(): string { return $this->Name; }
    public function getId(): int { return $this->Id; }

    public string $Sport;
    public int $SportId;
    public function getSport(): string { return $this->Sport; }
    public function getSportId(): int { return $this->SportId; }

    public string $Church;
    public int $ChurchId;
    public function getChurch(): string { return $this->Church; }
    public function getChurchId(): int { return $this->ChurchId; }

    #[Ignore]
    public array $Members = [];
    public function getMembers(): array { return $this->Members; }
}