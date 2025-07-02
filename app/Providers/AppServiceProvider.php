<?php

namespace App\Providers;

use App\Http\Services\Contracts\OrderServiceInterface;
use App\Http\Services\Implementations\OrderService;
use App\Services\Cart\CartService;
use App\Http\Services\Contracts\ProductServiceInterface;
use App\Http\Services\Contracts\SupplierServiceInterface;
use App\Http\Services\ProductService;
use App\Http\Services\SupplierService;
use Illuminate\Support\ServiceProvider;
use App\Services\Contracts\CartServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /*
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
        $this->app->bind(OrderServiceInterface::class, OrderService::class);


    }
}
