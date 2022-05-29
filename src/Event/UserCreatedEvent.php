<?php

namespace App\Event;

use App\Entity\User;

class UserCreatedEvent
{
    private $user;
    private $usingOauth;

    public function __construct(User $user, bool $usingOauth = false)
    {
        $this->user = $user;
        $this->usingOauth = $usingOauth;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function isUsingOauth(): bool
    {
        return $this->usingOauth;
    }
}
