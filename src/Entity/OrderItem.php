<?php

namespace App\Entity;

use App\Entity\Traits\IdTrait;
use App\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderItemRepository::class)
 */
class OrderItem 
{
    use IdTrait;

    /**
     * @var Command
     *
     * @ORM\ManyToOne(targetEntity=Command::class, inversedBy="items")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $order;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="orderItems")
     * @ORM\JoinColumn(nullable=false)
     * @ORM\OrderBy({"createdAt": "desc"})
     */
    protected $product;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $quantity;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    protected $priceTotal = 0;

    /**
     * @return Command
     */
    public function getOrder(): ?Command
    {
        return $this->order;
    }

    /**
     * @param Command $order
     */
    public function setOrder(Command $order): self
    {
        $this->order = $order;
        $order->addItem($this);

        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return float
     */
    public function getPriceTotal(): ?float
    {
        return $this->priceTotal;
    }

    /**
     * @param float $priceTotal
     */
    public function setPriceTotal(?float $priceTotal): self
    {
        $this->priceTotal = $priceTotal;

        return $this;
    }
}
