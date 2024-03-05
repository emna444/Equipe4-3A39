<?php

namespace App\Entity;
use App\Entity\User;
use App\Repository\UserRepository;
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

   
   
    #[ORM\Column(type:"integer")]
      private int $likes = 0;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_fin = null;

    

    #[ORM\Column(length: 7)]
    private ?string $background_color = null;

    #[ORM\Column(length: 255)]
    private ?string $text_color = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ImageName = null;

    #[ORM\ManyToOne(inversedBy: 'eventCat')]
    private ?Categories $cats = null;

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

//********likes
    public function getLikes(): int
    {
        return $this->likes;
    }

    public function setLikes(?int $likes): Evenement
    {
        $this->likes = $likes;

        return $this;
    }

   //part++

   public function getDateDebut(): ?\DateTimeInterface
   {
       return $this->date_debut;
   }

   public function setDateDebut(\DateTimeInterface $date_debut): static
   {
       $this->date_debut = $date_debut;

       return $this;
   }

   public function getDateFin(): ?\DateTimeInterface
   {
       return $this->date_fin;
   }

   public function setDateFin(\DateTimeInterface $date_fin): static
   {
       $this->date_fin = $date_fin;

       return $this;
   }

  
   public function getBackgroundColor(): ?string
   {
       return $this->background_color;
   }

   public function setBackgroundColor(string $background_color): static
   {
       $this->background_color = $background_color;

       return $this;
   }

   public function getTextColor(): ?string
   {
       return $this->text_color;
   }

   public function setTextColor(string $text_color): static
   {
       $this->text_color = $text_color;

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

   public function getCats(): ?Categories
   {
       return $this->cats;
   }

   public function setCats(?Categories $cats): static
   {
       $this->cats = $cats;

       return $this;
   }
  

    
}
