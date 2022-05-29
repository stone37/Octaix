<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 */
class Notification
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="user_id", onDelete="CASCADE", nullable=true)
     */
    private $user = null;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Groups({"read:notification", "create:notification"})
     */
    private $message;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank()
     * @Assert\Url()
     * @Groups({"read:notification", "create:notification"})
     */
    private $url = null;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"read:notification"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"create:notification"})
     */
    private $channel = 'public';

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $target = null;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Notification
    {
        $this->id = $id;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): Notification
    {
        $this->user = $user;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): Notification
    {
        $this->message = $message;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): Notification
    {
        $this->url = $url;

        return $this;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): Notification
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getChannel(): ?string
    {
        return $this->channel;
    }

    public function setChannel(?string $channel): Notification
    {
        $this->channel = $channel;

        return $this;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(?string $target): Notification
    {
        $this->target = $target;

        return $this;
    }

    public function isRead(): bool
    {
        if (null === $this->user) {
            return false;
        }
        $notificationsReadAt = $this->user->getNotificationsReadAt();

        return $notificationsReadAt ? ($this->createdAt > $notificationsReadAt) : false;
    }
}
