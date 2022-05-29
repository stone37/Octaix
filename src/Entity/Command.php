<?php

namespace App\Entity;

use App\Entity\Traits\IdTrait;
use App\Entity\Traits\OrderTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Command
 * @package App\Entity
 *
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\MappedSuperclass
 */
class Command
{
    use IdTrait;
    use OrderTrait;
    use TimestampableTrait;

    public function __construct()
    {
        $this->__constructOrder();
    }
}

