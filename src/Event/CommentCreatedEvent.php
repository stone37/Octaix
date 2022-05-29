<?php

namespace App\Event;

use App\Entity\Comment;

class CommentCreatedEvent
{
    private Comment $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function getComment(): Comment
    {
        return $this->comment;
    }
}

