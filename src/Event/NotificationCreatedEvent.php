<?php

namespace App\Event;

use App\Entity\Notification;

class NotificationCreatedEvent
{
    private $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function getNotification(): Notification
    {
        return $this->notification;
    }
}
