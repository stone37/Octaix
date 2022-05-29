<?php

namespace App\Entity;

use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\MediaTrait;
use App\Entity\Traits\PositionTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\TeamRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class Team
 * @package App\Entity
 *
 * @ORM\Entity(repositoryClass=TeamRepository::class)
 *
 * @Vich\Uploadable
 */
class Team
{
    use IdTrait;
    use EnabledTrait;
    use PositionTrait;
    use TimestampableTrait;
    use MediaTrait;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $name = "";

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $fonction = "";

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description = "";

    /**
     * @var File
     *
     * @Assert\File(maxSize="10M")
     *
     * @Vich\UploadableField(
     *     mapping="team",
     *     fileNameProperty="fileName",
     *     size="fileSize",
     *     mimeType="fileMimeType",
     *     originalName="fileOriginalName"
     * )
     */
    private $file;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    /**
     * @param string $fonction
     */
    public function setFonction(?string $fonction): self
    {
        $this->fonction = $fonction;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return File
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * @param File $image
     */
    public function setFile(?File $image = null): self
    {
        $this->file = $image;

        if (null !== $image) {
            $this->updatedAt = new DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): ?string
    {
        return $this->getName();
    }
}
