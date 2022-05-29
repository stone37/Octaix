<?php

namespace App\Entity;

use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\DemandRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DemandRepository::class)
 */
class Demand
{
    use IdTrait;
    use TimestampableTrait;
    use EnabledTrait;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="180")
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $firstName = "";

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="180")
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $lastName = "";

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="180")
     * @Assert\Email()
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $email = "";

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="8", max="15")
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $phone = "";

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $city;

    /**
     * @var Offer
     *
     * @ORM\ManyToOne(targetEntity=Offer::class, inversedBy="demands")
     */
    private $offer = null;

    public function __construct()
    {
        $this->enabled = false;
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
    public function setLastName(string $lastName): self
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


    /**
     * @return string
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Offer
     */
    public function getOffer(): ?Offer
    {
        return $this->offer;
    }

    /**
     * @param Offer $offer
     */
    public function setOffer(?Offer $offer): self
    {
        $this->offer = $offer;
        $offer->addDemand($this);

        return $this;
    }

    public function __toString()
    {
        return $this->firstName . ' ' . $this->lastName;
    }
}

