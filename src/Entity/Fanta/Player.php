<?php

namespace App\Entity\Fanta;

use App\Repository\Fanta\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $Year = null;

    #[ORM\Column(nullable: true)]
    private ?int $Staff = null;

    #[ORM\Column(nullable: true)]
    private ?int $Team = null;

    #[ORM\Column]
    private ?int $Chapeau = null;

    /**
     * @var Collection<int, Bonus>
     */
    #[ORM\ManyToMany(targetEntity: Bonus::class, inversedBy: 'Players')]
    private Collection $Bonuses;

    /**
     * @var Collection<int, PlayerChoice>
     */
    #[ORM\OneToMany(targetEntity: PlayerChoice::class, mappedBy: 'Player', orphanRemoval: true)]
    private Collection $playerChoices;

    public function __construct()
    {
        $this->Bonuses = new ArrayCollection();
        $this->playerChoices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStaff(): ?int
    {
        return $this->Staff;
    }

    public function setStaff(?int $Staff): static
    {
        $this->Staff = $Staff;

        return $this;
    }

    public function getTeam(): ?int
    {
        return $this->Team;
    }

    public function setTeam(?int $Team): static
    {
        $this->Team = $Team;

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

    /**
     * @return Collection<int, Bonus>
     */
    public function getBonuses(): Collection
    {
        return $this->Bonuses;
    }

    public function addBonus(Bonus $bonus): static
    {
        if (!$this->Bonuses->contains(element: $bonus)) {
            $this->Bonuses->add(element: $bonus);
        }

        return $this;
    }

    public function removeBonus(Bonus $bonus): static
    {
        $this->Bonuses->removeElement(element: $bonus);

        return $this;
    }

    /**
     * @return Collection<int, PlayerChoice>
     */
    public function getPlayerChoices(): Collection
    {
        return $this->playerChoices;
    }

    public function addPlayerChoice(PlayerChoice $playerChoice): static
    {
        if (!$this->playerChoices->contains(element: $playerChoice)) {
            $this->playerChoices->add(element: $playerChoice);
            $playerChoice->setPlayer(Player: $this);
        }

        return $this;
    }

    public function removePlayerChoice(PlayerChoice $playerChoice): static
    {
        if ($this->playerChoices->removeElement(element: $playerChoice)) {
            // set the owning side to null (unless already changed)
            if ($playerChoice->getPlayer() === $this) {
                $playerChoice->setPlayer(Player: null);
            }
        }

        return $this;
    }
}
