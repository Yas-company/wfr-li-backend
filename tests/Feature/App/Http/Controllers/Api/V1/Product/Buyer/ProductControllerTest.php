<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1\Product\Buyer;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Enums\ProductStatus;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Enums\UserRole;

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
                    'avg_rating',
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

    public function test_buyer_can_get_similar_products()
    {
        // Create a main product
        $mainProduct = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        // Create similar products in the same category (Flow 1: Category-based similarity)
        $similarProduct1 = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id, // Same category
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        $similarProduct2 = Product::factory()->create([
            'supplier_id' => $this->otherSupplier->id,
            'category_id' => $this->category->id, // Same category, different supplier
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        // Create products in different category but same supplier (Flow 2: Supplier-based similarity)
        $differentCategory = Category::factory()->create([
            'supplier_id' => $this->supplier->id,
        ]);

        $supplierSimilarProduct = Product::factory()->create([
            'supplier_id' => $this->supplier->id, // Same supplier
            'category_id' => $differentCategory->id, // Different category
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        // Create inactive product (should not be included)
        $inactiveProduct = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'is_active' => false,
            'status' => ProductStatus::PUBLISHED,
        ]);

        // Create unpublished product (will be included since service only checks is_active)
        $unpublishedProduct = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'is_active' => true,
            'status' => ProductStatus::DRAFT,
        ]);

        $response = $this->actingAs($this->buyer)
            ->getJson(route('buyer.products.similar', $mainProduct));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'price',
                        'status',
                        'is_favorite',
                        'category',
                        'ratings',
                    ]
                ]
            ]);

        $responseData = $response->json('data');
        
        // Should return similar products (category-based first, then supplier-based)
        $this->assertGreaterThanOrEqual(1, count($responseData));
        $this->assertLessThanOrEqual(5, count($responseData));
        
        // Verify the returned products are from the same category
        $returnedProductIds = collect($responseData)->pluck('id')->toArray();
        $expectedCategoryProductIds = [$similarProduct1->id, $similarProduct2->id];
        
        // Check that at least one category-based product is returned
        $this->assertTrue(
            count(array_intersect($returnedProductIds, $expectedCategoryProductIds)) > 0,
            'At least one category-based similar product should be returned'
        );
        
        // Verify inactive products are not included (but unpublished products might be included)
        $this->assertNotContains($inactiveProduct->id, $returnedProductIds);
        $this->assertNotContains($mainProduct->id, $returnedProductIds);
    }

    public function test_buyer_can_get_similar_products_when_no_category_matches()
    {
        // Create a completely isolated setup
        $isolatedSupplier = User::factory()->create([
            'role' => UserRole::SUPPLIER,
        ]);

        $isolatedCategory = Category::factory()->create([
            'supplier_id' => $isolatedSupplier->id,
        ]);

        // Create a main product with isolated category
        $mainProduct = Product::factory()->create([
            'supplier_id' => $isolatedSupplier->id,
            'category_id' => $isolatedCategory->id, // Isolated category
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        // Create products with different category but same supplier (Flow 2: Supplier-based similarity)
        $differentCategory = Category::factory()->create([
            'supplier_id' => $isolatedSupplier->id,
        ]);

        $supplierSimilarProduct1 = Product::factory()->create([
            'supplier_id' => $isolatedSupplier->id, // Same supplier
            'category_id' => $differentCategory->id, // Different category
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        $supplierSimilarProduct2 = Product::factory()->create([
            'supplier_id' => $isolatedSupplier->id, // Same supplier
            'category_id' => $differentCategory->id, // Different category
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        $response = $this->actingAs($this->buyer)
            ->getJson(route('buyer.products.similar', $mainProduct));

        $response->assertStatus(200);

        $responseData = $response->json('data');
        
        // Should return supplier-based similar products
        $this->assertGreaterThanOrEqual(1, count($responseData));
        $this->assertLessThanOrEqual(5, count($responseData));
        
        // Verify the returned products are supplier-based matches
        $returnedProductIds = collect($responseData)->pluck('id')->toArray();
        $expectedSupplierProductIds = [$supplierSimilarProduct1->id, $supplierSimilarProduct2->id];
        
        // Check that at least one supplier-based product is returned
        $this->assertTrue(
            count(array_intersect($returnedProductIds, $expectedSupplierProductIds)) > 0,
            'At least one supplier-based similar product should be returned'
        );
        
        $this->assertNotContains($mainProduct->id, $returnedProductIds);
    }

    public function test_buyer_can_get_similar_products_when_no_matches()
    {
        // Create a main product
        $mainProduct = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        // Create products with different category and different supplier
        $differentCategory = Category::factory()->create([
            'supplier_id' => $this->otherSupplier->id,
        ]);

        $differentProduct = Product::factory()->create([
            'supplier_id' => $this->otherSupplier->id,
            'category_id' => $differentCategory->id,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        $response = $this->actingAs($this->buyer)
            ->getJson(route('buyer.products.similar', $mainProduct));

        $response->assertStatus(200);

        $responseData = $response->json('data');
        
        // Should return empty array when no matches
        $this->assertCount(0, $responseData);
    }

    public function test_supplier_cannot_access_similar_products_endpoint()
    {
        $product = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        $response = $this->actingAs($this->supplier)
            ->getJson(route('buyer.products.similar', $product));

        $response->assertStatus(401);
    }


    public function test_similar_products_returns_maximum_5_products()
    {
        // Create a main product
        $mainProduct = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        // Create 7 similar products in the same category
        for ($i = 0; $i < 7; $i++) {
            Product::factory()->create([
                'supplier_id' => $this->supplier->id,
                'category_id' => $this->category->id,
                'is_active' => true,
                'status' => ProductStatus::PUBLISHED,
            ]);
        }

        $response = $this->actingAs($this->buyer)
            ->getJson(route('buyer.products.similar', $mainProduct));

        $response->assertStatus(200);

        $responseData = $response->json('data');
        
        // Should return maximum 5 products
        $this->assertCount(5, $responseData);
    }

    public function test_similar_products_excludes_current_product()
    {
        // Create a main product
        $mainProduct = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        // Create similar products
        $similarProduct = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ]);

        $response = $this->actingAs($this->buyer)
            ->getJson(route('buyer.products.similar', $mainProduct));

        $response->assertStatus(200);

        $responseData = $response->json('data');
        
        // Should not include the current product
        $this->assertNotContains($mainProduct->id, collect($responseData)->pluck('id')->toArray());
        
        // Should include the similar product
        $this->assertContains($similarProduct->id, collect($responseData)->pluck('id')->toArray());
    }

}
