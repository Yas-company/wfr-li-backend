<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Services\Product\ProductPricingCalculatorService;

class UpdateProductsPricingCommand extends Command
{

    public function __construct(protected ProductPricingCalculatorService $productPricingCalculatorService)
    {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-products-pricing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command updates the pricing of all products.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Product::withoutSyncingToSearch(function()
        {
            Product::query()
                ->lazyById(500, 'id')
                ->each(function (Product $product) {
                    $productPrices = $this->productPricingCalculatorService->calculate($product->base_price, $product->discount_rate);
                    $product->update($productPrices->toArray());
                });
        });

        Artisan::call('scout:import', ['model' => Product::class]);
    }
}
