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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $location;

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
     * @ORM\OneToMany(targetEntity=Picture::class, mappedBy="post", orphanRemoval=true, cascade={"persist"})
     */
    private $pictures;

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

    public function __construct()
    {
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
                $output = $path . 'wewantyou.jpg';
                break;
            case 1:
                $output = $path . 'lost.jpg';
                break;
            case 2:
                $output = $path . 'adoption.jpg';
                break;
            case 3:
                $output = $path . 'found.jpg';
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
            $picture->setPost($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getPost() === $this) {
                $picture->setPost(null);
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
     * @return Post
     */
    public function setPicture(?Picture $picture): Post
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

    public function setSpecies(?int $species): self
    {
        $this->species = $species;

        return $this;
    }
}
