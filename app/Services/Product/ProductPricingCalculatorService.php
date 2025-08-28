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
        $discountRate = $this->normalizeDiscountRate($discountRate);

        $platformBefore = $this->calculateTaxes(TaxGroup::PLATFORM->value, $basePrice);
        $priceBeforeDiscount = $basePrice + $platformBefore;

        $totalDiscount = $priceBeforeDiscount * $discountRate;
        $priceAfterDiscount = $priceBeforeDiscount - $totalDiscount;

        $platformAfter = $platformBefore * (1 - $discountRate);
        $country = $this->calculateTaxes(TaxGroup::COUNTRY_VAT->value, $priceAfterDiscount);
        $priceAfterCountry = $priceAfterDiscount + $country;

        $other = $this->calculateTaxes(TaxGroup::OTHER->value, $priceAfterCountry);
        $priceAfterTaxes = $priceAfterCountry + $other;

        return new ProductPrices(
            $this->money($basePrice),
            $this->money($priceBeforeDiscount),
            $this->money($priceAfterDiscount),
            $this->money($priceAfterTaxes),
            $this->money($totalDiscount),
            $this->money($platformAfter),
            $this->money($country),
            $this->money($other)
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

    private function money(float $value): float
    {
        return round($value, 2);
    }
}
