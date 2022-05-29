<?php

namespace App\Entity;

use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\PositionTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\ReviewRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Review
 * @package App\Entity
 *
 * @ORM\Entity(repositoryClass=ReviewRepository::class)
 */
class Review
{
    use IdTrait;
    use EnabledTrait;
    use PositionTrait;
    use TimestampableTrait;

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
     * @ORM\Column(name="profession", type="string", nullable=true)
     *
     * @Assert\NotBlank()
     */
    private $profession = "";

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment = "";

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $ip = "";

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reviews")
     */
    private $customer = null;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rating = 3;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $home;

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
     * @return string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return string
     */
    public function getIp(): ?string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->customer;
    }

    /**
     * @param User $user
     */
    public function setUser(?User $user): self
    {
        $this->customer = $user;
        $user->addReview($this);

        return $this;
    }

    /**
     * @return string
     */
    public function getProfession(): ?string
    {
        return $this->profession;
    }

    /**
     * @param string $profession
     */
    public function setProfession(?string $profession): self
    {
        $this->profession = $profession;

        return $this;
    }

    /**
     * @return int
     */
    public function getRating(): ?int
    {
        return $this->rating;
    }

    /**
     * @param int $rating
     */
    public function setRating(?int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHome(): ?bool
    {
        return $this->home;
    }

    /**
     * @param bool $home
     */
    public function setHome(?bool $home): self
    {
        $this->home = $home;

        return $this;
    }

    /**
     * @return User
     */
    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    /**
     * @param User $customer
     */
    public function setCustomer(?User $customer): self
    {
        $this->customer = $customer;

        return $this;
    }
}

