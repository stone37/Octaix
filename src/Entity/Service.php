<?php

namespace App\Entity;

use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\MediaTrait;
use App\Entity\Traits\PositionTrait;
use App\Entity\Traits\ServiceTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Entity\Traits\TreeTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Repository\ServiceRepository;

/**
 * Class Service
 * @package App\Entity
 *
 * @Vich\Uploadable()
 * @Gedmo\Tree(type="nested")
 *
 * @ORM\Entity(repositoryClass=ServiceRepository::class)
 * @ORM\MappedSuperclass
 */
class Service
{
    use IdTrait;
    use ServiceTrait;
    use PositionTrait;
    use EnabledTrait;
    use TimestampableTrait;
    use MediaTrait;
    use TreeTrait;

    public function __construct()
    {
        $this->__constructService();

        $this->enabled = true;
    }

    public function __toString()
    {
        return (string) $this->name;
    }
}

