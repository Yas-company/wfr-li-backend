<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1\Product\Buyer;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Enums\ProductStatus;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $supplier;
    protected User $otherSupplier;
    protected User $buyer;

    protected Category $category;
    protected Category $otherCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept-Language' => 'en',
        ]);

        $this->buyer = User::factory()->buyer()->create();
        $this->supplier = User::factory()->supplier()->create();
        $this->otherSupplier = User::factory()->supplier()->create();
        $this->category = Category::factory()->create([
            'supplier_id' => $this->supplier->id,
        ]);
        $this->otherCategory = Category::factory()->create([
            'supplier_id' => $this->supplier->id,
        ]);
    }


    public function test_buyer_can_view_product_details()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->buyer)->getJson(route('buyer.products.show', $product->id));

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

    public function test_buyer_can_view_all_products()
    {
        Product::factory(3)->create([
            'supplier_id' => $this->supplier->id,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        Product::factory(2)->create([
            'supplier_id' => $this->otherSupplier->id,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        $response = $this->actingAs($this->buyer)->getJson(route('buyer.products.index'));

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

        $response->assertJsonCount(5, 'data');
    }

    public function test_buyer_can_only_view_published_products()
    {
        Product::factory(2)->create([
            'supplier_id' => $this->supplier->id,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        Product::factory(1)->create([
            'supplier_id' => $this->supplier->id,
            'is_active' => true,
            'status' => ProductStatus::DRAFT,
        ]);

        Product::factory(1)->create([
            'supplier_id' => $this->otherSupplier->id,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        Product::factory(2)->create([
            'supplier_id' => $this->otherSupplier->id,
            'is_active' => true,
            'status' => ProductStatus::DRAFT,
        ]);

        $response = $this->actingAs($this->buyer)->getJson(route('buyer.products.index'));

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

        $response->assertJsonCount(3, 'data');
    }

    public function test_buyer_can_only_view_active_products()
    {
        Product::factory(2)->create([
            'supplier_id' => $this->supplier->id,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        Product::factory(1)->create([
            'supplier_id' => $this->supplier->id,
            'is_active' => false,
            'status' => ProductStatus::PUBLISHED,
        ]);

        Product::factory(1)->create([
            'supplier_id' => $this->otherSupplier->id,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        Product::factory(2)->create([
            'supplier_id' => $this->otherSupplier->id,
            'is_active' => false,
            'status' => ProductStatus::PUBLISHED,
        ]);

        $response = $this->actingAs($this->buyer)->getJson(route('buyer.products.index'));

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

        $response->assertJsonCount(3, 'data');
    }

    public function test_buyer_can_filter_products_by_supplier()
    {
        Product::factory(3)->create([
            'supplier_id' => $this->supplier->id,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        Product::factory(2)->create([
            'supplier_id' => $this->otherSupplier->id,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        $response = $this->actingAs($this->buyer)->getJson(route('buyer.products.index') . '?filter[supplier_id]=' . $this->supplier->id);

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

        $response->assertJsonCount(3, 'data');
    }

    public function test_buyer_can_filter_products_by_category()
    {
        Product::factory(3)->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        Product::factory(2)->create([
            'supplier_id' => $this->otherSupplier->id,
            'category_id' => $this->otherCategory->id,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        $response = $this->actingAs($this->buyer)->getJson(route('buyer.products.index') . '?filter[category_id]=' . $this->category->id);

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

        $response->assertJsonCount(3, 'data');
    }

    public function test_buyer_can_filter_products_by_name()
    {
        $productOne = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'name' => [
                'en' => 'Milk',
                'ar' => 'حليب',
            ],
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        $productTwo = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'name' => [
                'en' => 'Cheese',
                'ar' => 'جبنة',
            ],
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);


        $response = $this->actingAs($this->buyer)->getJson(route('buyer.products.index') . '?filter[name]=Mil');

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

        $response->assertJsonCount(1, 'data');
        $this->assertEquals($productOne->id, $response->json('data.0.id'));

        $response = $this->actingAs($this->buyer)->getJson(route('buyer.products.index') . '?filter[name]=جبن', [
            'Accept-Language' => 'ar',
        ]);
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

        $response->assertJsonCount(1, 'data');
        $this->assertEquals($productTwo->id, $response->json('data.0.id'));
    }

    public function test_buyer_can_filter_products_by_price()
    {
        $productOne = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'price' => 100,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        $productTwo = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'price' => 300,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);


        $response = $this->actingAs($this->buyer)->getJson(route('buyer.products.index') . '?filter[price]=100');

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

        $response->assertJsonCount(1, 'data');
        $this->assertEquals($productOne->id, $response->json('data.0.id'));
    }

    public function test_buyer_can_sort_products_by_price()
    {
        $productOne = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'price' => 100,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        $productTwo = Product::factory()->create([
            'supplier_id' => $this->otherSupplier->id,
            'price' => 200,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        $productThree = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'price' => 50,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        $response = $this->actingAs($this->buyer)->getJson(route('buyer.products.index') . '?sort=price');

        $response->assertJsonCount(3, 'data');
        $this->assertEquals($productThree->id, $response->json('data.0.id'));
        $this->assertEquals($productOne->id, $response->json('data.1.id'));
        $this->assertEquals($productTwo->id, $response->json('data.2.id'));

        $response = $this->actingAs($this->buyer)->getJson(route('buyer.products.index') . '?sort=-price');

        $response->assertJsonCount(3, 'data');
        $this->assertEquals($productTwo->id, $response->json('data.0.id'));
        $this->assertEquals($productOne->id, $response->json('data.1.id'));
        $this->assertEquals($productThree->id, $response->json('data.2.id'));
    }

    public function test_buyer_can_sort_products_by_id()
    {
        $productOne = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        $productTwo = Product::factory()->create([
            'supplier_id' => $this->otherSupplier->id,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        $productThree = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        $response = $this->actingAs($this->buyer)->getJson(route('buyer.products.index') . '?sort=id');

        $response->assertJsonCount(3, 'data');
        $this->assertEquals($productOne->id, $response->json('data.0.id'));
        $this->assertEquals($productTwo->id, $response->json('data.1.id'));
        $this->assertEquals($productThree->id, $response->json('data.2.id'));

        $response = $this->actingAs($this->buyer)->getJson(route('buyer.products.index') . '?sort=-id');

        $response->assertJsonCount(3, 'data');
        $this->assertEquals($productThree->id, $response->json('data.0.id'));
        $this->assertEquals($productTwo->id, $response->json('data.1.id'));
        $this->assertEquals($productOne->id, $response->json('data.2.id'));
    }

    public function test_buyer_can_sort_products_by_created_at()
    {
        $productOne = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
            'created_at' => now()->subDays(1),
        ]);

        $productTwo = Product::factory()->create([
            'supplier_id' => $this->otherSupplier->id,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
            'created_at' => now()->subDays(3),
        ]);

        $productThree = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
            'created_at' => now()->subDays(2),
        ]);

        $response = $this->actingAs($this->buyer)->getJson(route('buyer.products.index') . '?sort=created_at');

        $response->assertJsonCount(3, 'data');
        $this->assertEquals($productTwo->id, $response->json('data.0.id'));
        $this->assertEquals($productThree->id, $response->json('data.1.id'));
        $this->assertEquals($productOne->id, $response->json('data.2.id'));

        $response = $this->actingAs($this->buyer)->getJson(route('buyer.products.index') . '?sort=-created_at');

        $response->assertJsonCount(3, 'data');
        $this->assertEquals($productOne->id, $response->json('data.0.id'));
        $this->assertEquals($productThree->id, $response->json('data.1.id'));
        $this->assertEquals($productTwo->id, $response->json('data.2.id'));
    }
}
