<?php

namespace App\Event;

use App\Entity\Content;

class ContentDeletedEvent
{
    private Content $content;

    public function __construct(Content $content)
    {
        $this->content = $content;
    }

    public function getContent(): Content
    {
        return $this->content;
    }
}
