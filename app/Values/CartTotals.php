<?php

namespace App\Values;

class CartTotals
{
    public function __construct(
        public float $total,
        public float $discount
    ) {}

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
