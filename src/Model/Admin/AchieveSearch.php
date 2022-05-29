<?php

namespace App\Model\Admin;

class AchieveSearch
{
    /**
     * @var int
     */
    private $service = null;

    /**
     * @var boolean
     */
    private $enabled = null;

    /**
     * @return int
     */
    public function getService(): ?int
    {
        return $this->service;
    }

    /**
     * @param int $service
     */
    public function setService(?int $service): self
    {
        $this->service = $service;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(?bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }
}

