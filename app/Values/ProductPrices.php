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
    public float $totalTaxes;

    public function __construct(
        float $basePrice,
        float $priceBeforeDiscount,
        float $priceAfterDiscount,
        float $priceAfterTaxes,
        float $totalDiscount,
        float $totalPlatformTax,
        float $totalCountryTax,
        float $totalOtherTax,
        float $totalTaxes
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
        $this->totalTaxes = $totalTaxes;
    }

    public function toArray(): array {
        return [
            'base_price' => money($this->basePrice, 2),
            'price_before_discount' => money($this->priceBeforeDiscount, 2),
            'price' => money($this->priceAfterDiscount, 2),
            'price_after_taxes' => money($this->priceAfterTaxes, 2),
            'total_discount' => money($this->totalDiscount, 2),
            'platform_tax' => money($this->totalPlatformTax, 2),
            'country_tax' => money($this->totalCountryTax, 2),
            'other_tax' => money($this->totalOtherTax, 1),
            'total_taxes' => money($this->totalTaxes, 2),
        ];
    }
}
