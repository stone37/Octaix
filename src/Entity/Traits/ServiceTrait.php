<?php

namespace App\Entity\Traits;

use App\Entity\Achieve;
use App\Entity\Offer;
use App\Entity\Service;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Trait ServiceTrait
 * @package App\Entity\Traits
 */
trait ServiceTrait
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="100")
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $name = "";

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description = "";

    /**
     * @var @var Collection<int,Service>
     *
     * @ORM\OneToMany(targetEntity=Service::class, mappedBy="parent", cascade={"ALL"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $children = null;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"}, unique=true)
     *
     * @ORM\Column(type="string", length=100)
     */
    private $slug;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isHome = false;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"}, unique=true)
     * @Gedmo\SlugHandler(class="Gedmo\Sluggable\Handler\TreeSlugHandler")
     * @Gedmo\SlugHandlerOption(name="parentRelationField", value="parent")
     *
     * @ORM\Column(type="string", unique=true)
     */
    private $permalink;

    /**
     * @var Service
     *
     * @Gedmo\TreeParent()
     * @Gedmo\SortableGroup()
     *
     * @ORM\ManyToOne(targetEntity=Service::class, inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent = null;

    /**
     * @var File
     *
     * @Assert\File(maxSize="10M")
     *
     * @Vich\UploadableField(
     *     mapping="service",
     *     fileNameProperty="fileName",
     *     size="fileSize",
     *     mimeType="fileMimeType",
     *     originalName="fileOriginalName"
     * )
     */
    private $file;

    /**
     * @var @var Collection<int,Achieve>
     *
     * @ORM\OneToMany(targetEntity=Achieve::class, mappedBy="service")
     */
    private $achieves;

    /**
     * @var @var Collection<int,Offer>
     *
     * @ORM\OneToMany(targetEntity=Offer::class, mappedBy="service")
     */
    private $offers;

    public function __constructService()
    {
        $this->children = new ArrayCollection();
        $this->achieves = new ArrayCollection();
        $this->offers = new ArrayCollection();
    }

    /**
     * Set name
     *
     * @param null|string $name
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param null|string $slug
     */
    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Get description
     *
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param null|string $description
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Set permalink
     *
     * @param null|string $permalink
     */
    public function setPermalink(?string $permalink): self
    {
        $this->permalink = $permalink;

        return $this;
    }

    /**
     * Get permalink
     *
     * @return null|string
     */
    public function getPermalink(): ?string
    {
        return $this->permalink;
    }

    /**
     * Add child
     *
     * @param Service
     */
    public function addChildren(Service $children): self
    {
        if(!$this->hasChildren($children)) {
            $this->setParent($this);
            $this->children[] = $children;
        }

        return $this;
    }

    /**
     * Remove child
     *
     * @param Service $children
     */
    public function removeChildren(Service $children): self
    {
        if ($this->children->removeElement($children)) {
            $this->setParent(null);
        }

        return $this;
    }

    /**
     * @param Service $children
     *
     * @return bool
     */
    public function hasChildren(Service $children): ?bool
    {
        return $this->children->contains($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection|Service
     */
    public function getChildren(): ?Collection
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param Service $parent
     */
    public function setParent(?Service $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Service
     */
    public function getParent(): ?Service
    {
        return $this->parent;
    }

    /**
     * @return bool
     */
    public function isHome(): ?bool
    {
        return $this->isHome;
    }

    /**
     * @param bool $isHome
     */
    public function setIsHome(?bool $isHome): self
    {
        $this->isHome = $isHome;

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
     * Add achieve
     *
     * @param Achieve
     */
    public function addAchieve(Achieve $achieve): self
    {
        if(!$this->achieves->contains($achieve)) {
            $this->achieves[] = $achieve;
        }

        return $this;
    }

    public function removeAchieve(Achieve $achieve): self
    {
        if ($this->achieves->contains($achieve)) {
            $this->achieves->removeElement($achieve);
        }

        return $this;
    }

    public function getAchieves(): ?Collection
    {
        return $this->achieves;
    }

    /**
     * Add achieve
     *
     * @param Offer
     */
    public function addOffer(Offer $offer): self
    {
        if(!$this->offers->contains($offer)) {
            $this->offers[] = $offer;
        }

        return $this;
    }

    public function removeOffer(Offer $offer): self
    {
        if ($this->offers->contains($offer)) {
            $this->offers->removeElement($offer);
        }

        return $this;
    }

    public function getOffers(): ?Collection
    {
        return $this->offers;
    }

    /**
     * Return the service name
     *
     * @return string
     */
    public function __toString(): ?string
    {
        return (string) $this->getName();
    }

}


