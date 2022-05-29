<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait MediaTrait
 * @package App\Entity\Traits
 */
trait MediaTrait
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $fileName = null;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $fileSize = null;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $fileMimeType = null;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $fileOriginalName = null;

    /**
     * @return string
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName(?string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @return int
     */
    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    /**
     * @param int $fileSize
     */
    public function setFileSize(?int $fileSize): self
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileMimeType(): ?string
    {
        return $this->fileMimeType;
    }

    /**
     * @param string $fileMimeType
     */
    public function setFileMimeType(?string $fileMimeType): self
    {
        $this->fileMimeType = $fileMimeType;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileOriginalName(): ?string
    {
        return $this->fileOriginalName;
    }

    /**
     * @param string $fileOriginalName
     */
    public function setFileOriginalName(?string $fileOriginalName): self
    {
        $this->fileOriginalName = $fileOriginalName;

        return $this;
    }
}
