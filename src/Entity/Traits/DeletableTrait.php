<?php

namespace App\Entity\Traits;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

trait DeletableTrait
{
    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $deleteAt = null;

    public function getDeleteAt(): ?DateTimeImmutable
    {
        return $this->deleteAt;
    }

    public function setDeleteAt(?DateTimeImmutable $deleteAt): self
    {
        $this->deleteAt = $deleteAt;

        return $this;
    }
}
