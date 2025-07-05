<?php

namespace App\Providers;

use App\Contracts\AddToCartValidatorInterface;
use App\Http\Services\Contracts\PaymentServiceInterface;
use App\Http\Services\Payment\PaymentService;
use App\Services\Cart\CartService;
use App\Http\Services\ProductService;
use App\Http\Services\SupplierService;
use Illuminate\Support\ServiceProvider;
use App\Validators\CompositeCartValidator;
use App\Validators\StockAvailabilityValidator;
use App\Validators\SingleSupplierCartValidator;
use App\Services\Contracts\CartServiceInterface;
use App\Http\Services\Contracts\ProductServiceInterface;
use App\Http\Services\Contracts\SupplierServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind(ProductServiceInterface::class, ProductService::class);
        $this->app->bind(CartServiceInterface::class, CartService::class);
        $this->app->bind(SupplierServiceInterface::class, SupplierService::class);
        $this->app->bind(PaymentServiceInterface::class, PaymentService::class);

        $this->app->bind(
            AddToCartValidatorInterface::class,
            fn () => new CompositeCartValidator([
                new SingleSupplierCartValidator(),
                new StockAvailabilityValidator(),
            ])
        );
    }
}
