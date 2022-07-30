<?php

namespace App\Entity;

use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PaymentRepository;

/**
 * @ORM\Entity(repositoryClass=PaymentRepository::class)
 */
class Payment
{
    const METHOD_CARD = 'cart';

    use IdTrait;
    use TimestampableTrait;
    use EnabledTrait;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $method = self::METHOD_CARD;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $price = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tax = 0;

    /**
     * @var Command
     *
     * @ORM\OneToOne(targetEntity=Command::class, inversedBy="payment")
     * @ORM\JoinColumn(nullable=false)
     */
    private $order;

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

        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method): self
    {
        $this->method = $method;

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
    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return int
     */
    public function getTax(): ?int
    {
        return $this->tax;
    }

    /**
     * @param int $tax
     */
    public function setTax(int $tax): self
    {
        $this->tax = $tax;

        return $this;
    }
}


