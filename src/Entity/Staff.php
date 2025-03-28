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
    public function hasEmail(): bool { return is_string(value: $this->Email) && strlen(string: trim(string: $this->Email)) > 0; }
    public function hasPhone(): bool { return is_string(value: $this->Phone) && strlen(string: trim(string: $this->Phone)) > 0; }
}