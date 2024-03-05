<?php

namespace App\Entity;

use App\Repository\RendezvousRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


#[ORM\Entity(repositoryClass: RendezvousRepository::class)]
class Rendezvous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: 'Le date ne peut pas être vide')]
    #[Assert\GreaterThanOrEqual(value: "today", message: "La date doit être ultérieure à aujourd'hui")]
    private ?\DateTimeInterface $date = null;


    /**
     * @Assert\Callback
     */
    public function validateHeureRdv(ExecutionContextInterface $context)
    {
        $heureRdv = $this->date->format('Y-m-d H:i');
    
        if ($heureRdv >'08:00' && $heureRdv < '18:00') {
            $context->buildViolation('Le Rendez-vous doit être pris entre 08h00 et 18h00')
                ->atPath('date')
                ->addViolation();
        }
    }
    #[ORM\Column]
    /**
 * @Assert\Choice(choices={0, 1}, message="La valeur doit être soit 0:disonible ou 1 occupe.")
 */
    private ?int $etat = null;

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

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): static
    {
        $this->etat = $etat;

        return $this;
    }
}
