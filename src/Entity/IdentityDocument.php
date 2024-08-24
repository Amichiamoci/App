<?php

namespace App\Entity;

class IdentityDocument
{
    public string $Code;
    public function getCode(): string { return $this->Code; }

    public int $TypeId;
    public string $TypeName;
    public function getTypeName(): string { return $this->TypeName; }

    public ?string $Message;
    public function getMessage(): ?string { return $this->Message; }
    public function hasMessage(): bool { return is_string($this->Message) && strlen(trim($this->Message)) > 0; }
}