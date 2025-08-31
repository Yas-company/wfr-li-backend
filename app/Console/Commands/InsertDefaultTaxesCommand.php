<?php

namespace App\Console\Commands;

use App\Models\Tax;
use App\Enums\Tax\TaxGroup;
use App\Enums\Tax\TaxApplyTo;
use Illuminate\Console\Command;

class InsertDefaultTaxesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prod:insert-default-taxes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert default taxes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Tax::firstOrCreate([
            'name' => 'Platform tax',
            'code' => 'platform',
            'rate' => config('app.taxes.platform'),
            'is_active' => true,
            'group' => TaxGroup::PLATFORM->value,
            'applies_to' => TaxApplyTo::PRODUCT->value,
        ]);

        Tax::firstOrCreate([
            'name' => 'Country vat tax',
            'code' => 'country_vat',
            'rate' => config('app.taxes.country_vat'),
            'is_active' => true,
            'group' => TaxGroup::COUNTRY_VAT->value,
            'applies_to' => TaxApplyTo::PRODUCT->value,
        ]);
    }
}
