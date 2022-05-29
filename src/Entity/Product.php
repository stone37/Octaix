<?php

namespace App\Entity;

use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\ProductTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    use IdTrait;
    use ProductTrait;
    use EnabledTrait;
    use TimestampableTrait;
}
