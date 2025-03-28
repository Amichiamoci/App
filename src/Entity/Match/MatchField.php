<?php

namespace App\Entity\Match;

use Symfony\UX\Map\Map;
use Symfony\UX\Map\Marker;
use Symfony\UX\Map\Point;

class MatchField
{
    public int $Id;
    public string $Name;
    public function getId(): int { return $this->Id; }
    public function getName(): string { return $this->Name; }

    public ?string $Address;
    public function getAddress(): ?string { return $this->Address; }
    public function hasAddress(): bool { return is_string(value: $this->Address); }

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
        $point = new Point(latitude: $this->getLatitude(), longitude: $this->getLongitude());
        return (new Map())
            ->addMarker(marker: new Marker(
                position: $point, 
                title: $this->getName()
            ))
            
            ->center(center: $point)
            //->zoom(10)
            ->fitBoundsToMarkers()
            ;
    }
}