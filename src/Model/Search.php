<?php

namespace App\Model;

class Search
{
    /**
     * @var string
     */
    private $data;

    /**
     * @return string
     */
    public function getData(): ?string
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData(?string $data): self
    {
        $this->data = $data;

        return $this;
    }
}