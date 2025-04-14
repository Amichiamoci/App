<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

class IdentityDocumentType
{
    #[Assert\NotEqualTo(value: 0)]
    #[Assert\NotBlank]
    public int $Id;
    public function hasTypeId(): bool { return $this->Id !== 0; }
    public function getTypeId(): int { return $this->Id; }

    public ?string $Label = null;
    public function hasTypeName(): bool { return isset($this->Label); }
    public function getTypeName(): ?string { return $this->Label; }
}