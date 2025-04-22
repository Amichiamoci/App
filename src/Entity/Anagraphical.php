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

    #[Assert\Regex(pattern: '/[a-zA-Z]{6}[0-9]{2}[a-zA-Z][0-9]{2}[a-zA-Z][0-9]{3}[a-zA-Z]/')]
    #[Assert\NotBlank]
    public string $TaxCode;
    public function getTaxCode(): string { return $this->TaxCode; }
    
    #[Assert\Email]
    public ?string $Email;
    public ?string $Phone;
    public function getEmail(): ?string { return $this->Email; }
    public function getPhone(): ?string { return $this->Phone; }
    public function hasEmail(): bool { return is_string(value: $this->Email) && strlen(string: trim(string: $this->Email)) > 0; }
    public function hasPhone(): bool { return is_string(value: $this->Phone) && strlen(string: trim(string: $this->Phone)) > 0; }

    public ?string $BirthDate = null;
    public function hasBirtDate(): bool { return is_string(value: $this->BirthDate) && strlen(string: trim(string: $this->BirthDate)) > 0; }
    public function getBirthDate(): ?string { return $this->BirthDate; }

    public ?string $BirthPlace = null;
    public function hasBirtPlace(): bool { return !empty($this->BirthPlace); }
    public function getBirtPlace(): ?string { return $this->BirthPlace; }

    public IdentityDocument $Document;
    public function getDocument(): IdentityDocument { return $this->Document; }

    public ?Subscription $Subscription = null;
    public function hasSubscription(): bool { return isset($this->Subscription); }
    public function getSubscription(): ?Subscription { return $this->Subscription; }

    public array $Status = [];
    public function hasStatus(): bool { return count(value: $this->Status) > 0; }
    public function getStatus(): array { return $this->Status; }

}