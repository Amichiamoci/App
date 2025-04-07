<?php

namespace App\Entity;
use App\Entity\Church\Church;

class Subscription
{
    protected ?int $Id = null;
    public function getId(): ?int { return $this->Id; }
    public function hasId(): bool { return !empty($this->Id); }

    protected int $AnagraphicalId;
    public function getAnagraphicalId(): int { return $this->AnagraphicalId; }
    public function setAnagraphicalId(int $value): self
    {
        $this->AnagraphicalId = $value;
        return $this;
    }

    protected string $Shirt;
    public function getShirt(): string { return $this->Shirt; }
    public function hasShirt(): bool { return strlen(string: $this->Shirt) > 0; }
    public function setShirt(string $value): self
    {
        $this->Shirt = $value;
        return $this;
    }

    protected int $ChurchId;
    public function getChurchId(): ?int { return $this->ChurchId; }
    public function hasChurchId(): bool { return !empty($this->ChurchId); }
    public function setChurchId(int $value): self
    {
        $this->ChurchId = $value;
        return $this;
    }

    public function __construct(
        int $anagraphical,
        string $shirt,
        int|Church $church,
        ?int $id = null,
    ) {
        $this->Id = $id;
        $this->setAnagraphicalId(value: $anagraphical);
        $this->setShirt(value: $shirt);
        $this->setChurchId(value: (/*$church !== null &&*/ $church instanceof Church) ? $church->getId() : $church);
    }
}