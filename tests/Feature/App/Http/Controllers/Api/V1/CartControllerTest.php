<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1;

use App\Enums\Order\OrderType;
use App\Enums\Order\PaymentMethod;
use App\Enums\Order\ShippingMethod;
use App\Enums\ProductStatus;
use App\Enums\Settings\OrderSettings;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $buyer;

    protected User $supplier;

    protected Address $shippingAddress;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buyer = $this->createUser(UserRole::BUYER);
        $this->supplier = $this->createUser(UserRole::SUPPLIER);
        $this->shippingAddress = Address::factory()->create(['user_id' => $this->buyer->id]);
    }

    public function test_buyer_cannot_checkout_when_total_is_less_than_min_order_amount()
    {
        // Set minimum order amount for supplier
        $minOrderAmount = 100.00;
        $this->setSupplierMinOrderAmount($this->supplier, $minOrderAmount);

        // Create a product with low price
        $product = $this->createProduct($this->supplier, [
            'price' => 30.00,
            'stock_qty' => 10,
        ]);

        // Add product to cart (total will be 60.00, less than min 100.00)
        $this->addProductToCart($this->buyer, $product, 2);

        // Attempt to checkout
        $response = $this->actingAs($this->buyer)->postJson(route('cart.checkout'), [
            'shipping_address_id' => $this->shippingAddress->id,
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY->value,
            'shipping_method' => ShippingMethod::DELEGATE->value,
            'order_type' => OrderType::INDIVIDUAL->value,
            'notes' => 'Test order',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonFragment([
                'message' => __('messages.cart.insufficient_min_order_amount', [
                    'supplier_name' => $this->supplier->name,
                    'min_order_amount' => $minOrderAmount,
                ]),
            ]);
    }

    public function test_buyer_can_checkout_when_total_meets_min_order_amount()
    {
        // Set minimum order amount for supplier
        $minOrderAmount = 100.00;
        $this->setSupplierMinOrderAmount($this->supplier, $minOrderAmount);

        // Create a product with sufficient price
        $product = $this->createProduct($this->supplier, [
            'price' => 50.00,
            'stock_qty' => 10,
        ]);

        // Add product to cart (total will be 100.00, equal to min)
        $this->addProductToCart($this->buyer, $product, 2);

        // Attempt to checkout
        $response = $this->actingAs($this->buyer)->postJson(route('cart.checkout'), [
            'shipping_address_id' => $this->shippingAddress->id,
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY->value,
            'shipping_method' => ShippingMethod::DELEGATE->value,
            'order_type' => OrderType::INDIVIDUAL->value,
            'notes' => 'Test order',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'order' => [
                        'id',
                        'status',
                        'total',
                    ],
                ],
            ]);
    }

    public function test_buyer_can_checkout_when_total_exceeds_min_order_amount()
    {
        // Set minimum order amount for supplier
        $minOrderAmount = 100.00;
        $this->setSupplierMinOrderAmount($this->supplier, $minOrderAmount);

        // Create a product with sufficient price
        $product = $this->createProduct($this->supplier, [
            'price' => 60.00,
            'stock_qty' => 10,
        ]);

        // Add product to cart (total will be 120.00, more than min)
        $this->addProductToCart($this->buyer, $product, 2);

        // Attempt to checkout
        $response = $this->actingAs($this->buyer)->postJson(route('cart.checkout'), [
            'shipping_address_id' => $this->shippingAddress->id,
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY->value,
            'shipping_method' => ShippingMethod::DELEGATE->value,
            'order_type' => OrderType::INDIVIDUAL->value,
            'notes' => 'Test order',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'order' => [
                        'id',
                        'status',
                        'total',
                    ],
                ],
            ]);
    }

    public function test_buyer_cannot_checkout_with_multiple_suppliers_when_one_doesnt_meet_min_order_amount()
    {
        // Create another supplier
        $supplier2 = $this->createUser(UserRole::SUPPLIER);

        // Set minimum order amounts for both suppliers
        $minOrderAmount1 = 100.00;
        $minOrderAmount2 = 150.00;
        $this->setSupplierMinOrderAmount($this->supplier, $minOrderAmount1);
        $this->setSupplierMinOrderAmount($supplier2, $minOrderAmount2);

        // Create products from both suppliers
        $product1 = $this->createProduct($this->supplier, [
            'price' => 50.00,
            'stock_qty' => 10,
        ]);

        $product2 = $this->createProduct($supplier2, [
            'price' => 40.00,
            'stock_qty' => 10,
        ]);

        // Add products to cart
        // Supplier 1: 50 * 2 = 100 (meets minimum)
        // Supplier 2: 40 * 2 = 80 (doesn't meet minimum of 150)
        $this->addProductToCart($this->buyer, $product1, 2);
        $this->addProductToCart($this->buyer, $product2, 2);

        // Attempt to checkout
        $response = $this->actingAs($this->buyer)->postJson(route('cart.checkout'), [
            'shipping_address_id' => $this->shippingAddress->id,
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY->value,
            'shipping_method' => ShippingMethod::DELEGATE->value,
            'order_type' => OrderType::INDIVIDUAL->value,
            'notes' => 'Test order',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonFragment([
                'message' => __('messages.cart.insufficient_min_order_amount', [
                    'supplier_name' => $supplier2->name,
                    'min_order_amount' => $minOrderAmount2,
                ]),
            ]);
    }

    public function test_buyer_can_checkout_with_multiple_suppliers_when_all_meet_min_order_amount()
    {
        // Create another supplier
        $supplier2 = $this->createUser(UserRole::SUPPLIER);

        // Set minimum order amounts for both suppliers
        $minOrderAmount1 = 100.00;
        $minOrderAmount2 = 80.00;
        $this->setSupplierMinOrderAmount($this->supplier, $minOrderAmount1);
        $this->setSupplierMinOrderAmount($supplier2, $minOrderAmount2);

        // Create products from both suppliers
        $product1 = $this->createProduct($this->supplier, [
            'price' => 50.00,
            'stock_qty' => 10,
        ]);

        $product2 = $this->createProduct($supplier2, [
            'price' => 40.00,
            'stock_qty' => 10,
        ]);

        // Add products to cart
        // Supplier 1: 50 * 2 = 100 (meets minimum)
        // Supplier 2: 40 * 2 = 80 (meets minimum)
        $this->addProductToCart($this->buyer, $product1, 2);
        $this->addProductToCart($this->buyer, $product2, 2);

        // Attempt to checkout
        $response = $this->actingAs($this->buyer)->postJson(route('cart.checkout'), [
            'shipping_address_id' => $this->shippingAddress->id,
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY->value,
            'shipping_method' => ShippingMethod::DELEGATE->value,
            'order_type' => OrderType::INDIVIDUAL->value,
            'notes' => 'Test order',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_buyer_can_checkout_when_supplier_has_no_min_order_amount_set()
    {
        // Don't set any minimum order amount for supplier (defaults to 0)

        // Create a product with any price
        $product = $this->createProduct($this->supplier, [
            'price' => 10.00,
            'stock_qty' => 10,
        ]);

        // Add product to cart with small quantity
        $this->addProductToCart($this->buyer, $product, 1);

        // Attempt to checkout
        $response = $this->actingAs($this->buyer)->postJson(route('cart.checkout'), [
            'shipping_address_id' => $this->shippingAddress->id,
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY->value,
            'shipping_method' => ShippingMethod::DELEGATE->value,
            'order_type' => OrderType::INDIVIDUAL->value,
            'notes' => 'Test order',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_buyer_cannot_checkout_when_cart_total_is_exactly_one_cent_below_min_order_amount()
    {
        // Set minimum order amount for supplier
        $minOrderAmount = 100.00;
        $this->setSupplierMinOrderAmount($this->supplier, $minOrderAmount);

        // Create a product that will result in total just below minimum
        $product = $this->createProduct($this->supplier, [
            'price' => 49.99,
            'stock_qty' => 10,
        ]);

        // Add product to cart (total will be 99.98, just below min)
        $this->addProductToCart($this->buyer, $product, 2);

        // Attempt to checkout
        $response = $this->actingAs($this->buyer)->postJson(route('cart.checkout'), [
            'shipping_address_id' => $this->shippingAddress->id,
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY->value,
            'shipping_method' => ShippingMethod::DELEGATE->value,
            'order_type' => OrderType::INDIVIDUAL->value,
            'notes' => 'Test order',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonFragment([
                'message' => __('messages.cart.insufficient_min_order_amount', [
                    'supplier_name' => $this->supplier->name,
                    'min_order_amount' => $minOrderAmount,
                ]),
            ]);
    }

    // Helper methods

    protected function createUser(UserRole $role, array $attributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'role' => $role,
            'status' => UserStatus::APPROVED,
            'is_verified' => true,
        ], $attributes));

        // Create associated address
        Address::factory()->create(['user_id' => $user->id]);

        return $user;
    }

    protected function createProduct(User $supplier, array $attributes = []): Product
    {
        return Product::factory()->create(array_merge([
            'supplier_id' => $supplier->id,
            'status' => ProductStatus::PUBLISHED,
            'is_active' => true,
        ], $attributes));
    }

    protected function addProductToCart(User $buyer, Product $product, int $quantity): void
    {
        $cart = Cart::firstOrCreate(['user_id' => $buyer->id]);

        CartProduct::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $product->price,
        ]);
    }

    protected function setSupplierMinOrderAmount(User $supplier, float $amount): void
    {
        Setting::updateOrCreate(
            [
                'user_id' => $supplier->id,
                'key' => OrderSettings::ORDER_MIN_ORDER_AMOUNT->value,
            ],
            [
                'value' => $amount,
            ]
        );
    }
}
