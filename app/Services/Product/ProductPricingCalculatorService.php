<?php

namespace App\Services\Product;

use App\Models\Tax;
use App\Enums\Tax\TaxGroup;
use App\Values\ProductPrices;
use Illuminate\Database\Eloquent\Collection;

class ProductPricingCalculatorService
{
    protected Collection $taxes;

    public function calculate(float $basePrice, float $discountRate = 0): ProductPrices
    {
        $totalTaxes = 0;
        $discountRate = $this->normalizeDiscountRate($discountRate);

        $platformBefore = $this->calculateTaxes(TaxGroup::PLATFORM->value, $basePrice);
        $priceBeforeDiscount = $basePrice + $platformBefore;

        $totalDiscount = $priceBeforeDiscount * $discountRate;
        $priceAfterDiscount = $priceBeforeDiscount - $totalDiscount;

        $platformAfter = $platformBefore * (1 - $discountRate);

        $totalTaxes += $platformAfter;

        $country = $this->calculateTaxes(TaxGroup::COUNTRY_VAT->value, $priceAfterDiscount);
        $totalTaxes += $country;
        $priceAfterCountry = $priceAfterDiscount + $country;

        $other = $this->calculateTaxes(TaxGroup::OTHER->value, $priceAfterCountry);
        $totalTaxes += $other;
        $priceAfterTaxes = $priceAfterCountry + $other;

        return new ProductPrices(
            money($basePrice,2 ),
            money($priceBeforeDiscount, 2),
            money($priceAfterDiscount, 2),
            money($priceAfterTaxes, 2),
            money($totalDiscount, 2),
            money($platformAfter, 2),
            money($country, 2),
            money($other, 2),
            money($totalTaxes, 2)
        );
    }

    private function normalizeDiscountRate(float $discountRate): float
    {
        return min(max($discountRate, 0), 1.0);
    }

    private function calculateTaxes(string $group, float $price): float
    {
        return $price * $this->getGroupTaxesTotal($group);
    }

    private function getGroupTaxesTotal(string $group): float
    {
        $taxes = $this->getTaxes()->get($group);
        return $taxes?->sum('rate') ?? 0.0;
    }

    private function getTaxes(): Collection
    {
        if (! isset($this->taxes)) {
            $this->taxes = Tax::forProducts()->active()->get()->groupBy('group');
        }
        return $this->taxes;
    }
}
