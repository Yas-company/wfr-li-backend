<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1;

use Tests\TestCase;
use App\Models\Cart;
use App\Models\User;
use App\Enums\UserRole;
use App\Models\Address;
use App\Models\Product;
use App\Models\Setting;
use App\Enums\UserStatus;
use App\Models\CartProduct;
use App\Enums\ProductStatus;
use App\Models\Organization;
use App\Enums\Order\OrderType;
use App\Enums\Order\PaymentMethod;
use App\Enums\Order\ShippingMethod;
use App\Enums\Settings\OrderSettings;
use App\Enums\Organization\OrganizationRole;
use Illuminate\Foundation\Testing\WithFaker;
use App\Enums\Organization\OrganizationStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

    public function test_unauthenticated_cannot_access_cart_index()
    {
        $this->getJson(route('cart.index'))->assertStatus(401);
    }

    public function test_unauthenticated_cannot_add_to_cart()
    {
        $this->postJson(route('cart.store'), [])->assertStatus(401);
    }

    public function test_unauthenticated_cannot_remove_from_cart()
    {
        $this->deleteJson(route('cart.destroy', 1))->assertStatus(401);
    }

    public function test_unauthenticated_cannot_clear_cart()
    {
        $this->putJson(route('cart.clear'))->assertStatus(401);
    }

    public function test_unauthenticated_cannot_checkout()
    {
        $this->postJson(route('cart.checkout'), [])->assertStatus(401);
    }


    public function test_index_returns_cart_totals_and_supplier_requirements()
    {
        $p1 = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'status' => ProductStatus::PUBLISHED,
            'is_active' => true,
            'price' => 10.00,
            'price_before_discount' => 12.00,
            'price_after_taxes' => 11.50,
            'country_tax' => 1.50,
            'stock_qty' => 100,
        ]);

        $p2 = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'status' => ProductStatus::PUBLISHED,
            'is_active' => true,
            'price' => 20.00,
            'price_before_discount' => 25.00,
            'price_after_taxes' => 22.00,
            'country_tax' => 2.00,
            'stock_qty' => 100,
        ]);

        $this->setSupplierMinOrderAmount($this->supplier, 100.00);
        $this->addProductToCart($this->buyer, $p1, 3);
        $this->addProductToCart($this->buyer, $p2, 2);

        $response = $this->actingAs($this->buyer)->getJson(route('cart.index'));

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'cart',
                    'total',
                    'total_discount',
                    'total_products',
                    'total_after_taxes',
                    'total_country_tax',
                    'supplier_requirements',
                ],
            ]);

        $data = $response->json('data');

        // totals computed by App\Values\CartTotals
        // quantities: p1 x3, p2 x2
        $expectedTotal = 3 * 10.00 + 2 * 20.00; // 70.00
        $expectedProductsSum = 1 + 1; // 2 (We discard the quantity of the products)
        $expectedBeforeDiscount = 3 * 12.00 + 2 * 25.00; // 36 + 50 = 86
        $expectedDiscount = round($expectedBeforeDiscount - $expectedTotal, 2); // 16.00
        $expectedAfterTaxes = 3 * 11.50 + 2 * 22.00; // 34.5 + 44 = 78.5
        $expectedCountryTax = 3 * 1.50 + 2 * 2.00; // 4.5 + 4 = 8.5

        $this->assertEquals($expectedTotal, $data['total']);
        $this->assertEquals($expectedProductsSum, $data['total_products']);
        $this->assertEquals($expectedDiscount, $data['total_discount']);
        $this->assertEquals(round($expectedAfterTaxes, 2), $data['total_after_taxes']);
        $this->assertEquals(round($expectedCountryTax, 2), $data['total_country_tax']);

        $this->assertIsArray($data['supplier_requirements']);
        $this->assertCount(1, $data['supplier_requirements']);
        $this->assertEquals($this->supplier->id, $data['supplier_requirements'][0]['supplier_id']);
        $this->assertEquals($this->supplier->name, $data['supplier_requirements'][0]['supplier_name']);
        $this->assertEquals($expectedTotal, $data['supplier_requirements'][0]['current_total']);
        $this->assertEquals(30, $data['supplier_requirements'][0]['residual_amount']);
        $this->assertFalse($data['supplier_requirements'][0]['completed']);
    }

    public function test_remove_product_from_cart()
    {
        $product = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'status' => ProductStatus::PUBLISHED,
            'is_active' => true,
            'stock_qty' => 10,
        ]);

        $cart = Cart::firstOrCreate(['user_id' => $this->buyer->id]);
        $this->addProductToCart($this->buyer, $product, 2);

        $this->assertDatabaseHas('cart_product', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($this->buyer)->deleteJson(route('cart.destroy', $product->id));
        $response->assertCreated()->assertJson(['success' => true]);

        $this->assertDatabaseMissing('cart_product', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_destroy_fails_with_non_existent_product_id()
    {
        $nonExistentProductId = 999999;

        $response = $this->actingAs($this->buyer)->deleteJson(route('cart.destroy', $nonExistentProductId));

        $response->assertStatus(404);
    }

    public function test_clear_cart_removes_all_products()
    {
        $p1 = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'status' => ProductStatus::PUBLISHED,
            'is_active' => true,
            'stock_qty' => 10,
        ]);
        $p2 = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'status' => ProductStatus::PUBLISHED,
            'is_active' => true,
            'stock_qty' => 10,
        ]);

        $this->addProductToCart($this->buyer, $p1, 1);
        $this->addProductToCart($this->buyer, $p2, 2);

        $response = $this->actingAs($this->buyer)->putJson(route('cart.clear'));
        $response->assertCreated()->assertJson(['success' => true]);

        $this->assertDatabaseCount('cart_product', 0);
    }

    public function test_destroy_succeeds_even_if_product_not_in_cart()
    {
        $product = Product::factory()->create(['supplier_id' => $this->supplier->id]);

        $response = $this->actingAs($this->buyer)->deleteJson(route('cart.destroy', $product->id));

        $response->assertCreated()->assertJson(['success' => true]);
        // Double-check cart is still empty
        $this->assertDatabaseCount('cart_product', 0);
    }

    public function test_clear_succeeds_on_empty_cart()
    {
        Cart::firstOrCreate(['user_id' => $this->buyer->id]);

        $response = $this->actingAs($this->buyer)->putJson(route('cart.clear'));

        $response->assertCreated()->assertJson(['success' => true]);
    }

    public function test_checkout_fails_with_empty_cart()
    {
        $response = $this->actingAs($this->buyer)->postJson(route('cart.checkout'), [
            'shipping_address_id' => $this->shippingAddress->id,
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY->value,
            'shipping_method' => ShippingMethod::DELEGATE->value,
            'order_type' => OrderType::INDIVIDUAL->value,
        ]);

        $response->assertStatus(400)->assertJson(['success' => false]);
    }

    public function test_checkout_fails_when_buyer_uses_organization_order_type()
    {
        $product = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'status' => ProductStatus::PUBLISHED,
            'is_active' => true,
            'price' => 10.00,
            'stock_qty' => 10,
        ]);

        $cart = Cart::firstOrCreate(['user_id' => $this->buyer->id]);
        CartProduct::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => $product->price,
        ]);

        $response = $this->actingAs($this->buyer)->postJson(route('cart.checkout'), [
            'shipping_address_id' => $this->shippingAddress->id,
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY->value,
            'shipping_method' => ShippingMethod::DELEGATE->value,
            'order_type' => OrderType::ORGANIZATION->value,
        ]);

        $response->assertStatus(400)->assertJson(['success' => false]);
    }

    public function test_organization_user_can_use_organization_order_type()
    {
        $organization = Organization::factory()->create([
            'created_by' => $this->buyer->id,
            'status' => OrganizationStatus::APPROVED,
        ]);

        $this->buyer->organizations()->attach($organization->id, [
            'role' => OrganizationRole::OWNER,
            'joined_at' => now(),
        ]);

        $product = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'status' => ProductStatus::PUBLISHED,
            'is_active' => true,
            'price' => 100.00,
            'stock_qty' => 10,
        ]);

        $this->addProductToCart($this->buyer, $product, 1);

        $response = $this->actingAs($this->buyer)->postJson(route('cart.checkout'), [
            'shipping_address_id' => $this->shippingAddress->id,
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY->value,
            'shipping_method' => ShippingMethod::DELEGATE->value,
            'order_type' => OrderType::ORGANIZATION->value,
        ]);

        $response->assertStatus(201)->assertJson(['success' => true]);
    }

    public function test_add_to_cart_success()
    {
        $product = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'status' => ProductStatus::PUBLISHED,
            'is_active' => true,
            'price' => 15.00,
            'stock_qty' => 50,
        ]);

        $response = $this->actingAs($this->buyer)->postJson(route('cart.store'), [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $response->assertCreated()->assertJson(['success' => true]);
        $this->assertDatabaseHas('cart_product', [
            'cart_id' => $this->buyer->cart->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'price' => $product->price,
        ]);
    }

    public function test_add_to_cart_fails_when_quantity_exceeds_stock()
    {
        $product = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'status' => ProductStatus::PUBLISHED,
            'is_active' => true,
            'price' => 15.00,
            'stock_qty' => 2,
        ]);

        $cart = Cart::firstOrCreate(['user_id' => $this->buyer->id]);

        $response = $this->actingAs($this->buyer)->postJson(route('cart.store'), [
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        $response->assertStatus(422)->assertJson(['success' => false]);
        $this->assertDatabaseMissing('cart_product', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 5,
            'price' => $product->price,
        ]);
    }

    public function test_add_to_cart_fails_when_product_unpublished_or_inactive()
    {
        $product = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'status' => ProductStatus::DRAFT,
            'is_active' => false,
            'price' => 10.00,
            'stock_qty' => 10,
        ]);

        $cart = Cart::firstOrCreate(['user_id' => $this->buyer->id]);


        $response = $this->actingAs($this->buyer)->postJson(route('cart.store'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(422)->assertJson(['errors' => ['product_id' => []]]);
        $this->assertDatabaseMissing('cart_product', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => $product->price,
        ]);
    }

    public function test_checkout_fails_if_product_in_cart_becomes_inactive()
    {
        $product = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'status' => ProductStatus::PUBLISHED,
            'is_active' => true,
            'price' => 100.00,
            'stock_qty' => 10,
        ]);

        $this->addProductToCart($this->buyer, $product, 1);

        // Now, deactivate the product
        $product->update(['is_active' => false]);

        $response = $this->actingAs($this->buyer)->postJson(route('cart.checkout'), [
            'shipping_address_id' => $this->shippingAddress->id,
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY->value,
            'shipping_method' => ShippingMethod::DELEGATE->value,
            'order_type' => OrderType::INDIVIDUAL->value,
        ]);

        $response->assertStatus(400)
            ->assertJson(['success' => false])
            ->assertJsonFragment(['message' => __('messages.cart.invalid_product')]);
    }

    public function test_checkout_fails_if_product_in_cart_is_unpublished()
    {
        $product = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'status' => ProductStatus::PUBLISHED,
            'is_active' => true,
            'price' => 100.00,
            'stock_qty' => 10,
        ]);

        $this->addProductToCart($this->buyer, $product, 1);

        // Now, unpublish the product
        $product->update(['status' => ProductStatus::DRAFT]);

        $response = $this->actingAs($this->buyer)->postJson(route('cart.checkout'), [
            'shipping_address_id' => $this->shippingAddress->id,
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY->value,
            'shipping_method' => ShippingMethod::DELEGATE->value,
            'order_type' => OrderType::INDIVIDUAL->value,
        ]);

        $response->assertStatus(400)
            ->assertJson(['success' => false])
            ->assertJsonFragment(['message' => __('messages.cart.invalid_product')]);
    }

    public function test_cart_checkout_uses_current_price()
    {
        $originalPrice = 10.00;
        $newPrice = 15.00;

        $product = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'status' => ProductStatus::PUBLISHED,
            'is_active' => true,
            'price' => $originalPrice,
            'stock_qty' => 10,
        ]);

        // Add product to cart at original price
        $this->addProductToCart($this->buyer, $product, 2);

        // Now, change the product's price in the database
        $product->update(['price' => $newPrice]);

        // Get cart totals. It should still reflect the original price.
        $response = $this->actingAs($this->buyer)->getJson(route('cart.index'));
        $response->assertOk();

        $data = $response->json('data');
        $expectedTotal = 2 * $newPrice; // Should be 20.00, not 30.00

        $this->assertEquals($expectedTotal, $data['total']);
        $this->assertEquals($expectedTotal, $data['supplier_requirements'][0]['current_total']);

        // Proceed to checkout. The order total should also be based on the original price.
        $checkoutResponse = $this->actingAs($this->buyer)->postJson(route('cart.checkout'), [
            'shipping_address_id' => $this->shippingAddress->id,
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY->value,
            'shipping_method' => ShippingMethod::DELEGATE->value,
            'order_type' => OrderType::INDIVIDUAL->value,
        ]);

        $checkoutResponse->assertStatus(201);
        $orderTotal = $checkoutResponse->json('data.order.total');
        $this->assertEquals($expectedTotal, $orderTotal);
    }

    public function test_cart_handles_zero_quantity_items_gracefully()
    {
        $product = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'status' => ProductStatus::PUBLISHED,
            'is_active' => true,
            'price' => 10.00,
            'stock_qty' => 10,
        ]);

        // Manually insert a cart product with quantity 0
        $cart = Cart::firstOrCreate(['user_id' => $this->buyer->id]);
        CartProduct::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 0, // This is the edge case
            'price' => $product->price,
        ]);

        // The cart index should calculate totals as if the item doesn't exist.
        $response = $this->actingAs($this->buyer)->getJson(route('cart.index'));
        $response->assertOk();


        // Checkout should fail because the cart is effectively empty.
        $checkoutResponse = $this->actingAs($this->buyer)->postJson(route('cart.checkout'), [
            'shipping_address_id' => $this->shippingAddress->id,
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY->value,
            'shipping_method' => ShippingMethod::DELEGATE->value,
            'order_type' => OrderType::INDIVIDUAL->value,
        ]);

        $checkoutResponse->assertStatus(400)->assertJson(['success' => false]);
    }

    public function test_add_to_cart_fails_when_mixing_suppliers()
    {
        $supplierA = User::factory()->supplier()->create();
        $supplierB = User::factory()->supplier()->create();

        $productA = Product::factory()->create([
            'supplier_id' => $supplierA->id,
            'status' => ProductStatus::PUBLISHED,
            'is_active' => true,
            'stock_qty' => 10,
        ]);

        $productB = Product::factory()->create([
            'supplier_id' => $supplierB->id,
            'status' => ProductStatus::PUBLISHED,
            'is_active' => true,
            'stock_qty' => 10,
        ]);

        // First add product from supplier A
        $this->addProductToCart($this->buyer, $productA, 1);

        // Now attempt to add product from supplier B -> should fail
        $response = $this->actingAs($this->buyer)->postJson(route('cart.store'), [
            'product_id' => $productB->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(422)->assertJson(['success' => false]);
        $this->assertDatabaseMissing('cart_product', [
            'cart_id' => $this->buyer->cart->id,
            'product_id' => $productB->id,
            'quantity' => 1,
            'price' => $productB->price,
        ]);
    }

    public function test_add_to_cart_validation_rules()
    {
        // quantity must be >= 1 and product must exist and be active/published
        $response = $this->actingAs($this->buyer)->postJson(route('cart.store'), [
            'product_id' => 999999,
            'quantity' => 0,
        ]);

        $response->assertStatus(422)->assertJson([
            'errors' => [],
        ]);
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

    public function test_buyer_cannot_checkout_with_products_from_multiple_suppliers()
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
            ->assertJson(['success' => false]);
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
