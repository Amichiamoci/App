<?php

namespace App\Entity;

use Symfony\UX\Map\Map;
use Symfony\UX\Map\Marker;
use Symfony\UX\Map\Point;

class MatchFiled
{
    public int $Id;
    public string $Name;
    public function getId(): int { return $this->Id; }
    public function getName(): string { return $this->Name; }

    public ?string $Address;
    public function getAddress(): ?string { return $this->Address; }
    public function hasAddress(): bool { return is_string($this->Address); }

    public ?float $Latitude;
    public ?float $Longitude;
    public function hasLatitude(): bool
    {
        return isset($this->Latitude) && isset($this->Longitude);
    }
    public function hasLongitude(): bool { return $this->hasLatitude(); }

    public function getLatitude(): ?float { return $this->Latitude; }
    public function getLongitude(): ?float { return $this->Longitude; }

    public function getMap(): ?Map
    {
        if (!$this->hasLatitude())
        {
            return null;
        }
        $point = new Point($this->getLatitude(), $this->getLongitude());
        return (new Map())
            ->addMarker(new Marker(
                position: $point, 
                title: $this->getName()
            ))
            
            ->center($point)
            ->zoom(10)
            ->fitBoundsToMarkers();
    }
}