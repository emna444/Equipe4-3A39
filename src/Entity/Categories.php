<?php

namespace App\Entity;

use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
class Categories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Evenement::class, mappedBy: 'cats')]
    private Collection $eventCat;

    public function __construct()
    {
        $this->eventCat = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Evenement>
     */
    public function getEventCat(): Collection
    {
        return $this->eventCat;
    }

    public function addEventCat(Evenement $eventCat): static
    {
        if (!$this->eventCat->contains($eventCat)) {
            $this->eventCat->add($eventCat);
            $eventCat->setCats($this);
        }

        return $this;
    }

    public function removeEventCat(Evenement $eventCat): static
    {
        if ($this->eventCat->removeElement($eventCat)) {
            // set the owning side to null (unless already changed)
            if ($eventCat->getCats() === $this) {
                $eventCat->setCats(null);
            }
        }

        return $this;
    }


      public function __toString(){
    return $this ->name;
    }

}