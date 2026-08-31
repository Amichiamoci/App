<?php

namespace App\Entity\Team;

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


    public ?string $Coach = null;
    public function getCoach(): ?string { return $this->Coach; }
    public function hasCoach(): bool { return !empty($this->Coach); }

    public function CoachSplit(): array
    {
        if (!$this->hasCoach())
        {
            return [];
        }

        return array_filter(
            array: array_map(
                callback: function (string $s): string { return trim(string: $s); },
                array: explode(
                    separator: ',', 
                    string: str_replace(search: ["\n", "\r", "\t", ";", ], replace: ',', subject: $this->Coach),
                ),
            ),
            callback: function (string $s): bool { return strlen(string: $s) > 0; },
        );
    }
}