<?php


namespace App\Entity;


use DateTime;

class PostSearch
{
    /**
     * @var int|null
     */
    private ?int $category = null;
    /**
     * @var DateTime|null
     */
    private ?DateTime $created_at = null;

    /**
     * @return int|null
     */
    public function getCategory(): ?int
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     * @return PostSearch
     */
    public function setCategory($category): PostSearch
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return null
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param null $created_at
     * @return PostSearch
     */
    public function setCreatedAt($created_at): PostSearch
    {
        $this->created_at = $created_at;
        return $this;
    }



}