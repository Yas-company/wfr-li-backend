<?php

namespace App\Providers;

use App\Services\Cart\CartService;
use App\Http\Services\ProductService;
use Illuminate\Support\ServiceProvider;
use App\Services\Contracts\CartServiceInterface;
use App\Http\Services\Contracts\ProductServiceInterface;

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
    }
}
