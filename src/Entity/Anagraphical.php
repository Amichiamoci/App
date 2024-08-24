<?php

namespace App\Entity;

class Anagraphical
{
    public int $Id;
    public function getId(): int { return $this->Id; }

    public string $Name;
    public string $Surname;
    public function getName(): string { return $this->Name; }
    public function getSurname(): string { return $this->Surname; }

    public string $FiscalCode;
    public function getFiscalCose(): string { return $this->FiscalCode; }

    public ?string $Email;
    public ?string $Phone;
    public function getEmail(): ?string { return $this->Email; }
    public function getPhone(): ?string { return $this->Phone; }
    public function hasEmail(): bool { return is_string($this->Email) && strlen(trim($this->Email)) > 0; }
    public function hasPhone(): bool { return is_string($this->Phone) && strlen(trim($this->Phone)) > 0; }

    public string $BirthDate;
    public function getBirthDate(): string { return $this->BirthDate; }

    public string $MedicalCertificate;
    public string $SubscriptionStatus;
    public ?string $ShirtSize;
    public function getMedicalCertificate(): string { return $this->MedicalCertificate; }
    public function getSubscriptionStatus(): string { return $this->SubscriptionStatus; }
    public function getShirtSize(): ?string { return $this->ShirtSize; }
    public function hasShirtSize(): bool { return is_string($this->ShirtSize); }

    public ?string $Church;
    public ?int $ChurchId;
    public function getChurch(): ?string { return $this->Church; }
    public function getChurchId(): ?int { return $this->ChurchId; }
    public function hasChurch(): bool { return is_string($this->Church) && isset($this->ChurchId) && $this->ChurchId !== 0; }
    public function hasChurchId(): bool { return $this->hasChurch(); }

    public IdentityDocument $Document;
    public function getDocument(): IdentityDocument { return $this->Document; }
}