<?php

namespace App\Providers;

use App\Http\Services\Contracts\ProductServiceInterface;
use App\Http\Services\Contracts\SupplierServiceInterface;
use App\Http\Services\ProductService;
use App\Http\Services\SupplierService;
use Illuminate\Support\ServiceProvider;

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
        $this->app->bind(SupplierServiceInterface::class, SupplierService::class);

    }
}
