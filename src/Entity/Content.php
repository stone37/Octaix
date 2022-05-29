<?php

namespace App\Entity;

use App\Entity\Traits\MediaTrait;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ContentRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "post" = "App\Entity\Post",
 * })
 */
abstract class Content
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    private string $type = '';

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $title = null;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"title"}, unique=true)
     *
     * @ORM\Column(type="string", length=100)
     */
    private ?string $slug = null;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $content = null;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private bool $online = false;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private ?User $author = null;

    use TimestampableTrait;
    use MediaTrait;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
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

    /**
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getExcerpt(): string
    {
        if (null === $this->content) {
            return '';
        }

        $parts = preg_split("/(\r\n|\r|\n){2}/", $this->content);

        return false === $parts ? '' : strip_tags($parts[0]);
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function isOnline(): bool
    {
        return $this->online;
    }

    /**
     * @return $this
     */
    public function setOnline(bool $online): self
    {
        $this->online = $online;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User $author
     *
     * @return $this
     */
    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }
}
