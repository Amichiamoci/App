<?php

namespace App\Entity\Church;
use Symfony\Component\Serializer\Annotation\Ignore;

class Church
{
    public int $Id;
    public string $Name;
    public function getId(): int { return $this->Id; }
    public function getName(): string { return $this->Name; }

    public ?string $Address;
    public ?string $Website;
    public function getAddress(): ?string { return $this->Address; }
    public function hasAddress(): bool { return is_string($this->Address) && strlen($this->Address) > 0; }
    public function getWebsite(): ?string { return $this->Website; }
    public function hasWebsite(): bool { return is_string($this->Website) && strlen($this->Website) > 0; }

    
    #[Ignore]
    public array $Staff = [];
    public function getStaff(): array { return $this->Staff; }
    public function hasStaff(): bool { return count($this->Staff) > 0; }
}