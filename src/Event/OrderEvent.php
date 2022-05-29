<?php

namespace App\Event;

use App\Entity\Command;

class OrderEvent
{
    /**
     * @var Command
     */
    private $Command;

    public function  __construct(Command $Command)
    {
        $this->Command = $Command;
    }

    /**
     * @return Command
     */
    public function getCommand(): Command
    {
        return $this->Command;
    }
}

