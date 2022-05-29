<?php

namespace App\Entity\Traits;

use App\Entity\OrderItem;
use App\Entity\Taxe;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Trait ProductTrait
 * @package App\Entity\Traits
 */
trait ProductTrait
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"}, unique=true)
     *
     * @ORM\Column(type="string", length=100)
     */
    private $slug;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $price;

    /**
     * @var OrderItem|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity=OrderItem::class, mappedBy="product")
     */
    private $orderItems;

    /**
     * @var Taxe
     *
     * @ORM\ManyToOne(targetEntity=Taxe::class)
     */
    private $taxe;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
    }

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
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return int
     */
    public function getPrice(): ?int
    {
        return $this->price;
    }

    /**
     * @param int $price
     */
    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return OrderItem|OrderItem[]|ArrayCollection
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $item): self
    {
        if (!$this->orderItems->contains($item)) {
            $this->orderItems[] = $item;
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $item): self
    {
        if ($this->orderItems->contains($item)) {
            $this->orderItems->removeElement($item);
        }

        return $this;
    }

    /**
     * @return Taxe
     */
    public function getTaxe(): ?Taxe
    {
        return $this->taxe;
    }

    /**
     * @param Taxe $taxe
     */
    public function setTaxe(?Taxe $taxe): self
    {
        $this->taxe = $taxe;

        return $this;
    }
}

