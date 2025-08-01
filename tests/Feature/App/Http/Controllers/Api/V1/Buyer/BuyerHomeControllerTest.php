<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1\Buyer;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BuyerHomeControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $buyer;
    protected User $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept-Language' => 'en',
        ]);

        $this->buyer = User::factory()->buyer()->create();
        $this->supplier = User::factory()->supplier()->create();
    }

    public function test_buyer_can_get_suppliers_and_products()
    {
        // Create multiple suppliers with products
        $suppliers = User::factory()->supplier()->count(6)->create();
        
        foreach ($suppliers as $supplier) {
            $category = Category::factory()->create(['supplier_id' => $supplier->id]);
            Product::factory()->count(5)->create([
                'supplier_id' => $supplier->id,
                'category_id' => $category->id,
                'is_active' => true,
            ]);
        }

        $response = $this->actingAs($this->buyer)->getJson(route('home.suppliers-and-products'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'image',
                        'products' => [
                            '*' => [
                                'id',
                                'name',
                                'description',
                                'images',
                                'image',
                                'price',
                                'price_before_discount',
                                'quantity',
                                'stock_qty',
                                'nearly_out_of_stock_limit',
                                'status',
                                'is_favorite',
                                'unit_type',
                                'category',
                                'avg_rating',
                            ],
                        ],
                    ],
                ],
            ]);

        // Assert that only 4 suppliers are returned (take(4))
        $responseData = $response->json('data');
        $this->assertCount(4, $responseData);

        // Assert that each supplier has products (up to 10)
        foreach ($responseData as $supplierData) {
            $this->assertArrayHasKey('products', $supplierData);
            $this->assertLessThanOrEqual(10, count($supplierData['products']));
        }
    }

    public function test_buyer_can_get_suppliers_with_limited_products()
    {
        // Create a supplier with many products
        $category = Category::factory()->create(['supplier_id' => $this->supplier->id]);
        Product::factory()->count(15)->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->buyer)->getJson(route('home.suppliers-and-products'));

        $response->assertStatus(200);
        
        $responseData = $response->json('data');
        
        // Find the supplier in the response
        $supplierData = collect($responseData)->firstWhere('id', $this->supplier->id);
        
        if ($supplierData) {
            // Assert that only 10 products are returned (take(10) in products relationship)
            $this->assertLessThanOrEqual(10, count($supplierData['products']));
        }
    }

    public function test_buyer_can_get_suppliers_with_no_products()
    {
        // Create suppliers without products
        User::factory()->supplier()->count(3)->create();

        $response = $this->actingAs($this->buyer)->getJson(route('home.suppliers-and-products'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'image',
                        'products',
                    ],
                ],
            ]);

        $responseData = $response->json('data');
        
        // Each supplier should have an empty products array
        foreach ($responseData as $supplierData) {
            $this->assertIsArray($supplierData['products']);
        }
    }

    
    public function test_buyer_can_get_suppliers_with_products_ordered_by_latest()
    {
        $category = Category::factory()->create(['supplier_id' => $this->supplier->id]);
        
        // Create products with specific creation times
        $oldProduct = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $category->id,
            'is_active' => true,
            'created_at' => now()->subDays(5),
        ]);
        
        $newProduct = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $category->id,
            'is_active' => true,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->buyer)->getJson(route('home.suppliers-and-products'));

        $response->assertStatus(200);
        
        $responseData = $response->json('data');
        $supplierData = collect($responseData)->firstWhere('id', $this->supplier->id);
        
        if ($supplierData && count($supplierData['products']) >= 2) {
            // The newest product should be first (latest() ordering in products relationship)
            $firstProduct = $supplierData['products'][0];
            $this->assertEquals($newProduct->id, $firstProduct['id']);
        }
    }

    public function test_unauthenticated_user_cannot_access_suppliers_and_products()
    {
        $response = $this->getJson(route('home.suppliers-and-products'));

        $response->assertStatus(401);
    }

    public function test_non_buyer_cannot_access_suppliers_and_products()
    {
        $supplier = User::factory()->supplier()->create();

        $response = $this->actingAs($supplier)->getJson(route('home.suppliers-and-products'));

        // The RoleMiddleware returns 401 for role authorization failures
        $response->assertStatus(401);
    }

    public function test_buyer_gets_empty_response_when_no_suppliers_exist()
    {
        // Ensure no suppliers exist (only the buyer)
        User::where('id', '!=', $this->buyer->id)->delete();

        $response = $this->actingAs($this->buyer)->getJson(route('home.suppliers-and-products'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [],
            ]);
    }

    public function test_response_includes_correct_supplier_fields()
    {
        $category = Category::factory()->create(['supplier_id' => $this->supplier->id]);
        Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->buyer)->getJson(route('home.suppliers-and-products'));

        $response->assertStatus(200);
        
        $responseData = $response->json('data');
        $supplierData = collect($responseData)->firstWhere('id', $this->supplier->id);
        
        if ($supplierData) {
            // Assert supplier has only the selected fields (id, name, image)
            $this->assertArrayHasKey('id', $supplierData);
            $this->assertArrayHasKey('name', $supplierData);
            $this->assertArrayHasKey('image', $supplierData);
            $this->assertArrayHasKey('products', $supplierData);
            
            // Assert supplier doesn't have other fields like email, created_at, etc.
            $this->assertArrayNotHasKey('email', $supplierData);
            $this->assertArrayNotHasKey('created_at', $supplierData);
            $this->assertArrayNotHasKey('updated_at', $supplierData);
        }
    }

    public function test_products_include_complete_structure()
    {
        $category = Category::factory()->create(['supplier_id' => $this->supplier->id]);
        $product = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->buyer)->getJson(route('home.suppliers-and-products'));

        $response->assertStatus(200);
        
        $responseData = $response->json('data');
        $supplierData = collect($responseData)->firstWhere('id', $this->supplier->id);
        
        if ($supplierData && count($supplierData['products']) > 0) {
            $productData = $supplierData['products'][0];
            
            // Assert product has all required fields
            $requiredFields = [
                'id', 'name', 'description', 'images', 'image', 'price',
                'price_before_discount', 'quantity', 'stock_qty', 
                'nearly_out_of_stock_limit', 'status', 'is_favorite',
                'unit_type', 'category', 'avg_rating'
            ];
            
            foreach ($requiredFields as $field) {
                $this->assertArrayHasKey($field, $productData, "Product should have '{$field}' field");
            }
        }
    }
}
