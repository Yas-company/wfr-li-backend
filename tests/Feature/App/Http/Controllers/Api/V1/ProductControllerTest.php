<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1;

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

    protected User $buyer;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept-Language' => 'en',
        ]);

        $this->supplier = User::factory()->supplier()->create();
        $this->buyer = User::factory()->buyer()->create();
        $this->category = Category::factory()->create();
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

        $response = $this->actingAs($this->supplier)->postJson('/api/v1/products', $productData);
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
                    'status',
                    'is_favorite',
                    'unit_type',
                    'avg_rating',
                ],
            ]);

        $this->assertDatabaseHas('products', [
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
        ]);
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
        $response = $this->actingAs($this->supplier)->postJson('/api/v1/products/'.$product->id, $updateData);

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

        $response = $this->actingAs($this->supplier)->deleteJson('/api/v1/products/'.$product->id);

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

        $response = $this->actingAs($this->supplier)->getJson('/api/v1/products/'.$product->id);

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
                    'status',
                    'is_favorite',
                    'unit_type',
                    'avg_rating',
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

        $response = $this->actingAs($this->supplier)->getJson('/api/v1/products');

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
                        'status',
                        'is_favorite',
                        'unit_type',
                        'avg_rating',
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

    public function test_supplier_can_view_product_details()
    {
        $product = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->supplier)->getJson('/api/v1/products/'.$product->id);

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
                    'status',
                    'is_favorite',
                    'unit_type',
                    'avg_rating',
                ],
            ]);
    }

    public function test_buyer_can_view_product_details()
    {
        $product = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->buyer)->getJson('/api/v1/products/'.$product->id);

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
                    'status',
                    'is_favorite',
                    'unit_type',
                    'avg_rating',
                ],
            ]);
    }

    public function test_buyer_can_view_all_products()
    {
        // Create some products
        Product::factory()->count(3)->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->buyer)->getJson('/api/v1/products');

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
                        'status',
                        'is_favorite',
                        'unit_type',
                        'avg_rating',
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

    public function test_buyer_can_favorite_product()
    {
        $product = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->buyer)->postJson('/api/v1/favorite/toggle', [
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

        $response = $this->actingAs($this->buyer)->postJson('/api/v1/favorite/toggle', [
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
        $this->actingAs($this->buyer)->postJson('/api/v1/favorite/toggle', [
            'product_id' => $product->id,
            'is_favorite' => true,
        ]);

        $response = $this->actingAs($this->buyer)->getJson('/api/v1/favorite');

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
                        'status',
                        'is_favorite',
                        'unit_type',
                        'avg_rating',
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

        $response = $this->actingAs($this->supplier)->getJson('/api/v1/products/expired/count');

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
            'stock_qty' => 3, // Near expiry products
        ]);

        Product::factory()->count(3)->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'stock_qty' => 10, // Normal stock products
        ]);

        $response = $this->actingAs($this->supplier)->getJson('/api/v1/products/near-expiry/count');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'near_expiry' => 2,
                ],
            ]);
    }

    public function test_supplier_can_view_stock_status_counts()
    {
        // Create products with different stock statuses
        Product::factory()->count(2)->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'stock_qty' => 0, // Expired products
        ]);

        Product::factory()->count(3)->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'stock_qty' => 2, // Near expiry products
        ]);

        $response = $this->actingAs($this->supplier)->getJson('/api/v1/products/stock-status/count');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'expired' => 2,
                    'near_expiry' => 3,
                ],
            ]);
    }
}
