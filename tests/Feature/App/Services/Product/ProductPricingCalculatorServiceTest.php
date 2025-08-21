<?php

namespace Tests\Unit\Product;

use App\Models\Tax;
use Tests\TestCase;
use App\Enums\Tax\TaxGroup;
use App\Enums\Tax\TaxApplyTo;
use App\Values\ProductPrices;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\Product\ProductPricingCalculatorService;

class ProductPricingCalculatorServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_it_calculates_final_price_correctly_with_multiple_taxes_and_discount()
    {
        $basePrice = 100.0;
        $discountRate = 0.1; // 10%

        // Platform Tax: 5%
        Tax::create([
            'name' => 'Platform Tax',
            'code' => 'platform',
            'group' => TaxGroup::PLATFORM->value,
            'applies_to' => TaxApplyTo::PRODUCT->value,
            'rate' => 0.05,
            'is_active' => true,
        ]);

        // Country VAT: 15%
        Tax::create([
            'name' => 'Country VAT',
            'code' => 'country_vat',
            'group' => TaxGroup::COUNTRY_VAT->value,
            'applies_to' => TaxApplyTo::PRODUCT->value,
            'rate' => 0.15,
            'is_active' => true,
        ]);

        $calculator = new ProductPricingCalculatorService;

        $result = $calculator->calculate($basePrice, $discountRate);

        $this->assertInstanceOf(ProductPrices::class, $result);
        $this->assertEquals(105.0, $result->priceBeforeDiscount);
        $this->assertEquals(10.5, $result->totalDiscount);
        $this->assertEquals(94.5, $result->priceAfterDiscount);
        $this->assertEquals(108.68, $result->priceAfterTaxes);
    }

    public function test_it_calculates_correctly_with_no_discount()
    {
        $basePrice = 200.0;
        $discountRate = 0.0;

        Tax::create([
            'name' => 'Platform Tax',
            'code' => 'platform',
            'group' => TaxGroup::PLATFORM->value,
            'applies_to' => TaxApplyTo::PRODUCT->value,
            'rate' => 0.05,
            'is_active' => true,
        ]);

        Tax::create([
            'name' => 'Country VAT',
            'code' => 'country_vat',
            'group' => TaxGroup::COUNTRY_VAT->value,
            'applies_to' => TaxApplyTo::PRODUCT->value,
            'rate' => 0.15,
            'is_active' => true,
        ]);

        $calculator = new ProductPricingCalculatorService();

        $result = $calculator->calculate($basePrice, $discountRate);

        $this->assertEquals(210.0, $result->priceBeforeDiscount);
        $this->assertEquals(0.0, $result->totalDiscount);
        $this->assertEquals(210.0, $result->priceAfterDiscount);
        $this->assertEquals(241.5, $result->priceAfterTaxes);
    }

    public function test_it_calculates_correctly_with_multiple_taxes_in_same_group()
    {
        $basePrice = 100.0;
        $discountRate = 0.0;

        // Two platform taxes: 3% + 2% = 5%
        Tax::create([
            'name' => 'Platform Tax 2',
            'code' => 'platform_1',
            'group' => TaxGroup::PLATFORM->value,
            'applies_to' => TaxApplyTo::PRODUCT->value,
            'rate' => 0.03,
            'is_active' => true
        ]);
        Tax::create([
            'name' => 'Platform Tax 2',
            'code' => 'platform_2',
            'group' => TaxGroup::PLATFORM->value,
            'applies_to' => TaxApplyTo::PRODUCT->value,
            'rate' => 0.02,
            'is_active' => true
        ]);

        // Two country taxes: 10% + 5% = 15%
        Tax::create([
            'name' => 'Country VAT 1',
            'code' => 'country_vat_1',
            'group' => TaxGroup::COUNTRY_VAT->value,
            'applies_to' => TaxApplyTo::PRODUCT->value,
            'rate' => 0.10,
            'is_active' => true
        ]);
        Tax::create([
            'name' => 'Country VAT 2',
            'code' => 'country_vat_2',
            'group' => TaxGroup::COUNTRY_VAT->value,
            'applies_to' => TaxApplyTo::PRODUCT->value,
            'rate' => 0.05,
            'is_active' => true
        ]);

        $calculator = new ProductPricingCalculatorService();

        $result = $calculator->calculate($basePrice, $discountRate);

        $this->assertEquals(105.0, $result->priceBeforeDiscount);
        $this->assertEquals(0.0, $result->totalDiscount);
        $this->assertEquals(105.0, $result->priceAfterDiscount);
        $this->assertEquals(120.75, $result->priceAfterTaxes);

    }

    public function test_it_handles_no_taxes_gracefully()
    {
        $basePrice = 100.0;
        $discountRate = 0.1;

        $calculator = new ProductPricingCalculatorService();

        $result = $calculator->calculate($basePrice, $discountRate);

        $this->assertEquals(100.0, $result->priceBeforeDiscount);
        $this->assertEquals(10.0, $result->totalDiscount);
        $this->assertEquals(90.0, $result->priceAfterDiscount);
        $this->assertEquals(90.0, $result->priceAfterTaxes);
    }

    public function test_it_ignores_inactive_taxes()
    {
        $basePrice = 100.0;

        // Platform Tax: 5%
        Tax::create([
            'name' => 'Platform Tax',
            'code' => 'platform',
            'group' => TaxGroup::PLATFORM->value,
            'applies_to' => TaxApplyTo::PRODUCT->value,
            'rate' => 0.05,
            'is_active' => false,
        ]);

        // Country VAT: 15%
        Tax::create([
            'name' => 'Country VAT',
            'code' => 'country_vat',
            'group' => TaxGroup::COUNTRY_VAT->value,
            'applies_to' => TaxApplyTo::PRODUCT->value,
            'rate' => 0.15,
            'is_active' => false,
        ]);


        $calculator = new ProductPricingCalculatorService();

        $result = $calculator->calculate($basePrice);

        $this->assertEquals(100.0, $result->priceBeforeDiscount);
        $this->assertEquals(0.0, $result->totalDiscount);
        $this->assertEquals(100.0, $result->priceAfterDiscount);
        $this->assertEquals(100.0, $result->priceAfterTaxes);
    }
}
