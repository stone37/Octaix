<?php

namespace App\Entity\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Trait TimestampableTrait
 * @package App\Entity\Traits
 */
trait TimestampableTrait
{
    /**
     * @var Datetime
     *
     * @Gedmo\Timestampable(on="create"))
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var Datetime
     *
     * @Gedmo\Timestampable(on="update")
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * Set createdAt
     *
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return DateTime
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt(Datetime $updatedAt = null): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return DateTime
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }
}
