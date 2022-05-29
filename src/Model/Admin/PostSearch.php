<?php

namespace App\Model\Admin;

class PostSearch
{
    /**
     * @var string
     */
    private $category;

    /**
     * @var boolean
     */
    private $published;

    /**
     * @return string
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPublished(): ?bool
    {
        return $this->published;
    }

    /**
     * @param bool $enabled
     */
    public function setPublished(?bool $published): self
    {
        $this->published = $published;

        return $this;
    }
}

