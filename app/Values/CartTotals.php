<?php

namespace App\Values;

class CartTotals
{
    /**
     * CartTotals constructor.
     *
     * @param float $total
     * @param float $discount
     *
     */
    public function __construct(
        public float $total,
        public float $discount
    ) {}

    /**
     * Create a new CartTotals instance from an iterable of Product instances.
     *
     * @param iterable<\App\Entities\Product> $products
     *
     * @return self
     */
    public static function fromProducts(iterable $products): self
    {
        $total = 0;
        $totalBeforeDiscount = 0;

        foreach ($products as $item) {
            $total += $item->quantity * $item->price;
            $totalBeforeDiscount += $item->quantity * ($item->product->price_before_discount ?? $item->price);
        }

        return new static(
            total: $total,
            discount: $totalBeforeDiscount - $total
        );
    }
}
