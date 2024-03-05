<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]

    #[Assert\NotBlank(message:"Le champ email ne doit pas être vide.")]
    #[Assert\Email(message:"L'adresse email n'est pas valide.")]


    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]

    private ?string $password = null;

    #[ORM\Column]
    #[Assert\Length( min:8, max:8, exactMessage:"Le champ doit contenir exactement 8 numeros.")]
    #[Assert\NotBlank( message:"Le champ Cin ne doit pas être vide.")]
    #[Assert\Regex(pattern:"/^\d+$/", message:"Le champ Cin ne doit contenir que des chiffres")]
    private ?int $cin = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank( message:"Le champ nom ne doit pas être vide.")]
    #[Assert\Regex(pattern:"/^[a-zA-Z\s]+$/", message:"Le champ ne doit contenir que des lettres.")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank( message:"Le champ prenom ne doit pas être vide.")]
    #[Assert\Regex(pattern:"/^[a-zA-Z\s]+$/", message:"Le champ ne doit contenir que des lettres.")]
    private ?string $prenom = null;

    

    #[ORM\Column]
    #[Assert\Length( min:8, max:8, exactMessage:"Le champ doit contenir exactement 8 numeros.")]
    #[Assert\NotBlank( message:"Le champ telephone ne doit pas être vide.")]
    #[Assert\Regex(pattern:"/^\d+$/", message:"Le champ téléphone ne doit contenir que des chiffres")]
    private ?int $telephone = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank( message:"Le champ ville ne doit pas être vide.")]
    #[Assert\Regex(pattern:"/^[a-zA-Z\s]+$/", message:"Le champ ne doit contenir que des lettres.")]
    private ?string $ville = null;

    #[ORM\Column(type: 'boolean')]
    private $is_verified = false;
    
    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $resetToken;

    #[ORM\Column]
    private ?int $don_count = null;

    public function __construct()
    {
        $this->bonuses = new ArrayCollection();
        $this->dons = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getCin(): ?int
    {
        return $this->cin;
    }

    public function setCin(?int $cin): static
    {
        $this->cin = $cin;

        return $this;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    

    public function getTelephone(): ?int
    {
        return $this->telephone;
    }

    public function setTelephone(?int $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->is_verified;
    }

    public function setIsVerified(bool $is_verified): self
    {
        $this->is_verified = $is_verified;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }
    
   /**
     * @return Collection<int, Bonus>
     */
    public function getBonuses(): Collection
    {
        return $this->bonuses;
    }

    public function addBonus(Bonus $bonus): static
    {
        if (!$this->bonuses->contains($bonus)) {
            $this->bonuses->add($bonus);
            $bonus->setPatient($this);
        }

        return $this;
    }

    public function removeBonus(Bonus $bonus): static
    {
        if ($this->bonuses->removeElement($bonus)) {
            // set the owning side to null (unless already changed)
            if ($bonus->getPatient() === $this) {
                $bonus->setPatient(null);
            }
        }

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
            $don->setUserP($this);
        }

        return $this;
    }

    public function removeDon(Dons $don): static
    {
        if ($this->dons->removeElement($don)) {
            // set the owning side to null (unless already changed)
            if ($don->getUserP() === $this) {
                $don->setUserP(null);
            }
        }

        return $this;
    }

    public function getDonCount(): ?int
    {
        return $this->don_count;
    }

    public function setDonCount(int $don_count): static
    {
        $this->don_count = $don_count;

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
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

}
