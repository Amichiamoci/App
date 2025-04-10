<?php

namespace App\Entity\Fanta;

use App\Entity\User;
use App\Repository\Fanta\PlayerChoiceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerChoiceRepository::class)]
class PlayerChoice
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'playerChoices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $User = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'playerChoices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Player $Player = null;

    #[ORM\Column]
    private ?int $Year = null;

    #[ORM\Column]
    private ?bool $IsLeader = null;

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): static
    {
        $this->User = $User;

        return $this;
    }

    public function getPlayer(): ?Player
    {
        return $this->Player;
    }

    public function setPlayer(?Player $Player): static
    {
        $this->Player = $Player;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->Year;
    }

    public function setYear(int $Year): static
    {
        $this->Year = $Year;

        return $this;
    }

    public function isLeader(): ?bool
    {
        return $this->IsLeader;
    }

    public function setIsLeader(bool $IsLeader): static
    {
        $this->IsLeader = $IsLeader;

        return $this;
    }
}
