<?php

namespace App\Entity;

use App\Entity\Traits\IdTrait;
use App\Entity\Traits\MediaTrait;
use App\Entity\Traits\SettingsTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\SettingsRepository;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class Settings
 * @package App\Entity
 *
 * @ORM\Entity(repositoryClass=SettingsRepository::class)
 * @Vich\Uploadable
 * @ORM\MappedSuperclass
 */
class Settings
{
    use IdTrait;
    use SettingsTrait;
    use MediaTrait;
    use TimestampableTrait;

    /**
     * Return the Settings name
     *
     * @return string
     */
    public function __toString(): ?string
    {
        return (string) $this->getName();
    }
}
