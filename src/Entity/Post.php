<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 * @ORM\Table("blog_post")
 *
 * @Vich\Uploadable
 */
class Post extends Content 
{
    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="posts")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private ?Category $category = null;

    /**
     * @var File
     *
     * @Assert\File(maxSize="10M")
     *
     * @Vich\UploadableField(
     *     mapping="post",
     *     fileNameProperty="fileName",
     *     size="fileSize",
     *     mimeType="fileMimeType",
     *     originalName="fileOriginalName"
     * )
     */
    private $file;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $comment = true;

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function hasVideo(): bool
    {
        if (null !== $this->getContent()) {
            return 1 === preg_match('/^[^\s]*youtube.com/mi', $this->getContent());
        }

        return false;
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
            $this->setUpdatedAt(new DateTime());
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isComment(): ?bool
    {
        return $this->comment;
    }

    /**
     * @param bool $comment
     */
    public function setComment(?bool $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}


