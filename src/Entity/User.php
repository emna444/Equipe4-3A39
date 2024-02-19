<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    /**
 * @Assert\NotBlank(message="Le champ email ne doit pas être vide.")
 * @Assert\Email(message="L'adresse email n'est pas valide.")
 */

    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]

    private ?string $password = null;

    #[ORM\Column]
 
/**
 * @Assert\NotBlank(message="Le champ CIN ne doit pas être vide.")
 * @Assert\Length(
 *     min=8,
 *     max=8,
 *     exactMessage="Le champ doit contenir exactement 8 numeros."
 * )
 */
    private ?int $cin = null;

    #[ORM\Column(length: 255)]
    /**
 * @Assert\NotBlank(message="Le champ nom ne doit pas être vide.")
 * @Assert\Regex(
 *     pattern="/^[a-zA-Z\s]+$/",
 *     message="Le champ ne doit contenir que des lettres."
 * )
 */
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    /**
 * @Assert\NotBlank(message="Le champ nom ne doit pas être vide.")
 * @Assert\Regex(
 *     pattern="/^[a-zA-Z\s]+$/",
 *     message="Le champ ne doit contenir que des lettres."
 * )
 */
    private ?string $prenom = null;

    

    #[ORM\Column]
 
/**
 * @Assert\NotBlank(message="Le champ telephone ne doit pas être vide.")
 * @Assert\Length(
 *     min=8,
 *     max=8,
 *     exactMessage="Le champ doit contenir exactement 8 numeros."
 * )
 * @Assert\Regex(
 *     pattern="/^\d+$/",
 *     message="Le champ téléphone ne doit contenir que des chiffres."
 * )
 */

    private ?int $telephone = null;

    #[ORM\Column(length: 255)]
        /**
 * @Assert\NotBlank(message="Le ville nom ne doit pas être vide.")
 
 * @Assert\Regex(
 *     pattern="/^[a-zA-Z\s]+$/",
 *     message="Le champ ne doit contenir que des lettres."
 * )
 */
    private ?string $ville = null;

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
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

    public function setCin(int $cin): static
    {
        $this->cin = $cin;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    

    public function getTelephone(): ?int
    {
        return $this->telephone;
    }

    public function setTelephone(int $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    
}
