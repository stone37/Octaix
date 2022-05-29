<?php

namespace App\Entity\Traits;

use App\Entity\Command;
use App\Entity\Review;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Trait UserTrait
 * @package App\Entity\Traits
 */
trait UserTrait
{
    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message="Entrez un prénom s'il vous plait.",
     *     groups={"Registration", "Profile"}
     * )
     *
     * @Assert\Length(
     *     min="2",
     *     max="180",
     *     minMessage="Le prénom est trop court.",
     *     maxMessage="Le prénom est trop long.",
     *     groups={"Registration", "Profile"}
     * )
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $firstName;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message="Entrez un prénom s'il vous plait.",
     *     groups={"Registration", "Profile"}
     * )
     *
     * @Assert\Length(
     *     min="2",
     *     max="180",
     *     minMessage="Le prénom est trop court.",
     *     maxMessage="Le prénom est trop long.",
     *     groups={"Registration", "Profile"}
     * )
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $lastName;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message="Entrez un numéro de téléphone s''il vous plait.",
     *     groups={"Registration", "Profile"}
     * )
     *
     * @Assert\Length(
     *     min="10",
     *     max="180",
     *     minMessage="Le numéro de téléphone est trop court.",
     *     maxMessage="Le numéro de téléphone est trop long.",
     *     groups={"Registration", "Profile"}
     * )
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $phone = null;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthDay = null;

    /**
     * @var File
     *
     * @Assert\File(maxSize="10M")
     *
     * @Vich\UploadableField(
     *     mapping="user",
     *     fileNameProperty="fileName",
     *     size="fileSize",
     *     mimeType="fileMimeType",
     *     originalName="fileOriginalName"
     * )
     */
    private $file;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt = null;

    /**
     * @var ArrayCollection|Command
     *
     * @ORM\OneToMany(targetEntity=Command::class, mappedBy="user", cascade={"ALL"}, orphanRemoval=true)
     */
    private $orders = null;

    /**
     * @var ArrayCollection|Review
     *
     * @ORM\OneToMany(targetEntity=Review::class, mappedBy="customer")
     */
    private $reviews;

    public function __constructUser()
    {
        $this->createdAt = new DateTime();
        $this->orders  = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getBirthDay(): ?DateTime
    {
        return $this->birthDay;
    }

    /**
     * @param DateTime $birthDay
     */
    public function setBirthDay(?DateTime $birthDay): self
    {
        $this->birthDay = $birthDay;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getOrders(): ?Collection
    {
        return $this->orders;
    }

    public function addOrder(Command $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
        }

        return $this;
    }

    public function removeOrder(Command $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
        }

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
     * @return DateTime
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(?DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getReviews(): ?Collection
    {
        return $this->reviews;
    }

    public function addReview(?Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
        }

        return $this;
    }

    public function removeReview(?Review $review): self
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->removeElement($review);
        }

        return $this;
    }

    public function __toString()
    {
        return (string) ucfirst($this->getFirstName()) . ' ' . ucfirst($this->getLastName());
    }
}

