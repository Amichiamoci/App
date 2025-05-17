<?php

namespace App\Entity;
use App\Entity\Church\Church;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

    public Church $Church;
    public function getChurch(): Church { return $this->Church; }
    
    public ?UploadedFile $Certificate = null;
    public function hasCertificate(): bool { return $this->Certificate !== null; }
    public function getCertificate(): ?UploadedFile { return $this->Certificate; }
}