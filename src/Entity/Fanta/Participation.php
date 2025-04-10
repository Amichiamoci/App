<?php

namespace App\Entity\Fanta;

use App\Entity\User;
use App\Repository\Fanta\ParticipationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipationRepository::class)]
class Participation
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'participations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $User = null;

    #[ORM\Column]
    #[ORM\Id]
    private ?int $Year = null;

    #[ORM\Column]
    private ?int $Chapeau = null;

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): static
    {
        $this->User = $User;

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

    public function getChapeau(): ?int
    {
        return $this->Chapeau;
    }

    public function setChapeau(int $Chapeau): static
    {
        $this->Chapeau = $Chapeau;

        return $this;
    }
}
