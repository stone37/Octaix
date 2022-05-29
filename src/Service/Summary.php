<?php

namespace App\Service;

use App\Entity\Command;

class Summary
{
    const DISCOUNT_PF = 'fixed';
    const DISCOUNT_PD = 'percent';

    /**
     * @var Command
     */
    private $order;

    /**
     * Summary constructor.
     *
     * @param Command $order
     */
    public function __construct(Command $order)
    {
        $this->order = $order;
    }

    /**
     * @return int
     */
    public function getItemsPriceTotal(): int
    {
        return $this->order->getPriceTotalTva();
    }

    /**
     * @return int
     */
    public function getPriceTotal(): int
    {
        return $this->order->getPriceTotal();
    }

    /**
     * @return int
     */
    public function getDiscount(): int
    {
        $price = 0;
        $discount = $this->order->getDiscount();

        if ($discount) {
            if ($discount->getType() === self::DISCOUNT_PF) {
                $price = ($this->getPriceItemsBeforeDiscount() - $discount->getDiscount());
            } elseif ($discount->getType() === self::DISCOUNT_PD) {
                $price = (($this->getPriceItemsBeforeDiscount() * $discount->getDiscount()) / 100);
            }
        }

        return $price;
    }

    /**
     * @return int
     */
    public function getPriceItemsBeforeDiscount(): int
    {
        $price = 0;
        foreach ($this->order->getItems() as $item) {
            $price += $item->getPriceTotal();
        }

        return $price;
    }
}