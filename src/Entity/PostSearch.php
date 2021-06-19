<?php


namespace App\Entity;


class PostSearch
{
    /**
     * @var int|null
     */
    private ?int $category = null;

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

}