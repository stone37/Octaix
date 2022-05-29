<?php

namespace App\Event;

use App\Entity\Post;

class PostUpdatedEvent extends ContentUpdatedEvent
{
    public function __construct(Post $content, Post $previous)
    {
        parent::__construct($content, $previous);
    }
}
