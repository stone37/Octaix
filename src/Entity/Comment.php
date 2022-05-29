<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment
{
    use TimestampableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $email = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $username = null;

    /**
     * @ORM\Column(type="text")
     */
    private string $content = '';

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=true)
     */
    private ?User $author;

    /**
     * @ORM\ManyToOne(targetEntity=Comment::class, inversedBy="replies")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private ?self $parent = null;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="parent")
     *
     * @var Collection<int, Comment>
     */
    private Collection $replies;

    /**
     * @ORM\ManyToOne(targetEntity=Content::class)
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false, name="content_id")
     */
    private Content $target;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->replies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): Comment
    {
        $this->id = $id;

        return $this;
    }

    public function getUsername(): string
    {
        if (null !== $this->author) {
            return $this->author->getUsername();
        }

        return $this->username ?: '';
    }

    public function setUsername(?string $username): Comment
    {
        $this->username = $username;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): Comment
    {
        $this->content = $content;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): Comment
    {
        $this->author = $author;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): Comment
    {
        $this->parent = $parent;

        return $this;
    }

    public function getTarget(): ?Content
    {
        return $this->target;
    }

    public function setTarget(Content $target): self
    {
        $this->target = $target;

        return $this;
    }

    public function addReply(Comment $comment): self
    {
        if (!$this->replies->contains($comment)) {
            $this->replies->add($comment);
            $comment->setParent($this);
        }

        return $this;
    }

    public function getReplies(): Collection
    {
        return $this->replies;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }
}

