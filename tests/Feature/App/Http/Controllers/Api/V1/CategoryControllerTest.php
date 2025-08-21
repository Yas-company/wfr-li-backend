<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Category;
use App\Models\Field;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $supplier;

    protected User $otherSupplier;

    protected User $buyer;

    protected User $admin;

    protected Field $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept-Language' => 'en',
        ]);

        // Create test users
        $this->supplier = User::factory()->create([
            'role' => UserRole::SUPPLIER,
            'status' => UserStatus::APPROVED,
        ]);

        $this->otherSupplier = User::factory()->create([
            'role' => UserRole::SUPPLIER,
            'status' => UserStatus::APPROVED,
        ]);

        $this->buyer = User::factory()->create([
            'role' => UserRole::BUYER,
            'status' => UserStatus::APPROVED,
        ]);

        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN,
            'status' => UserStatus::APPROVED,
        ]);

        // Create a field for categories
        $this->field = Field::factory()->create();

        // Setup fake storage
        Storage::fake('public');
    }


    public function test_user_can_view_categories()
    {
        // Create categories for the supplier
        Category::factory()->count(3)->create([
            'field_id' => $this->field->id,
        ]);

        $response = $this->actingAs($this->supplier)
            ->getJson(route('categories.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'image',
                        'field_id',
                        'products_count',
                    ],
                ],
                'links',
            ]);

        // Should only see their own categories
        $this->assertEquals(3, count($response->json('data')));
    }

    public function test_user_can_view_specific_category()
    {
        $category = Category::factory()->create([
            'field_id' => $this->field->id,
        ]);

        $response = $this->actingAs($this->supplier)
            ->getJson(route('categories.show', $category));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'image',
                    'field_id',
                    'field',
                    'products_count',
                ],
            ]);
    }

    public function test_category_includes_products_count()
    {
        $category = Category::factory()->create([
            'field_id' => $this->field->id,
        ]);

        // Create products in this category
        Product::factory()->count(3)->create([
            'category_id' => $category->id,
            'supplier_id' => $this->supplier->id,
        ]);

        $response = $this->actingAs($this->supplier)
            ->getJson(route('categories.show', $category));

        $response->assertStatus(200);
        $this->assertEquals(3, $response->json('data.products_count'));
    }

    public function test_can_search_categories_using_index()
    {
        // Create categories with specific names
        Category::factory()->create([
            'name' => ['en' => 'Electronics', 'ar' => 'إلكترونيات'],
            'field_id' => $this->field->id,
        ]);

        Category::factory()->create([
            'name' => ['en' => 'Clothing', 'ar' => 'ملابس'],
            'field_id' => $this->field->id,
        ]);

        Category::factory()->create([
            'name' => ['en' => 'Electronics Accessories', 'ar' => 'ملحقات إلكترونيات'],
            'field_id' => $this->field->id,
        ]);

        // Test search using index endpoint
        $response = $this->actingAs($this->supplier)
            ->getJson(route('categories.index') . '?filter[name]=Electronics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'image',
                        'field_id',
                        'products_count',
                    ],
                ],
                'links',
            ]);

        // Should find categories containing 'Electronics'
        $categories = $response->json('data');
        $this->assertGreaterThan(0, count($categories));

        // Verify all returned categories contain 'Electronics' in name
        foreach ($categories as $category) {
            $this->assertTrue(
                str_contains(strtolower($category['name']), 'electronics')
            );
        }
    }

    public function test_can_get_categories_by_field_using_index()
    {
        $field1 = Field::factory()->create();
        $field2 = Field::factory()->create();

        // Create categories for field1
        Category::factory()->count(3)->create([
            'field_id' => $field1->id,
        ]);

        // Create categories for field2
        Category::factory()->count(2)->create([
            'field_id' => $field2->id,
        ]);

        // Test filtering by field using index endpoint
        $response = $this->actingAs($this->supplier)
            ->getJson(route('categories.index') . '?filter[field_id]=' . $field1->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'image',
                        'field_id',
                        'products_count',
                    ],
                ],
                'links',
            ]);

        // Should only get categories for the specified field
        $categories = $response->json('data');
        $this->assertEquals(3, count($categories));

        foreach ($categories as $category) {
            $this->assertEquals($field1->id, $category['field_id']);
        }
    }

    public function test_search_with_arabic_text()
    {
        // Create categories with Arabic names
        Category::factory()->create([
            'name' => ['en' => 'Electronics', 'ar' => 'إلكترونيات'],
            'field_id' => $this->field->id,
        ]);

        Category::factory()->create([
            'name' => ['en' => 'Clothing', 'ar' => 'ملابس'],
            'field_id' => $this->field->id,
        ]);

        // Test search with Arabic text
        $response = $this->actingAs($this->supplier)
            ->withHeader('Accept-Language', 'ar')
            ->getJson(route('categories.index'). '?filter[name]=إلكترونيات');

        $response->assertStatus(200);

        $categories = $response->json('data');
        $this->assertGreaterThan(0, count($categories));

        // Verify Arabic search works
        foreach ($categories as $category) {
            $this->assertTrue(
                str_contains($category['name'], 'إلكترونيات')
            );
        }
    }

    public function test_user_can_include_products()
    {
        Category::factory()->create([
            'field_id' => $this->field->id,
        ]);

        $response = $this->actingAs($this->supplier)
            ->getJson(route('categories.index') . '?include[]=products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'image',
                        'field_id',
                        'products_count',
                        'products',
                    ],
                ],
                'links',
            ]);
    }

    public function test_user_can_include_field()
    {
        Category::factory()->create([
            'field_id' => $this->field->id,
        ]);

        $response = $this->actingAs($this->supplier)
            ->getJson(route('categories.index') . '?include[]=field');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'image',
                        'field_id',
                        'products_count',
                        'field',
                    ],
                ],
                'links',
            ]);
    }
}
