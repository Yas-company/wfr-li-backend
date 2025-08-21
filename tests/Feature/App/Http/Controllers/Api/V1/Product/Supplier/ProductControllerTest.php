<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1\Product\Supplier;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $supplier;
    protected User $otherSupplier;
    protected User $buyer;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept-Language' => 'en',
        ]);

        $this->supplier = User::factory()->supplier()->create();
        $this->otherSupplier = User::factory()->supplier()->create();
        $this->buyer = User::factory()->buyer()->create();
        $this->category = Category::factory()->create([]);
    }

    public function test_supplier_can_create_product()
    {
        $productData = Product::factory()->make([
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
        ])->toArray();

        // Remove the image URL and replace with a fake image file
        unset($productData['image']);

        // Create a fake image file for testing using a simple approach
        $imageFile = \Illuminate\Http\UploadedFile::fake()->create('product.jpg', 100, 'image/jpeg');
        $productData['image'] = $imageFile;

        $response = $this->actingAs($this->supplier)->postJson(route('supplier.products.store'), $productData);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'image',
                    'price',
                    'price_before_discount',
                    'quantity',
                    'stock_qty',
                    'nearly_out_of_stock_limit',
                    'status',
                    'is_favorite',
                    'unit_type',

                ],
            ]);

        $this->assertDatabaseHas('products', [
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
        ]);
    }

    public function test_supplier_cannot_create_product_with_invalid_status_and_unit_type()
    {
        $productData = Product::factory()->make([
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
        ])->toArray();

        // Set invalid values directly in the payload
        $productData['status'] = 100;
        $productData['unit_type'] = 100;

        unset($productData['image']);
        $imageFile = \Illuminate\Http\UploadedFile::fake()->create('product.jpg', 100, 'image/jpeg');
        $productData['image'] = $imageFile;

        $response = $this->actingAs($this->supplier)->postJson(route('supplier.products.store'), $productData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status', 'unit_type']);
    }

    public function test_supplier_cannot_create_product_with_zero_price_and_quantity()
    {
        $productData = Product::factory()->make([
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
        ])->toArray();

        $productData['base_price'] = 0;
        $productData['quantity'] = 0;

        unset($productData['image']);
        $imageFile = \Illuminate\Http\UploadedFile::fake()->create('product.jpg', 100, 'image/jpeg');
        $productData['image'] = $imageFile;

        $response = $this->actingAs($this->supplier)->postJson(route('supplier.products.store'), $productData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['base_price', 'quantity']);
    }

    public function test_supplier_can_update_product()
    {
        $product = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
        ]);
        // Remove the image URL and replace with a fake image file

        $updateData = Product::factory()->make([
            'category_id' => $this->category->id,
        ])->toArray();

        unset($updateData['image']);

        // Create a fake image file for testing using a simple approach
        $imageFile = \Illuminate\Http\UploadedFile::fake()->create('product.jpg', 100, 'image/jpeg');
        $updateData['image'] = $imageFile;
        $response = $this->actingAs($this->supplier)->postJson(route('supplier.products.update', $product->id), $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'price',
                ],
            ]);
    }

    public function test_supplier_can_delete_product()
    {
        $product = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->supplier)->deleteJson(route('supplier.products.destroy', $product->id));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Delete deleted successfully',
            ]);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_supplier_can_view_product()
    {
        $product = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->supplier)->getJson(route('supplier.products.show', $product->id));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'image',
                    'price',
                    'price_before_discount',
                    'quantity',
                    'stock_qty',
                    'nearly_out_of_stock_limit',
                    'status',
                    'is_favorite',
                    'unit_type',

                ],
            ]);
    }

    public function test_supplier_can_view_all_products()
    {
        // Create some products for the supplier
        Product::factory()->count(3)->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->supplier)->getJson(route('supplier.products.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'image',
                        'price',
                        'price_before_discount',
                        'quantity',
                        'stock_qty',
                        'nearly_out_of_stock_limit',
                        'status',
                        'is_favorite',
                        'unit_type',

                    ],
                ],
                'links' => [
                    'first',
                    'last',
                    'next',
                    'prev',
                ],
            ]);
    }

    public function test_supplier_cannot_view_other_supplier_product()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->supplier)->getJson(route('supplier.products.show', $product->id));

        $response->assertStatus(404);
    }

    public function test_supplier_cannot_update_other_supplier_product()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->supplier)->postJson(route('supplier.products.update', $product->id), []);

        $response->assertStatus(404);
    }

    public function test_supplier_cannot_delete_other_supplier_product()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->supplier)->deleteJson(route('supplier.products.destroy', $product->id));

        $response->assertStatus(404);
    }

    public function test_supplier_can_view_product_details()
    {
        $product = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->supplier)->getJson(route('supplier.products.show', $product->id));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'image',
                    'price',
                    'price_before_discount',
                    'quantity',
                    'stock_qty',
                    'nearly_out_of_stock_limit',
                    'status',
                    'is_favorite',
                    'unit_type',

                ],
            ]);
    }

    public function test_buyer_can_favorite_product()
    {
        $product = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->buyer)->postJson(route('favorite.toggle'), [
            'product_id' => $product->id,
            'is_favorite' => true,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Favorite status updated successfully',
            ]);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->buyer->id,
            'product_id' => $product->id,
            'is_favorite' => true,
        ]);
    }

    public function test_buyer_can_unfavorite_product()
    {
        $product = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->buyer)->postJson(route('favorite.toggle'), [
            'product_id' => $product->id,
            'is_favorite' => false,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Favorite status updated successfully',
            ]);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->buyer->id,
            'product_id' => $product->id,
            'is_favorite' => false,
        ]);
    }

    public function test_buyer_can_view_favorite_products()
    {
        $product = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
        ]);

        // First, add the product to favorites
        $this->actingAs($this->buyer)->postJson(route('favorite.toggle'), [
            'product_id' => $product->id,
            'is_favorite' => true,
        ]);

        $response = $this->actingAs($this->buyer)->getJson(route('favorite.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'image',
                        'price',
                        'price_before_discount',
                        'quantity',
                        'stock_qty',
                        'nearly_out_of_stock_limit',
                        'status',
                        'is_favorite',
                        'unit_type',

                    ],
                ],
            ]);
    }

    public function test_supplier_can_view_expired_count()
    {
        // Create some products with different stock quantities
        Product::factory()->count(3)->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'stock_qty' => 0, // Expired products
        ]);

        Product::factory()->count(2)->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'stock_qty' => 5, // Non-expired products
        ]);

        $response = $this->actingAs($this->supplier)->getJson(route('supplier.products.expired.count'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'expired' => 3,
                ],
            ]);
    }

    public function test_supplier_can_view_near_expiry_count()
    {
        // Create some products with different stock quantities
        Product::factory()->count(2)->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'stock_qty' => 3, // Near expiry products,
            'nearly_out_of_stock_limit' => 5,
        ]);

        Product::factory()->count(3)->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'stock_qty' => 10, // Normal stock products,
            'nearly_out_of_stock_limit' => 6,
        ]);

        $response = $this->actingAs($this->supplier)->getJson(route('supplier.products.near-expiry.count'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'near_expiry' => 2,
                ],
            ]);
    }

    public function test_supplier_can_view_available_products()
    {
        // Clear any existing products for this supplier
        Product::where('supplier_id', $this->supplier->id)->delete();

        // Create available products (stock_qty > nearly_out_of_stock_limit)
        Product::factory()->count(3)->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'stock_qty' => 15,
            'nearly_out_of_stock_limit' => 10,
            'is_active' => true,
        ]);

        // Create nearly out-of-stock products (stock_qty < nearly_out_of_stock_limit)
        Product::factory()->count(2)->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'stock_qty' => 5,
            'nearly_out_of_stock_limit' => 10,
            'is_active' => true,
        ]);

        // Create out-of-stock products
        Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'stock_qty' => 0,
            'nearly_out_of_stock_limit' => 10,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->supplier)->getJson(route('supplier.products.available'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'image',
                        'price',
                        'price_before_discount',
                        'quantity',
                        'stock_qty',
                        'nearly_out_of_stock_limit',
                        'status',
                        'is_favorite',
                        'unit_type',
                    ],
                ],
                'links' => [
                    'first',
                    'last',
                    'next',
                    'prev',
                ],
            ]);

        // Assert that only available products are returned
        $responseData = $response->json('data');
        $this->assertCount(3, $responseData);

        foreach ($responseData as $product) {
            $this->assertGreaterThan($product['nearly_out_of_stock_limit'], $product['stock_qty']);
        }
    }

    public function test_supplier_can_view_out_of_stock_products()
    {
        // Clear any existing products for this supplier
        Product::where('supplier_id', $this->supplier->id)->delete();

        // Create available products
        Product::factory()->count(2)->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'stock_qty' => 10,
            'is_active' => true,
        ]);

        // Create out-of-stock products (stock_qty = 0)
        Product::factory()->count(3)->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'stock_qty' => 0,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->supplier)->getJson(route('supplier.products.out-of-stock'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'image',
                        'price',
                        'price_before_discount',
                        'quantity',
                        'stock_qty',
                        'nearly_out_of_stock_limit',
                        'status',
                        'is_favorite',
                        'unit_type',
                    ],
                ],
                'links' => [
                    'first',
                    'last',
                    'next',
                    'prev',
                ],
            ]);

        // Assert that only out-of-stock products are returned
        $responseData = $response->json('data');
        $this->assertCount(3, $responseData);

        foreach ($responseData as $product) {
            $this->assertEquals(0, $product['stock_qty']);
        }
    }

    public function test_supplier_can_view_nearly_out_of_stock_products()
    {
        // Create a fresh supplier and category for this test to avoid conflicts
        $testSupplier = User::factory()->supplier()->create();
        $testCategory = Category::factory()->create([]);

        // Create nearly out-of-stock products (stock_qty < nearly_out_of_stock_limit and stock_qty > 0)
        $nearlyOutOfStockProducts = collect();
        for ($i = 0; $i < 3; $i++) {
            $product = new Product([
                'supplier_id' => $testSupplier->id,
                'category_id' => $testCategory->id,
                'name' => ['en' => 'Nearly Out Product ' . $i, 'ar' => 'Nearly Out Product ' . $i],
                'description' => ['en' => 'Test Description', 'ar' => 'Test Description'],
                'price' => 100.00,
                'price_before_discount' => 120.00,
                'stock_qty' => 50,  // Start with high stock
                'nearly_out_of_stock_limit' => 5,
                'quantity' => 10,
                'unit_type' => 1,
                'status' => 1,
                'is_active' => true,
                'is_featured' => false,
                'min_order_quantity' => 1,
            ]);
            $product->save();

            // Now reduce the stock to simulate sales/usage - make it nearly out of stock
            $product->stock_qty = 3;  // LESS than nearly_out_of_stock_limit (5)
            $product->save();

            $nearlyOutOfStockProducts->push($product);
        }

        // Create available products (stock_qty > nearly_out_of_stock_limit) - should not be returned
        for ($i = 0; $i < 2; $i++) {
            $availableProduct = Product::create([
                'supplier_id' => $testSupplier->id,
                'category_id' => $testCategory->id,
                'name' => ['en' => 'Available Product ' . $i, 'ar' => 'Available Product ' . $i],
                'description' => ['en' => 'Test Description', 'ar' => 'Test Description'],
                'price' => 100.00,
                'price_before_discount' => 120.00,
                'stock_qty' => 50,  // Start with high stock
                'nearly_out_of_stock_limit' => 5,
                'quantity' => 10,
                'unit_type' => 1,
                'status' => 1,
                'is_active' => true,
                'is_featured' => false,
                'min_order_quantity' => 1,
            ]);

            // Keep stock high - still available (no reduction needed for available products)
            // stock_qty (50) > nearly_out_of_stock_limit (5) = Available
        }

        // Create out-of-stock products (stock_qty = 0) - should not be returned
        $outOfStockProduct = Product::create([
            'supplier_id' => $testSupplier->id,
            'category_id' => $testCategory->id,
            'name' => ['en' => 'Out of Stock Product', 'ar' => 'Out of Stock Product'],
            'description' => ['en' => 'Test Description', 'ar' => 'Test Description'],
            'price' => 100.00,
            'price_before_discount' => 120.00,
            'stock_qty' => 30,  // Start with good stock
            'nearly_out_of_stock_limit' => 5,
            'quantity' => 10,
            'unit_type' => 1,
            'status' => 1,
            'is_active' => true,
            'is_featured' => false,
            'min_order_quantity' => 1,
        ]);

        // Completely sell out the product - reduce stock to 0
        $outOfStockProduct->stock_qty = 0;
        $outOfStockProduct->save();

        $response = $this->actingAs($testSupplier)->getJson(route('supplier.products.nearly-out-of-stock'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'image',
                        'price',
                        'price_before_discount',
                        'quantity',
                        'stock_qty',
                        'nearly_out_of_stock_limit',
                        'status',
                        'is_favorite',
                        'unit_type',
                    ],
                ],
                'links' => [
                    'first',
                    'last',
                    'next',
                    'prev',
                ],
            ]);

        // Assert that only nearly out-of-stock products are returned
        $responseData = $response->json('data');

        // The response should contain exactly 3 nearly out-of-stock products
        $this->assertCount(3, $responseData);

        // Verify each returned product meets the criteria
        foreach ($responseData as $product) {
            // For nearly out of stock: stock_qty < nearly_out_of_stock_limit
            // So we assert: stock_qty is less than nearly_out_of_stock_limit
            $this->assertTrue($product['stock_qty'] < $product['nearly_out_of_stock_limit'],
                "Product ID {$product['id']}: stock_qty ({$product['stock_qty']}) should be LESS than nearly_out_of_stock_limit ({$product['nearly_out_of_stock_limit']})");
            // stock_qty should be greater than 0 (not out of stock)
            $this->assertGreaterThan(0, $product['stock_qty'],
                "Product ID {$product['id']}: stock_qty ({$product['stock_qty']}) should be greater than 0");
        }

        // Verify the returned products are the ones we created
        $returnedIds = collect($responseData)->pluck('id')->sort()->values();
        $expectedIds = $nearlyOutOfStockProducts->pluck('id')->sort()->values();
        $this->assertEquals($expectedIds->toArray(), $returnedIds->toArray());
    }
}
