<?php

namespace App\Entity;

use DateTime;

class TodaySaint
{
    public function __construct(
        string $Name,
        string $Typology,
        string $Default,
        ?string $Description = null,
        ?string $Link = null,
        ?string $Image = null,
    ) {
        $this->Name = $Name;
        $this->Typology = $Typology;
        $this->IsDefault = ((int)$Default) === 1;

        $this->Description = $Description;
        $this->Link = $Link;
        $this->Image = $Image;
    }

    private string $Name;
    private string $Typology;
    private bool $IsDefault = false;
    public function getName(): string { return $this->Name; }
    public function getTypology(): string { return $this->Typology; }
    public function getIsDefault(): bool { return $this->IsDefault; }

    private ?string $Description = null;
    public function getDescription(): ?string { return $this->Description; }
    public function hasDescription(): bool { return is_string($this->Description); }

    private ?string $Link = null;
    public function getLink(): ?string { return $this->Link; }
    public function hasLink(): bool { return is_string($this->Link); }

    
    private ?string $Image = null;
    public function getImage(): ?string { return $this->Image; }
    public function hasImage(): bool { return is_string($this->Image); }
}