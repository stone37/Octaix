<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait EnabledTrait
 * @package App\Entity\Traits
 */
trait EnabledTrait
{
    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $enabled = true;

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


