<?php

namespace App\Entity;

use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\MediaTrait;
use App\Entity\Traits\TimestampableTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Repository\OfferRepository;

/**
 * @ORM\Entity(repositoryClass=OfferRepository::class)
 *
 * @Vich\Uploadable
 */
class Offer
{
    use IdTrait;
    use EnabledTrait;
    use TimestampableTrait;
    use MediaTrait;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name = '';

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"}, unique=true)
     *
     * @ORM\Column(type="string", length=100)
     */
    private string $slug;

    /**
     * @var Collection<int,Post>
     *
     * @ORM\OneToMany(targetEntity=Demand::class, mappedBy="offer")
     */
    private Collection $demands;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var File
     *
     * @Assert\File(maxSize="10M")
     *
     * @Vich\UploadableField(
     *     mapping="offer",
     *     fileNameProperty="fileName",
     *     size="fileSize",
     *     mimeType="fileMimeType",
     *     originalName="fileOriginalName"
     * )
     */
    private $file;

    /**
     * @var Service
     *
     * @ORM\ManyToOne(targetEntity=Service::class, inversedBy="offers")
     */
    private $service = null;

    public function __construct()
    {
        $this->demands = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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
     * @return Collection<int,Post>|Post[]
     */
    public function getDemands(): Collection
    {
        return $this->demands;
    }

    public function addDemand(Demand $demand): self
    {
        if (!$this->demands->contains($demand)) {
            $this->demands[] = $demand;
        }

        return $this;
    }

    public function removeDemand(Demand $demand): self
    {
        if ($this->demands->contains($demand)) {
            $this->demands->removeElement($demand);
            // set the owning side to null (unless already changed)
            if ($demand->getOffer() === $this) {
                $demand->setOffer(null);
            }
        }

        return $this;
    }

    /**
     * @return Service
     */
    public function getService(): ?Service
    {
        return $this->service;
    }

    /**
     * @param Service $service
     */
    public function setService(?Service $service): self
    {
        $this->service = $service;
        $service->addOffer($this);

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
            $this->setUpdatedAt(new DateTime());
        }

        return $this;
    }
}
