<?php

namespace App\Entity;

use App\Repository\PetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PetRepository::class)
 */
class Pet
{
    public const SPECIES = [
        0 => 'Chat',
        1 => 'Chien',
        2 => 'Rongeur',
        3 => 'Oiseau',
        4 => 'NAC'
    ];

    public const GENDER = [
        0 => 'Femelle',
        1 => 'Mâle',
        2 => 'Je ne sais pas',
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $age;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="pets")
     */
    private ?User $owner;

    /**
     * Only used in the Pet creation form, if TRUE then Pet->setOwner() = $this->getUser()->getId();
     * @var bool
     */
    private bool $owned;

    /**
     * @ORM\Column(type="integer")
     */
    private $species;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="integer")
     */
    private $gender;

    /**
     * @var Picture|null
     */
    private $picture;

    /**
     * @ORM\OneToMany(targetEntity=Picture::class, mappedBy="pet", orphanRemoval=true, cascade={"persist"})
     */
    private $pictures;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    /**
     * @Assert\All(
     *     @Assert\Image(mimeTypes="image/jpeg")
     * )
     */
    private $pictureFiles;

    /**
     * @ORM\ManyToMany(targetEntity=Tags::class, inversedBy="pets")
     */
    private $tags;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isMissing;

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

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return bool
     */
    public function isOwned(): bool
    {
        return $this->owned;
    }

    /**
     * @param bool $owned
     * @return Pet
     */
    public function setOwned(bool $owned): Pet
    {
        $this->owned = $owned;
        return $this;
    }

    public function getSpecies(): ?int
    {
        return $this->species;
    }

    public function getSpeciesString(): ?string
    {
        return self::SPECIES[$this->species];
    }

    public function getSpeciesIcon(): ?string
    {
        $output = '';
        $path = 'pictures/ressources/icons/';
        switch ($this->species) {
            case 0:
                $output = $path . 'cat.svg';
                break;
            case 1:
                $output = $path . 'dog.svg';
                break;
            case 2:
                $output = $path . 'rodent.svg';
                break;
            case 3:
                $output = $path . 'bird.svg';
                break;
            case 4:
                $output = $path . 'nac.svg';
                break;
        }
        return $output;
    }

    public function setSpecies(int $species): self
    {
        $this->species = $species;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getGender(): ?int
    {
        return $this->gender;
    }

    public function getGenderIcon(): ?string
    {
        if ($this->gender === 0) {
            $output = '<i class="fas fa-venus"></i>';
        } elseif ($this->gender === 1) {
            $output = '<i class="fas fa-mars"></i>';
        } else {
            $output = '<i class="fas fa-genderless"></i>';
        }
        return $output;
    }

    public function getGenderString(): ?string
    {
        return self::GENDER[$this->gender];
    }

    public function getGenderPronoun(): ?string
    {
        return $this->gender === 0 ? 'Elle' : 'Il';
    }

    public function setGender(int $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return Collection|Picture[]
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setPet($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getPet() === $this) {
                $picture->setPet(null);
            }
        }
        return $this;
    }

    /**
     * @return Picture|null
     */
    public function getPicture(): ?Picture
    {
        return $this->picture;
    }

    /**
     * @param Picture|null $picture
     * @return Pet
     */
    public function setPicture(?Picture $picture): Pet
    {
        $this->picture = $picture;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPictureFiles()
    {
        return $this->pictureFiles;
    }

    /**
     * @param mixed $pictureFiles
     * @return Pet
     */
    public function setPictureFiles($pictureFiles): Pet
    {
        foreach ($pictureFiles as $pictureFile) {
            $picture = new Picture();
            $picture->setImageFile($pictureFile);
            $this->addPicture($picture);
        }
        $this->pictureFiles = $pictureFiles;
        return $this;
    }

    /**
     * @return Collection|Tags[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tags $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->addPet($this);
        }

        return $this;
    }

    public function removeTag(Tags $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removePet($this);
        }
        return $this;
    }

    public function getIsMissing(): ?bool
    {
        return $this->isMissing;
    }

    public function setIsMissing(bool $isMissing): self
    {
        $this->isMissing = $isMissing;

        return $this;
    }
}
