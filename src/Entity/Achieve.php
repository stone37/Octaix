<?php

namespace App\Entity;

use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\MediaTrait;
use App\Entity\Traits\PositionTrait;
use App\Entity\Traits\TimestampableTrait;
use DateTimeImmutable;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Repository\AchieveRepository;

/**
 * Class Service
 * @package App\Entity
 *
 * @Vich\Uploadable
 *
 * @Entity(repositoryClass=AchieveRepository::class)
 */
class Achieve
{
    use IdTrait;
    use PositionTrait;
    use EnabledTrait;
    use TimestampableTrait;
    use MediaTrait;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="100")
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $title = "";

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"title"}, unique=true)
     *
     * @ORM\Column(type="string", length=100)
     */
    private string $slug;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description = "";

    /**
     * @var Service
     *
     * @ORM\ManyToOne(targetEntity=Service::class, inversedBy="achieves")
     */
    private $service;

    /**
     * @var File
     *
     * @Assert\File(maxSize="10M")
     *
     * @Vich\UploadableField(
     *     mapping="achieve",
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
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Service
     */
    public function getService(): ?Service
    {
        return $this->service;
    }

    /**
     * @param Service $service
     */
    public function setService(?Service $service): self
    {
        $this->service = $service;
        $service->addAchieve($this);

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
        return $this->getTitle();
    }
}

