<?php

namespace App\Entity\Church;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

class Church
{
    #[Assert\NotEqualTo(value: 0)]
    public int $Id;
    public function getId(): int { return $this->Id; }
    public function hasId(): bool { return !empty($this->Id); }
    public function setId(int $value): self
    {
        $this->Id = $value;
        return $this;
    }
    
    #[Assert\NotBlank]
    public string $Name;
    public function getName(): string { return $this->Name; }
    public function hasName(): bool { return strlen(string: $this->Name) > 0; }
    public function setName(string $value): self
    {
        $this->Name = $value;
        return $this;
    }

    public ?string $Address;
    public ?string $Website;
    public function getAddress(): ?string { return $this->Address; }
    public function hasAddress(): bool { return is_string(value: $this->Address) && strlen(string: $this->Address) > 0; }
    public function getWebsite(): ?string { return $this->Website; }
    public function hasWebsite(): bool { return is_string(value: $this->Website) && strlen(string: $this->Website) > 0; }

    
    #[Ignore]
    public array $Staff = [];
    public function getStaff(): array { return $this->Staff; }
    public function hasStaff(): bool { return count(value: $this->Staff) > 0; }
}