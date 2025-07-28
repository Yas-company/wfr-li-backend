<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Order;
use App\Models\Rating;
use App\Models\Address;
use App\Models\Product;
use App\Policies\OrderPolicy;
use App\Policies\RatingPolicy;
use App\Policies\AddressPolicy;
use App\Services\ProductService;
use App\Services\Cart\CartService;
use Illuminate\Support\Facades\Gate;
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
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Http\Services\Contracts\PaymentServiceInterface;
use App\Http\Services\Contracts\SupplierServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
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
        Gate::policy(Rating::class, RatingPolicy::class);


        Relation::morphMap([
            'user' => User::class,
            'order' => Order::class,
            'product' => Product::class,
        ]);
    }
}
