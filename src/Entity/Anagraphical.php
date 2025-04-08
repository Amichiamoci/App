<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

class Anagraphical
{
    #[Assert\NotEqualTo(value: 0)]
    public int $Id;
    public function getId(): int { return $this->Id; }

    #[Assert\NotBlank]
    public string $Name;
    #[Assert\NotBlank]
    public string $Surname;
    public function getName(): string { return $this->Name; }
    public function getSurname(): string { return $this->Surname; }

    public string $TaxCode;
    public function getTaxCode(): string { return $this->TaxCode; }

    public ?string $Email;
    public ?string $Phone;
    public function getEmail(): ?string { return $this->Email; }
    public function getPhone(): ?string { return $this->Phone; }
    public function hasEmail(): bool { return is_string(value: $this->Email) && strlen(string: trim(string: $this->Email)) > 0; }
    public function hasPhone(): bool { return is_string(value: $this->Phone) && strlen(string: trim(string: $this->Phone)) > 0; }

    public string $BirthDate;
    public function getBirthDate(): string { return $this->BirthDate; }

    public string $MedicalCertificate;
    public string $SubscriptionStatus;
    public ?string $ShirtSize;
    public function getMedicalCertificate(): string { return $this->MedicalCertificate; }
    public function getSubscriptionStatus(): string { return $this->SubscriptionStatus; }
    public function getShirtSize(): ?string { return $this->ShirtSize; }
    public function hasShirtSize(): bool { return is_string(value: $this->ShirtSize); }

    public ?string $Church;
    #[Assert\NotEqualTo(value: 0)]
    public ?int $ChurchId;
    public function getChurch(): ?string { return $this->Church; }
    public function getChurchId(): ?int { return $this->ChurchId; }
    public function hasChurch(): bool { return is_string(value: $this->Church) && isset($this->ChurchId) && $this->ChurchId !== 0; }
    public function hasChurchId(): bool { return $this->hasChurch(); }

    public IdentityDocument $Document;
    public function getDocument(): IdentityDocument { return $this->Document; }
}