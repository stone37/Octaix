<?php

namespace App\Model\Admin;

class OfferSearch
{
    /**
     * @var boolean
     */
    private $enabled;

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