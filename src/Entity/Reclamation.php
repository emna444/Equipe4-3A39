<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: 'La date est requise.')]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'La description est requise.')]
    #[Assert\Length(min: 20, minMessage: 'La description doit contenir au moins {{ limit }} caractères.')]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le type est requis.')]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La priorité est requise.')]
    private ?string $priorite = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Les commentaires sont requis.')]
    #[Assert\Length(min: 20, minMessage: 'Les commentaires doivent contenir au moins {{ limit }} caractères.')]
    private ?string $commentaires = null;
 
    #[ORM\OneToMany(targetEntity: Reponse::class, mappedBy: 'reclamation')]
    private Collection $reference;


    public function __construct()
    {
        $this->reference = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getPriorite(): ?string
    {
        return $this->priorite;
    }
    
    public function setPriorite(?string $priorite): static
    {
        $this->priorite = $priorite;
        return $this;
    }
    
    public function getCommentaires(): ?string
    {
        return $this->commentaires;
    }

    public function setCommentaires(?string $commentaires): static
    {
        $this->commentaires = $commentaires;

        return $this;
    }
    

    /**
     * @return Collection<int, Reponse>
     */
    public function getReference(): Collection
    {
        return $this->reference;
    }

    public function addReference(Reponse $reference): static
    {
        if (!$this->reference->contains($reference)) {
            $this->reference->add($reference);
            $reference->setReclamation($this);
        }

        return $this;
    }

    public function removeReference(Reponse $reference): static
    {
        if ($this->reference->removeElement($reference)) {
            // set the owning side to null (unless already changed)
            if ($reference->getReclamation() === $this) {
                $reference->setReclamation(null);
            }
        }

        return $this;
    }
}
