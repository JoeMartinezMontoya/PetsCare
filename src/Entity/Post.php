<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    public const CATEGORY = [
        0 => 'Pet Sitting',
        1 => 'Disparition',
        2 => 'Adoption',
        3 => 'Apercu'
    ];

    public const DURATION = [
        0 => 'Jour(s)',
        1 => 'Semaine(s)',
        2 => 'Mois'
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
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="integer")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     */
    private $author;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $durationType;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $petSittingStart;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastSeen;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $missingPet;

    /**
     * @var Picture|null
     */
    private $picture;

    /**
     * @Assert\All(
     *     @Assert\Image(mimeTypes="image/jpeg")
     * )
     */
    private $pictureFiles;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $species;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $petsToBeWatched = [];

    /**
     * @ORM\ManyToMany(targetEntity=Tags::class, inversedBy="posts")
     */
    private $tags;

    /**
     * @ORM\ManyToMany(targetEntity=Picture::class, inversedBy="posts", cascade={"persist"})
     */
    private $pictures;

    /**
     * @ORM\Column(type="float", scale=4, precision=6, nullable=true)
     */
    private $lat;

    /**
     * @ORM\Column(type="float", scale=4, precision=7, nullable=true)
     */
    private $lng;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $town;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->pictures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Return a small sample of the content
     * @return string
     */
    public function getExcerpt(): string
    {
        if (strlen($this->content) > 80) {
            return substr($this->content, 0, 80) . '...';
        }
        return $this->content;
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

    public function getCategory(): ?int
    {
        return $this->category;
    }

    public function getCategoryIcon(): ?string
    {
        $path = 'pictures/ressources/icons/';
        switch ($this->category) {
            case 0 :
                $output = $path . 'petsitting.svg';
                break;
            case 1:
                $output = $path . 'missing.svg';
                break;
            case 2:
                $output = $path . 'adoption.svg';
                break;
            case 3:
                $output = $path . 'found.svg';
                break;
        }
        return $output ?? '';
    }

    public function getCategoryImage(): ?string
    {
        $path = 'pictures/ressources/categoryImages/';

        switch ($this->category) {
            case 0 :
                $output = $path . 'petsitting.png';
                break;
            case 1:
                $output = $path . 'missing.png';
                break;
            case 2:
                $output = $path . 'adoption.png';
                break;
            case 3:
                $output = $path . 'found.png';
                break;
        }
        return $output ?? '';
    }

    public function setCategory(int $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getDurationType(): ?int
    {
        return $this->durationType;
    }

    public function getDurationTypeString(): ?string
    {
        return self::DURATION[$this->durationType];
    }

    public function setDurationType(int $durationType): self
    {
        $this->durationType = $durationType;

        return $this;
    }

    public function getPetSittingStart(): ?\DateTimeInterface
    {
        return $this->petSittingStart;
    }

    public function setPetSittingStart(\DateTimeInterface $petSittingStart): self
    {
        $this->petSittingStart = $petSittingStart;

        return $this;
    }

    public function getLastSeen(): ?\DateTimeInterface
    {
        return $this->lastSeen;
    }

    public function setLastSeen(?\DateTimeInterface $lastSeen): self
    {
        $this->lastSeen = $lastSeen;

        return $this;
    }

    public function getMissingPet(): ?int
    {
        return $this->missingPet;
    }

    public function setMissingPet(?int $missingPet): self
    {
        $this->missingPet = $missingPet;

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
     * @return Post
     */
    public function setPicture(Picture $picture): Post
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
     * @return Post
     */
    public function setPictureFiles($pictureFiles): Post
    {
        foreach ($pictureFiles as $pictureFile) {
            $picture = new Picture();
            $picture->setImageFile($pictureFile);
            $this->addPicture($picture);
        }
        $this->pictureFiles = $pictureFiles;
        return $this;
    }

    public function getSpecies(): ?int
    {
        return $this->species;
    }

    public function getSpeciesString(): ?string
    {
        return Pet::SPECIES[$this->species];
    }

    public function setSpecies(?int $species): self
    {
        $this->species = $species;

        return $this;
    }

    public function getPetsToBeWatched(): ?array
    {
        return $this->petsToBeWatched;
    }

    public function setPetsToBeWatched(array $petsToBeWatched): self
    {
        $this->petsToBeWatched = $petsToBeWatched;

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
        }

        return $this;
    }

    public function removeTag(Tags $tag): self
    {
        $this->tags->removeElement($tag);

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
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        $this->pictures->removeElement($picture);

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(float $lng): self
    {
        $this->lng = $lng;

        return $this;
    }

    public function getTown(): ?string
    {
        if ($this->town !== null) {
            $preposition = 'À ';
            $formattedName = $this->town;

            if (str_contains($this->town, 'Le ')) {
                $preposition = 'Au ';
                $formattedName = str_replace('Le ', $preposition, $this->town);
            } elseif (str_contains($this->town, 'Les ')) {
                $preposition = 'Aux ';
                $formattedName =  str_replace('Les ', $preposition, $this->town);
            } else {
                return $preposition . $formattedName;
            }
        }
        return $formattedName ?? $this->town;
    }

    public function setTown(?string $town): self
    {
        $this->town = $town;

        return $this;
    }
}
