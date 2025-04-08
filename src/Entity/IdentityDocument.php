<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

class IdentityDocument
{
    public string $Code;
    public function getCode(): string { return $this->Code; }

    #[Assert\NotEqualTo(value: 0)]
    public int $TypeId;
    public string $TypeName;
    public function getTypeName(): string { return $this->TypeName; }

    public ?string $Message;
    public function getMessage(): ?string { return $this->Message; }
    public function hasMessage(): bool { return is_string(value: $this->Message) && strlen(string: trim(string: $this->Message)) > 0; }
}