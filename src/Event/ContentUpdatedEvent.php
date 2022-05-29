<?php

namespace App\Event;

use App\Entity\Content;

class ContentUpdatedEvent
{
    private Content $content;
    private Content $previous;

    public function __construct(Content $content, Content $previous)
    {
        $this->content = $content;
        $this->previous = $previous;
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function getPrevious(): Content
    {
        return $this->previous;
    }
}

