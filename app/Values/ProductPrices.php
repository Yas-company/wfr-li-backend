<?php

namespace App\Values;


class ProductPrices
{
    public float $basePrice;
    public float $priceBeforeDiscount;
    public float $priceAfterDiscount;
    public float $priceAfterTaxes;
    public float $totalDiscount;

    public function __construct(
        float $basePrice,
        float $priceBeforeDiscount,
        float $priceAfterDiscount,
        float $priceAfterTaxes,
        float $totalDiscount
    )
    {
        $this->basePrice = $basePrice;
        $this->priceBeforeDiscount = $priceBeforeDiscount;
        $this->priceAfterDiscount = $priceAfterDiscount;
        $this->priceAfterTaxes = $priceAfterTaxes;
        $this->totalDiscount = $totalDiscount;
    }
}
