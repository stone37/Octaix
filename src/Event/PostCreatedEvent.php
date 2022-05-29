<?php

namespace App\Event;

use App\Entity\Post;

class PostCreatedEvent extends ContentCreatedEvent
{
    public function __construct(Post $content)
    {
        parent::__construct($content);
    }
}
