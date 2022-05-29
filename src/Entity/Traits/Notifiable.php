<?php

namespace App\Entity\Traits;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

trait Notifiable
{
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $notificationsReadAt = null;

    public function getNotificationsReadAt(): ?DateTimeInterface
    {
        return $this->notificationsReadAt;
    }

    public function setNotificationsReadAt(?DateTimeInterface $notificationsReadAt): void
    {
        $this->notificationsReadAt = $notificationsReadAt;
    }
}
