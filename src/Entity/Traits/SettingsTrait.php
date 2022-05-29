<?php

namespace App\Entity\Traits;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Trait SettingsTrait
 * @package App\Entity\Traits
 */
trait SettingsTrait
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string", length=255)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $fax;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $district;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $facebookAddress;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $twitterAddress;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $instagramAddress;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $youtubeAddress;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $linkedinAddress;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $activeAchieve;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $activePost;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $activeReview;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $activeOffre;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $activeEservice;

    /**
     * @var File
     *
     * @Assert\File(maxSize="10M")
     *
     * @Vich\UploadableField(
     *     mapping="settings",
     *     fileNameProperty="fileName",
     *     size="fileSize",
     *     mimeType="fileMimeType",
     *     originalName="fileOriginalName"
     * )
     */
    private $file;

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
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

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
    public function getFax(): ?string
    {
        return $this->fax;
    }

    /**
     * @param string $fax
     */
    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

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
     * @return string
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry(?string $country): self
    {
        $this->country = $country;

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
     * @return string
     */
    public function getDistrict(): ?string
    {
        return $this->district;
    }

    /**
     * @param string $district
     */
    public function setDistrict(?string $district): self
    {
        $this->district = $district;

        return $this;
    }

    /**
     * @return string
     */
    public function getFacebookAddress(): ?string
    {
        return $this->facebookAddress;
    }

    /**
     * @param string $facebookAddress
     */
    public function setFacebookAddress(?string $facebookAddress): self
    {
        $this->facebookAddress = $facebookAddress;

        return $this;
    }

    /**
     * @return string
     */
    public function getTwitterAddress(): ?string
    {
        return $this->twitterAddress;
    }

    /**
     * @param string $twitterAddress
     */
    public function setTwitterAddress(?string $twitterAddress): self
    {
        $this->twitterAddress = $twitterAddress;

        return $this;
    }

    /**
     * @return string
     */
    public function getInstagramAddress(): ?string
    {
        return $this->instagramAddress;
    }

    /**
     * @param string $instagramAddress
     */
    public function setInstagramAddress(?string $instagramAddress): self
    {
        $this->instagramAddress = $instagramAddress;

        return $this;
    }

    /**
     * @return string
     */
    public function getLinkedinAddress(): ?string
    {
        return $this->linkedinAddress;
    }

    /**
     * @param string $linkedinAddress
     */
    public function setLinkedinAddress(?string $linkedinAddress): self
    {
        $this->linkedinAddress = $linkedinAddress;

        return $this;
    }

    /**
     * @return string
     */
    public function getYoutubeAddress(): ?string
    {
        return $this->youtubeAddress;
    }

    /**
     * @param string $youtubeAddress
     */
    public function setYoutubeAddress(?string $youtubeAddress): self
    {
        $this->youtubeAddress = $youtubeAddress;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActiveAchieve(): ?bool
    {
        return $this->activeAchieve;
    }

    /**
     * @param bool $activeAchieve
     */
    public function setActiveAchieve(?bool $activeAchieve): self
    {
        $this->activeAchieve = $activeAchieve;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActiveEservice(): ?bool
    {
        return $this->activeEservice;
    }

    /**
     * @param bool $activeEservice
     */
    public function setActiveEservice(?bool $activeEservice): self
    {
        $this->activeEservice = $activeEservice;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActiveOffre(): ?bool
    {
        return $this->activeOffre;
    }

    /**
     * @param bool $activeOffre
     */
    public function setActiveOffre(?bool $activeOffre): self
    {
        $this->activeOffre = $activeOffre;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActivePost(): ?bool
    {
        return $this->activePost;
    }

    /**
     * @param bool $activePost
     */
    public function setActivePost(?bool $activePost): self
    {
        $this->activePost = $activePost;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActiveReview(): ?bool
    {
        return $this->activeReview;
    }

    /**
     * @param bool $activeReview
     */
    public function setActiveReview(?bool $activeReview): self
    {
        $this->activeReview = $activeReview;

        return $this;
    }
}


