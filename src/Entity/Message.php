<?php

namespace App\Entity;

use App\Twig\CacheExtension\CacheableInterface;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Parsedown;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message implements CacheableInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     * @Groups({"read:message"})
     */
    private $id;


    /**
     * @var string
     *
     * @Assert\NotBlank(message="Veuillez renseigne votre prénom")
     * @Assert\Length(min=2, minMessage="Votre prénom doit contenir au moin {{ limit }} caractères")
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $firstname = null;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Veuillez renseigne votre téléphone")
     * @Assert\Length(min=10, minMessage="Votre téléphone doit contenir au moin {{ limit }} caractères")
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $phone = null;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Veuillez renseigne votre email")
     * @Assert\Email(message="Votre email n'est pas valide")
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $email = null;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $accepted = false;

    /**
     * @Assert\NotBlank(message="Veuillez renseigne votre message")
     * @Assert\Length(min=10, minMessage="Votre message doit contenir au moin {{ limit }} caractères")
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $content = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt = null;

    /**
     * @ORM\Column(type="boolean", options={"default":1})
     */
    private $notification = true;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt = null;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $adSimilar = true;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $deleted = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $recepDeleted = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function isAccepted(): ?bool
    {
        return $this->accepted;
    }

    public function setAccepted(bool $accepted): self
    {
        $this->accepted = $accepted;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @Groups({"read:message"})
     */
    public function getFormattedContent(): ?string
    {
        return (new Parsedown())->setSafeMode(true)->text($this->content);
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function hasNotification(): bool
    {
        return $this->notification;
    }

    public function setNotification(bool $notification): Message
    {
        $this->notification = $notification;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAdSimilar(): ?bool
    {
        return $this->adSimilar;
    }

    /**
     * @param bool $adSimilar
     */
    public function setAdSimilar(bool $adSimilar): self
    {
        $this->adSimilar = $adSimilar;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDeleted(): ?bool
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted(?bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRecepDeleted(): ?bool
    {
        return $this->recepDeleted;
    }

    /**
     * @param bool $recepDeleted
     */
    public function setRecepDeleted(?bool $recepDeleted): self
    {
        $this->recepDeleted = $recepDeleted;

        return $this;
    }
}
