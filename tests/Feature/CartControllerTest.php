<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Enums\UserRole;
use Database\Factories\CartItemFactory;
use Database\Factories\ProductFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;
    private Product $product;
    private Product $product2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a buyer user
        $this->buyer = User::factory()->create([
            'role' => UserRole::BUYER->value,
        ]);

        // Create products
        $this->product = ProductFactory::new()->create([
            'price' => 100.00,
        ]);

        $this->product2 = ProductFactory::new()->create([
            'price' => 200.00,
        ]);
    }

    public function test_buyer_can_view_their_cart()
    {
        // Create cart with items
        $cart = Cart::factory()->create(['user_id' => $this->buyer->id]);

        // Create first cart item
        $cartItem1 = CartItemFactory::new()->create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price' => $this->product->price,
        ]);

        // Create second cart item
        $cartItem2 = CartItemFactory::new()->create([
            'cart_id' => $cart->id,
            'product_id' => $this->product2->id,
            'quantity' => 1,
            'price' => $this->product2->price,
        ]);

        // Set initial total to 0 to verify it gets updated
        $cart->update(['total_amount' => 0]);

        $response = $this->actingAs($this->buyer)
            ->getJson('/api/buyer/cart');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'cart' => [
                    'id',
                    'total_amount',
                    'items' => [
                        '*' => [
                            'id',
                            'quantity',
                            'price',
                            'product',
                        ],
                    ],
                ],
            ])
            ->assertJson([
                'cart' => [
                    'total_amount' => (string)number_format(400.00, 2), // (2 * 100.00) + (1 * 200.00)
                ],
            ]);

        // Verify the cart total was updated in the database
        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'total_amount' => 400.00,
        ]);
    }

    public function test_buyer_can_add_product_to_cart()
    {
        $response = $this->actingAs($this->buyer)
            ->postJson('/api/buyer/cart/add', [
                'product_id' => $this->product->id,
                'quantity' => 2,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'cart' => [
                    'id',
                    'total_amount',
                    'items' => [
                        '*' => [
                            'id',
                            'quantity',
                            'price',
                            'product' => [
                                'id',
                                'name',
                                'price',
                            ],
                        ],
                    ],
                ],
            ]);

        $this->assertDatabaseHas('carts', [
            'user_id' => $this->buyer->id,
            'total_amount' => 200.00, // 2 * 100.00
        ]);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price' => 100.00,
        ]);
    }

    public function test_buyer_can_update_product_quantity_in_cart()
    {
        // First add a product to cart
        $cart = Cart::factory()->create(['user_id' => $this->buyer->id]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price' => $this->product->price,
        ]);

        $response = $this->actingAs($this->buyer)
            ->patchJson("/api/buyer/cart/items/{$cartItem->id}/quantity", [
                'quantity' => 3,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'cart' => [
                    'id',
                    'total_amount',
                    'items',
                ],
            ]);

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 3,
        ]);

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'total_amount' => 300.00, // 3 * 100.00
        ]);
    }

    public function test_buyer_can_remove_product_from_cart()
    {
        // First add a product to cart
        $cart = Cart::factory()->create(['user_id' => $this->buyer->id]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price' => $this->product->price,
        ]);

        $response = $this->actingAs($this->buyer)
            ->deleteJson("/api/buyer/cart/items/{$cartItem->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'cart' => [
                    'id',
                    'total_amount',
                    'items',
                ],
            ]);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id,
        ]);

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'total_amount' => 0.00,
        ]);
    }

    public function test_buyer_cannot_access_other_buyers_cart()
    {
        $otherBuyer = User::factory()->create([
            'role' => UserRole::BUYER->value,
        ]);

        $cart = Cart::factory()->create(['user_id' => $otherBuyer->id]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price' => $this->product->price,
        ]);

        // Try to update quantity
        $response = $this->actingAs($this->buyer)
            ->patchJson("/api/buyer/cart/items/{$cartItem->id}/quantity", [
                'quantity' => 2,
            ]);

        $response->assertStatus(403);

        // Try to remove item
        $response = $this->actingAs($this->buyer)
            ->deleteJson("/api/buyer/cart/items/{$cartItem->id}");

        $response->assertStatus(403);
    }

    public function test_validation_rules_for_adding_to_cart()
    {
        $response = $this->actingAs($this->buyer)
            ->postJson('/api/buyer/cart/add', [
                'product_id' => 999, // Non-existent product
                'quantity' => 0, // Invalid quantity
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['product_id', 'quantity']);
    }

    public function test_validation_rules_for_updating_quantity()
    {
        $cart = Cart::factory()->create(['user_id' => $this->buyer->id]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price' => $this->product->price,
        ]);

        $response = $this->actingAs($this->buyer)
            ->patchJson("/api/buyer/cart/items/{$cartItem->id}/quantity", [
                'quantity' => 0, // Invalid quantity
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['quantity']);
    }

//    public function test_adding_same_product_updates_quantity()
//    {
//        // First add
//        $response = $this->actingAs($this->buyer)
//            ->postJson('/api/buyer/cart/add', [
//                'product_id' => $this->product->id,
//                'quantity' => 1,
//            ]);
//
//        $response->assertStatus(200);
//
//        // Get the cart item ID after first addition
//        $cartItem = CartItem::where('product_id', $this->product->id)->first();
//        $this->assertNotNull($cartItem, 'Cart item should exist after first addition');
//
//        // Verify first addition
//        $this->assertDatabaseHas('cart_items', [
//            'id' => $cartItem->id,
//            'product_id' => $this->product->id,
//            'quantity' => 1,
//        ]);
//
//        // Add same product again
//        $response = $this->actingAs($this->buyer)
//            ->postJson('/api/buyer/cart/add', [
//                'product_id' => $this->product->id,
//                'quantity' => 2,
//            ]);
//
//        $response->assertStatus(200)
//            ->assertJsonStructure([
//                'message',
//                'cart' => [
//                    'id',
//                    'total_amount',
//                    'items' => [
//                        '*' => [
//                            'id',
//                            'quantity',
//                            'price',
//                            'product',
//                        ],
//                    ],
//                ],
//            ]);
//
//        // Get fresh cart item data from database
//        $updatedCartItem = CartItem::where('id', $cartItem->id)->first();
//        $this->assertNotNull($updatedCartItem, 'Cart item should still exist after update');
//
//        // Verify final state
//        $this->assertEquals(3, $updatedCartItem->quantity, 'Cart item quantity should be 3');
//        $this->assertEquals($this->product->price, $updatedCartItem->price, 'Cart item price should match product price');
//
//        // Verify database state
//        $this->assertDatabaseHas('cart_items', [
//            'id' => $cartItem->id,
//            'product_id' => $this->product->id,
//            'quantity' => 3,
//        ]);
//
//        $this->assertDatabaseHas('carts', [
//            'user_id' => $this->buyer->id,
//            'total_amount' => 300.00,
//        ]);
//
//        // Verify there's only one cart item for this product
//        $this->assertDatabaseCount('cart_items', 1);
//
//        // Verify the response data
//        $responseData = $response->json('cart');
//        $this->assertEquals(1, count($responseData['items']), 'Should have exactly one item in cart');
//        $this->assertEquals(3, $responseData['items'][0]['quantity'], 'Item quantity should be 3');
//        $this->assertEquals('300.00', $responseData['total_amount'], 'Total amount should be 300.00');
//    }
}
