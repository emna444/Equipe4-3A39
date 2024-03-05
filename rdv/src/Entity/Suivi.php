<?php

namespace App\Entity;

use App\Repository\SuiviRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SuiviRepository::class)]
class Suivi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'L ordonance ne peut pas être vide')]
    #[Assert\Regex(
        pattern: '/^[a-zA-ZÀ-ÿ\s]+$/',
        message: ' ordonance ne peut contenir que des lettres.'
    )]
    #[Assert\Length(
        max: 255,
        maxMessage: 'description peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $ordonnance = null;

    #[ORM\ManyToOne(inversedBy: 'suivis')]
    #[Assert\NotBlank(message: 'La nom patient ne peut pas être vide')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'suivis')]
    #[Assert\NotBlank(message: 'La nom medecin ne peut pas être vide')]
    private ?Medecin $medecin = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Rendezvous $rendezvous = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrdonnance(): ?string
    {
        return $this->ordonnance;
    }

    public function setOrdonnance(string $ordonnance): static
    {
        $this->ordonnance = $ordonnance;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getMedecin(): ?Medecin
    {
        return $this->medecin;
    }

    public function setMedecin(?Medecin $medecin): static
    {
        $this->medecin = $medecin;

        return $this;
    }

    public function getRendezvous(): ?Rendezvous
    {
        return $this->rendezvous;
    }

    public function setRendezvous(?Rendezvous $rendezvous): static
    {
        $this->rendezvous = $rendezvous;

        return $this;
    }
}
