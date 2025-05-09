<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

class IdentityDocument
{
    #[Assert\NotBlank]
    public string $Code;
    public function getCode(): string { return $this->Code; }

    #[Assert\NotBlank]
    public \DateTimeInterface $Expiration;
    public function getExpiration(): \DateTimeInterface { return $this->Expiration; }
    public function getExpirationItalian(): string {
        if (empty($this->Expiration))
        {
            return '';
        }
        
        return $this->Expiration->format(format: 'd/m/Y');
    }
    public IdentityDocumentType $Type;
}