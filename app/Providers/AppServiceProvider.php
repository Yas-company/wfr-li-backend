<?php

namespace App\Providers;

<<<<<<< HEAD
use App\Services\Cart\CartService;
=======
use App\Http\Services\Contracts\ProductServiceInterface;
use App\Http\Services\Contracts\SupplierServiceInterface;
>>>>>>> 3e4d1f784d50d6ce667c04fa064ab43e44023cde
use App\Http\Services\ProductService;
use App\Http\Services\SupplierService;
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
<<<<<<< HEAD
        $this->app->bind(CartServiceInterface::class, CartService::class);
=======
        $this->app->bind(SupplierServiceInterface::class, SupplierService::class);

>>>>>>> 3e4d1f784d50d6ce667c04fa064ab43e44023cde
    }
}
