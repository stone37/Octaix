<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Trait PositionTrait
 * @package App\Entity\Traits
 */
trait PositionTrait
{
    /**
     * @var int
     *
     * @Gedmo\SortablePosition()
     *
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * Set position
     *
     * @param null|int $position
     */
    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return null|int
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }
}


