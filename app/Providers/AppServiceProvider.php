<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\User;
use App\Models\Order;
use App\Models\Rating;
use App\Models\Address;
use App\Models\Product;
use App\Policies\OrderPolicy;
use App\Policies\RatingPolicy;
use App\Policies\AddressPolicy;
use App\Policies\ProductPolicy;
use App\Services\Cart\CartService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use App\Http\Services\SupplierService;
use App\Validators\EmptyCartValidator;
use Illuminate\Support\ServiceProvider;
use App\Services\Product\ProductService;
use App\Contracts\CartValidatorInterface;
use App\Enums\Payment\PaymentGateway;
use App\Validators\CompositeCartValidator;
use App\Validators\ProductStatusValidator;
use App\Services\Payment\TapPaymentService;
use App\Validators\MinOrderAmountValidator;
use App\Validators\StockAvailabilityValidator;
use App\Validators\SingleSupplierCartValidator;
use App\Services\Contracts\CartServiceInterface;
use App\Services\Contracts\PaymentGatewayInterface;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Database\Eloquent\Relations\Relation;
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
        $this->app->register(\L5Swagger\L5SwaggerServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind(ProductServiceInterface::class, ProductService::class);
        $this->app->bind(CartServiceInterface::class, CartService::class);
        $this->app->bind(SupplierServiceInterface::class, SupplierService::class);

        $this->app->bind(CartValidatorInterface::class, fn () => new CompositeCartValidator(
            addToCartValidators: [
                new SingleSupplierCartValidator(),
                new StockAvailabilityValidator(),
                new ProductStatusValidator(),
            ],
            checkoutValidators: [
                new EmptyCartValidator(),
                new ProductStatusValidator(),
                new StockAvailabilityValidator(),
                new SingleSupplierCartValidator(),
                new MinOrderAmountValidator(),
            ]
        ));

        $this->app->bind(PaymentGatewayInterface::class, function () {
            $gateway = config('services.payment.default_payment_gateway');

            return match ($gateway) {
                PaymentGateway::TAP->value => new TapPaymentService(),
                default => throw new \Exception('Unsupported payment gateway')
            };
        });

        Gate::policy(Address::class, AddressPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Rating::class, RatingPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);

        Relation::morphMap([
            'user' => User::class,
            'order' => Order::class,
            'product' => Product::class,
        ]);


        Route::bind('page', function(string $value) {
            return Page::query()
                ->isActive()
                ->where('slug', $value)
                ->firstOrFail();
        });
    }
}
