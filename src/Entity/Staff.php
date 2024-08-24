<?php

namespace App\Entity;

class Staff
{
    public string $Name;
    public function getName(): string { return $this->Name; }

    public int $ChurchId;
    public function getChurchId() : int { return $this->ChurchId; }

    public ?string $Email;
    public ?string $Phone;
    public function getEmail(): ?string { return $this->Email; }
    public function getPhone(): ?string { return $this->Phone; }
    public function hasEmail(): bool { return is_string($this->Email) && strlen(trim($this->Email)) > 0; }
    public function hasPhone(): bool { return is_string($this->Phone) && strlen(trim($this->Phone)) > 0; }
}