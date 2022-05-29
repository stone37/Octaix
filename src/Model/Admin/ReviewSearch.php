<?php

namespace App\Model\Admin;

class ReviewSearch
{
    /**
     * @var boolean
     */
    private $enabled = null;

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
    public function setEnabled(?bool $enabled): void
    {
        $this->enabled = $enabled;
    }
}

