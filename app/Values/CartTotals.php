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
        public float $discount,
        public float $totalAfterTaxes,
        public float $totalCountryTax,
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
        $totalAfterTaxes = 0;
        $totalCountryTax = 0;

        foreach ($products as $item) {
            $productsSum += $item->quantity;
            $totalProducts++;
            $total += $item->quantity * $item->product->price;
            $totalBeforeDiscount += $item->quantity * ($item->product->price_before_discount ?? $item->product->price);
            $totalAfterTaxes += $item->quantity * $item->product->price_after_taxes;
            $totalCountryTax += $item->quantity * $item->product->country_tax;
        }

        return new static(
            totalProducts: $totalProducts,
            productsSum: $productsSum,
            total: money($total, 2),
            discount: money($totalBeforeDiscount - $total, 2),
            totalAfterTaxes: money($totalAfterTaxes, 2),
            totalCountryTax: money($totalCountryTax, 2)
        );
    }
}
