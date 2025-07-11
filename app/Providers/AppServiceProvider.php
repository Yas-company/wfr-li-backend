<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Address;
use App\Policies\AddressPolicy;
use App\Services\Cart\CartService;
use Illuminate\Support\Facades\Gate;
use App\Http\Services\ProductService;
use App\Http\Services\SupplierService;
use App\Validators\EmptyCartValidator;
use Illuminate\Support\ServiceProvider;
use App\Contracts\CartValidatorInterface;
use App\Validators\CompositeCartValidator;
use App\Validators\MinOrderAmountValidator;
use App\Http\Services\Payment\PaymentService;
use App\Validators\StockAvailabilityValidator;
use App\Validators\SingleSupplierCartValidator;
use App\Services\Contracts\CartServiceInterface;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Http\Services\Contracts\PaymentServiceInterface;
use App\Http\Services\Contracts\ProductServiceInterface;
use App\Http\Services\Contracts\SupplierServiceInterface;
use App\Models\Order;
use App\Policies\OrderPolicy;

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

        $this->app->bind(CartValidatorInterface::class, fn () => new CompositeCartValidator(
            addToCartValidators: [
                new SingleSupplierCartValidator(),
                new StockAvailabilityValidator(),
            ],
            checkoutValidators: [
                new MinOrderAmountValidator(),
                new EmptyCartValidator(),
            ]
        ));

        Gate::policy(Address::class, AddressPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);


        Relation::morphMap([
            'user' => User::class
        ]);
    }
}
