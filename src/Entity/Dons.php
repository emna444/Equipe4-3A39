<?php

namespace App\Entity;

use App\Repository\DonsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert; // Ajoutez cette ligne
use Symfony\Component\HttpFoundation\File\File;


#[ORM\Entity(repositoryClass: DonsRepository::class)]
class Dons
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    /**
 * @Assert\NotBlank(message="Le champ nom ne doit pas être vide.")
 * @Assert\Length(
 *      min = 5,
 *      minMessage = "Le champ doit contenir au moins 5 lettres."
 * )
 * @Assert\Regex(
 *     pattern="/^[a-zA-Z0-9\s]+$/",
 *     message="Le champ ne doit pas contenir de caractères spéciaux."
 * )
 */
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    /**
 * @Assert\NotBlank(message="Le champ nom ne doit pas être vide.")
 * @Assert\Length(
 *      min = 5,
 *      minMessage = "Le champ doit contenir au moins 5 lettres."
 * )
 */
    private ?string $adresse = null;

    #[ORM\Column(type: Types::TEXT)]
    /**
 * @Assert\NotBlank(message="Le champ nom ne doit pas être vide.")
 * @Assert\Length(
 *      min = 5,
 *      minMessage = "Le champ doit contenir au moins 5 lettres."
 * )
 * @Assert\Regex(
 *     pattern="/^[a-zA-Z0-9\s]+$/",
 *     message="Le champ ne doit pas contenir de caractères spéciaux."
 * )
 */
    private ?string $description = null;
    
    #[ORM\ManyToOne(inversedBy: 'dons')]
    private ?Bonus $bonus = null;

    #[ORM\ManyToOne(inversedBy: 'dons')]
    private ?User $userP = null;


    #[ORM\Column]
    private ?int $user_id = null;

    #[ORM\OneToMany(targetEntity: Comments::class, mappedBy: 'don')]
    private Collection $comments;

    #[ORM\Column(length: 255)]
    private ?string $qrCodePath = null;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getBonus(): ?Bonus
    {
        return $this->bonus;
    }

    public function setBonus(?Bonus $bonus): static
    {
        $this->bonus = $bonus;

        return $this;
    }

    public function getUserP(): ?User
    {
        return $this->userP;
    }

    public function setUserP(?User $userP): static
    {
        $this->userP = $userP;

        return $this;
    }


    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setDon($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getDon() === $this) {
                $comment->setDon(null);
            }
        }

        return $this;
    }

    public function getQrCodePath(): ?string
    {
        return $this->qrCodePath;
    }

    public function setQrCodePath(string $qrCodePath): static
    {
        $this->qrCodePath = $qrCodePath;

        return $this;
    }
    
}
