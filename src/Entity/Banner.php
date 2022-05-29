<?php

namespace App\Entity;

use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\MediaTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\BannerRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=BannerRepository::class)
 *
 * @Vich\Uploadable
 */
class Banner
{
    use IdTrait;
    use EnabledTrait;
    use TimestampableTrait;
    use MediaTrait;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $name = "";

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @var array
     *
     * @ORM\Column(type="array", nullable=true)
     */
    private $services;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $link = "";

    /**
     * @var File
     *
     * @Assert\File(maxSize="10M")
     *
     * @Vich\UploadableField(
     *     mapping="banner",
     *     fileNameProperty="fileName",
     *     size="fileSize",
     *     mimeType="fileMimeType",
     *     originalName="fileOriginalName"
     * )
     */
    protected $file;

    /**
     * @return string
     */
    public function getName(): ?string
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
     * @return DateTime
     */
    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    /**
     * @param DateTime $startDate
     */
    public function setStartDate(?DateTime $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    /**
     * @param DateTime $endDate
     */
    public function setEndDate(?DateTime $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return array
     */
    public function getServices(): ?array
    {
        return $this->services;
    }

    /**
     * @param array $services
     */
    public function setServices(?array $services): self
    {
        $this->services = $services;

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
    public function setFile(?File $image = null): void
    {
        $this->file = $image;

        if (null !== $image) {
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    /**
     * @return string
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }
}
