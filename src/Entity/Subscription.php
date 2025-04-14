<?php

namespace App\Entity;
use App\Entity\Church\Church;
use Symfony\Component\Validator\Constraints as Assert;

class Subscription
{
    #[Assert\NotEqualTo(value: 0)]
    public ?int $Id = null;
    public function getId(): ?int { return $this->Id; }
    public function hasId(): bool { return !empty($this->Id); }

    #[Assert\NotBlank]
    public string $Shirt;
    public function getShirt(): string { return $this->Shirt; }
    public function hasShirt(): bool { return strlen(string: $this->Shirt) > 0; }
    public function setShirt(string $value): self
    {
        $this->Shirt = $value;
        return $this;
    }

    #[Assert\NotEqualTo(value: 0)]
    #[Assert\NotBlank]
    public int $ChurchId;
    public function getChurchId(): ?int { return $this->ChurchId; }
    public function hasChurchId(): bool { return !empty($this->ChurchId); }
    public function setChurchId(int $value): self
    {
        $this->ChurchId = $value;
        return $this;
    }
}