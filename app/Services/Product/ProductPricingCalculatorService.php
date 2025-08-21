<?php

namespace App\Services\Product;

use App\Models\Tax;
use App\Enums\Tax\TaxGroup;
use App\Values\ProductPrices;
use Illuminate\Database\Eloquent\Collection;

class ProductPricingCalculatorService
{
    protected Collection $taxes;

    public function __construct()
    {
        $this->taxes = Tax::forProducts()->active()->get()->groupBy('group');
    }

    public function calculate(float $basePrice, float $discountRate = 0): ProductPrices
    {
        $platformTaxes = $this->taxes->get(TaxGroup::PLATFORM->value);
        $countryTaxes = $this->taxes->get(TaxGroup::COUNTRY_VAT->value);
        $otherTaxes = $this->taxes->get(TaxGroup::OTHER->value);

        $platformTaxTotal = $platformTaxes?->sum('rate');
        $priceBeforeDiscount = $basePrice + ($basePrice * $platformTaxTotal);

        $totalDiscount = $priceBeforeDiscount * $discountRate;
        $priceAfterDiscount = $priceBeforeDiscount - $totalDiscount;

        $countryTaxRates = $countryTaxes?->sum('rate');
        $priceAfterCountryTaxes = $priceAfterDiscount + ($priceAfterDiscount * $countryTaxRates);

        $otherTaxRates = $otherTaxes?->sum('rate');
        $priceAfterTaxes = round($priceAfterCountryTaxes + ($priceAfterCountryTaxes * $otherTaxRates), 2);

        return new ProductPrices(
            $basePrice,
            $priceBeforeDiscount,
            $priceAfterDiscount,
            $priceAfterTaxes,
            $totalDiscount
        );
    }
}
