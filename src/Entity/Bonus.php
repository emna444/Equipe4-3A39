<?php

namespace App\Entity;

use App\Repository\BonusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert; // Ajoutez cette ligne


#[ORM\Entity(repositoryClass: BonusRepository::class)]
class Bonus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
     /**
 * @Assert\NotBlank(message="Le champ nom ne doit pas être vide.")
 * */
    private ?float $montant = null;

    #[ORM\ManyToOne(inversedBy: 'bonuses')]
    private ?User $patient = null;

    #[ORM\OneToMany(targetEntity: Dons::class, mappedBy: 'bonus')]
    private Collection $dons;

    public function __construct()
    {
        $this->dons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getPatient(): ?User
    {
        return $this->patient;
    }

    public function setPatient(?User $patient): static
    {
        $this->patient = $patient;

        return $this;
    }

    /**
     * @return Collection<int, Dons>
     */
    public function getDons(): Collection
    {
        return $this->dons;
    }

    public function addDon(Dons $don): static
    {
        if (!$this->dons->contains($don)) {
            $this->dons->add($don);
            $don->setBonus($this);
        }

        return $this;
    }

    public function removeDon(Dons $don): static
    {
        if ($this->dons->removeElement($don)) {
            // set the owning side to null (unless already changed)
            if ($don->getBonus() === $this) {
                $don->setBonus(null);
            }
        }

        return $this;
    }
    public function __toString(){
        return $this ->montant;
    }
}
