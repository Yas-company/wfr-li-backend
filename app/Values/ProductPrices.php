<?php

namespace App\Values;


class ProductPrices
{
    public float $basePrice;
    public float $priceBeforeDiscount;
    public float $priceAfterDiscount;
    public float $priceAfterTaxes;
    public float $totalDiscount;
    public float $totalPlatformTax;
    public float $totalCountryTax;
    public float $totalOtherTax;

    public function __construct(
        float $basePrice,
        float $priceBeforeDiscount,
        float $priceAfterDiscount,
        float $priceAfterTaxes,
        float $totalDiscount,
        float $totalPlatformTax,
        float $totalCountryTax,
        float $totalOtherTax
    )
    {
        $this->basePrice = $basePrice;
        $this->priceBeforeDiscount = $priceBeforeDiscount;
        $this->priceAfterDiscount = $priceAfterDiscount;
        $this->priceAfterTaxes = $priceAfterTaxes;
        $this->totalDiscount = $totalDiscount;
        $this->totalPlatformTax = $totalPlatformTax;
        $this->totalCountryTax = $totalCountryTax;
        $this->totalOtherTax = $totalOtherTax;
    }

    public function toArray(): array {
        return [
            'base_price' => $this->basePrice,
            'price_before_discount' => $this->priceBeforeDiscount,
            'price' => $this->priceAfterDiscount,
            'price_after_taxes' => $this->priceAfterTaxes,
            'total_discount' => $this->totalDiscount,
            'platform_tax' => $this->totalPlatformTax,
            'country_tax' => $this->totalCountryTax,
            'other_tax' => $this->totalOtherTax,
        ];
    }
}
