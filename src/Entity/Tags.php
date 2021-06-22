<?php

namespace App\Entity;

use App\Repository\TagsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TagsRepository::class)
 */
class Tags
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $category;

    /**
     * @ORM\ManyToMany(targetEntity=Pet::class, inversedBy="tags")
     */
    private $pet;

    public function __construct()
    {
        $this->pet = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCategory(): ?int
    {
        return $this->category;
    }

    public function setCategory(int $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Pet[]
     */
    public function getPet(): Collection
    {
        return $this->pet;
    }

    public function addPet(Pet $pet): self
    {
        if (!$this->pet->contains($pet)) {
            $this->pet[] = $pet;
        }

        return $this;
    }

    public function removePet(Pet $pet): self
    {
        $this->pet->removeElement($pet);

        return $this;
    }
}
