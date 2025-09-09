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
        public int $totalProducts,
        public int $productsSum,
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
        $totalProducts = 0;
        $productsSum = 0;
        $total = 0;
        $totalBeforeDiscount = 0;

        foreach ($products as $item) {
            $productsSum += $item->quantity;
            $totalProducts++;
            $total += $item->quantity * $item->price;
            $totalBeforeDiscount += $item->quantity * ($item->product->price_before_discount ?? $item->price);
        }

        return new static(
            totalProducts: $totalProducts,
            productsSum: $productsSum,
            total: $total,
            discount: $totalBeforeDiscount - $total
        );
    }
}
