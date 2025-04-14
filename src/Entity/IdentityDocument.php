<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

class IdentityDocument
{
    #[Assert\NotBlank]
    public string $Code;
    public function getCode(): string { return $this->Code; }
    public IdentityDocumentType $Type;
}