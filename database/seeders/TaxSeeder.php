<?php

namespace Database\Seeders;

use App\Models\Tax;
use App\Enums\Tax\TaxGroup;
use App\Enums\Tax\TaxApplyTo;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tax::create([
            'name' => 'Platform tax',
            'code' => 'platform',
            'rate' => config('app.taxes.platform'),
            'is_active' => true,
            'group' => TaxGroup::PLATFORM->value,
            'applies_to' => TaxApplyTo::PRODUCT->value,
        ]);

        Tax::create([
            'name' => 'Country vat tax',
            'code' => 'country_vat',
            'rate' => config('app.taxes.country_vat'),
            'is_active' => true,
            'group' => TaxGroup::COUNTRY_VAT->value,
            'applies_to' => TaxApplyTo::PRODUCT->value,
        ]);
    }
}
