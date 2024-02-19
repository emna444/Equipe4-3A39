<?php

namespace App\Entity;

use App\Repository\PartenaireRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert; // imporation validation

#[ORM\Entity(repositoryClass: PartenaireRepository::class)]
class Partenaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
 /**
     * @Assert\NotBlank(message=" nom partenaire doit etre non vide")
     * @Assert\Length(
     *      min = 5,
     *      minMessage=" Entrer un nom au mini de 5 caracteres"
     *
     *     )
     *  * @Assert\Regex(
     *     pattern="/^[a-zA-ZÀ-ÿ\s]+$/",
     *     message="Le nom du partenaire ne peut contenir que des lettres et des espaces"
     * )
     */
    #[ORM\Column(length: 255)]
    private ?string $nom = null;


     /**
     * @Assert\Length(
     *      min = 5,
     *      minMessage = "L'adresse du partenaire doit contenir au moins {{ limit }} caractères",
     *      max = 255,
     *      maxMessage = "L'adresse du partenaire ne peut pas dépasser {{ limit }} caractères"
     * )
     * @Assert\NotBlank(message="L'adresse du partenaire ne peut pas être vide")
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z0-9\s\-,'àáâãäåæçèéêëìíîïðòóôõöøùúûüýÿ]+$/",
     *     message="L'adresse du partenaire n'est pas valide"
     * )
     */

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;
   /**  @Assert\NotBlank(message="Le contact du partenaire ne peut pas être vide")
    * @Assert\Regex(
    *     pattern="/^\d{8}$/",
    *     message="Le contact du partenaire doit être un numéro de téléphone à 8 chiffres"
    * )
    */
    #[ORM\Column(length: 255)]
    private ?string $contact = null;

     /**
     * @Assert\NotNull(message="Veuillez sélectionner un événement")
    
     **/

    #[ORM\ManyToOne(inversedBy: 'partenaires')]
    private ?Evenement $evenement = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(string $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    public function getEvenement(): ?Evenement
    {
        return $this->evenement;
    }

    public function setEvenement(?Evenement $evenement): static
    {
        $this->evenement = $evenement;

        return $this;
    }

    public function __toString(){
        return $this ->nom;
    }
}