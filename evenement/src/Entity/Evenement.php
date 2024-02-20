<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    
    
    #[ORM\Column(length: 255)]
    #[Assert\Length( min:3 ,minMessage:" Entrer un nom au mini de 3 caracteres")]
    #[Assert\NotBlank (message:" nom événement doit etre non vide")]
    #[Assert\Regex ( pattern :"/^[a-zA-ZÀ-ÿ0-9\s]+$/", message:" Le nom du évènement ne peut contenir que des lettres et chiffres ")]
    private ?string $nom = null;
     

  
 #[Assert\Range (  min : "today", max : "+10 years",
       minMessage : "La date doit être à partir d'aujourd'hui",
       maxMessage : "La date ne peut pas dépasser {{ compared_value }}" )]


    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le lieu ne peut pas être vide")]
    #[Assert\Regex( pattern: "/^[a-zA-Z0-9\s\-\,\|']+$/",
    message: "Le lieu doit contenir uniquement des lettres et des chiffres")]
    private ?string $lieu = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "description doit être non vide")]
    #[Assert\Length(min: 5,minMessage: "Entrer description au minimum de 5 caractères" )]
    #[Assert\Regex( pattern: "/^[a-zA-ZÀ-ÿ0-9\s\-\é\è\.]+$/",
    message: "La description du événement ne peut contenir que des lettres et des chiffres ")]
    private ?string $description = null;
  
    #relation OneToMany
    #[ORM\OneToMany(targetEntity: Partenaire::class, mappedBy: 'evenement')]
    private Collection $partenaires;

    public function __construct()
    {
        $this->partenaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(?string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }
    /**
     * @return Collection<int, Partenaire>
     */
    public function getPartenaires(): Collection
    {
        return $this->partenaires;
    }

    public function addPartenaire(Partenaire $partenaire): static
    {
        if (!$this->partenaires->contains($partenaire)) {
            $this->partenaires->add($partenaire);
            $partenaire->setEvenement($this);
        }

        return $this;
    }

    public function removePartenaire(Partenaire $partenaire): static
    {
        if ($this->partenaires->removeElement($partenaire)) {
            // set the owning side to null (unless already changed)
            if ($partenaire->getEvenement() === $this) {
                $partenaire->setEvenement(null);
            }
        }

        return $this;
    }
    public function __toString(){
        return $this ->nom;
    }
}
