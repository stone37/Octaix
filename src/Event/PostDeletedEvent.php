<?php

namespace App\Event;

use App\Entity\Post;

class PostDeletedEvent extends ContentDeletedEvent
{
    public function __construct(Post $content)
    {
        parent::__construct($content);
    }
}
