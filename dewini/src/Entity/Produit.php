<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert; // Ajoutez cette ligne

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
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
    private ?string $nom = null;

   

    #[ORM\Column]
        /**
     * @Assert\NotBlank(message="Le champ prix ne doit pas être vide.")
     * @Assert\Range(
     *      min = 0,
     *      max = 1000,
     *      notInRangeMessage = "Le prix doit être compris entre {{ min }} et {{ max }}.",
     * )
     */

    private ?float $prix = null;


    #[ORM\Column(length: 255, nullable: true)]
   
    private ?string $ImageName = null;

    #[ORM\OneToMany(targetEntity: DetailCommande::class, mappedBy: 'Produit')]
    private Collection $detailcommande;

    public function __construct()
    {
        $this->detailcommande = new ArrayCollection();
    }

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

   

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

   

    public function getImageName(): ?string
    {
        return $this->ImageName;
    }

    public function setImageName(?string $ImageName): static
    {
        $this->ImageName = $ImageName;

        return $this;
    }

    /**
     * @return Collection<int, DetailCommande>
     */
    public function getDetailcommande(): Collection
    {
        return $this->detailcommande;
    }

    public function addDetailcommande(DetailCommande $detailcommande): static
    {
        if (!$this->detailcommande->contains($detailcommande)) {
            $this->detailcommande->add($detailcommande);
            $detailcommande->setProduit($this);
        }

        return $this;
    }

    public function removeDetailcommande(DetailCommande $detailcommande): static
    {
        if ($this->detailcommande->removeElement($detailcommande)) {
            // set the owning side to null (unless already changed)
            if ($detailcommande->getProduit() === $this) {
                $detailcommande->setProduit(null);
            }
        }

        return $this;
    }
}
