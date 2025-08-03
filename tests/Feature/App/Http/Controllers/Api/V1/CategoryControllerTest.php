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
use Illuminate\Http\UploadedFile;
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

    public function test_approved_supplier_can_create_category()
    {
        $categoryData = [
            'name' => [
                'en' => 'Electronics',
                'ar' => 'إلكترونيات',
            ],
            'field_id' => $this->field->id,
            'image' => UploadedFile::fake()->create('category.jpg', 100, 'image/jpeg'),
        ];

        $response = $this->actingAs($this->supplier)
            ->postJson(route('categories.store'), $categoryData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'image',
                    'supplier_id',
                    'field_id',
                    'field',
                    'products_count',
                ],
            ]);

        $this->assertDatabaseHas('categories', [
            'supplier_id' => $this->supplier->id,
            'field_id' => $this->field->id,
        ]);

    }

    public function test_supplier_cannot_create_category_without_required_fields()
    {
        $response = $this->actingAs($this->supplier)
            ->postJson(route('categories.store'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'field_id', 'image']);
    }

    public function test_supplier_cannot_create_category_with_invalid_field_id()
    {
        $categoryData = [
            'name' => [
                'en' => 'Electronics',
                'ar' => 'إلكترونيات',
            ],
            'field_id' => 999, // Non-existent field
            'image' => UploadedFile::fake()->create('category.jpg', 100, 'image/jpeg'),
        ];

        $response = $this->actingAs($this->supplier)
            ->postJson(route('categories.store'), $categoryData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['field_id']);
    }

    public function test_unapproved_supplier_cannot_create_category()
    {
        $unapprovedSupplier = User::factory()->create([
            'role' => UserRole::SUPPLIER,
            'status' => UserStatus::PENDING,
        ]);

        $categoryData = [
            'name' => [
                'en' => 'Electronics',
                'ar' => 'إلكترونيات',
            ],
            'field_id' => $this->field->id,
            'image' => UploadedFile::fake()->create('category.jpg', 100, 'image/jpeg'),
        ];

        $response = $this->actingAs($unapprovedSupplier)
            ->postJson(route('categories.store'), $categoryData);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => __('messages.errors.unauthorized_category_creation'),
            ]);
    }

    public function test_buyer_cannot_create_category()
    {
        $categoryData = [
            'name' => [
                'en' => 'Electronics',
                'ar' => 'إلكترونيات',
            ],
            'field_id' => $this->field->id,
            'image' => UploadedFile::fake()->create('category.jpg', 100, 'image/jpeg'),
        ];

        $response = $this->actingAs($this->buyer)
            ->postJson(route('categories.store'), $categoryData);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => __('messages.errors.unauthorized_category_creation'),
            ]);
    }

    public function test_supplier_can_view_their_categories()
    {
        // Create categories for the supplier
        Category::factory()->count(3)->create([
            'supplier_id' => $this->supplier->id,
            'field_id' => $this->field->id,
        ]);

        // Create categories for another supplier (should not be visible)
        Category::factory()->count(2)->create([
            'supplier_id' => $this->otherSupplier->id,
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
                        'supplier_id',
                        'field_id',
                        'field',
                        'products_count',
                    ],
                ],
                'links',
            ]);

        // Should only see their own categories
        $this->assertEquals(3, count($response->json('data')));
    }

    public function test_supplier_can_view_specific_category()
    {
        $category = Category::factory()->create([
            'supplier_id' => $this->supplier->id,
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
                    'supplier_id',
                    'field_id',
                    'field',
                    'products_count',
                ],
            ]);
    }

    public function test_supplier_cannot_view_other_suppliers_category()
    {
        $category = Category::factory()->create([
            'supplier_id' => $this->otherSupplier->id,
            'field_id' => $this->field->id,
        ]);

        $response = $this->actingAs($this->supplier)
            ->getJson(route('categories.show', $category));

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => __('messages.errors.unauthorized_category_ownership'),
            ]);
    }

    public function test_supplier_can_update_their_category()
    {
        $category = Category::factory()->create([
            'supplier_id' => $this->supplier->id,
            'field_id' => $this->field->id,
        ]);

        $updateData = [
            'name' => [
                'en' => 'Updated Electronics',
                'ar' => 'إلكترونيات محدثة',
            ],
            'field_id' => $this->field->id,
            'image' => UploadedFile::fake()->create('updated_category.jpg', 100, 'image/jpeg'),
        ];

        $response = $this->actingAs($this->supplier)
            ->postJson(route('categories.update', $category), $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'image',
                    'supplier_id',
                    'field_id',
                ],
            ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'supplier_id' => $this->supplier->id,
        ]);
    }

    public function test_supplier_cannot_update_other_suppliers_category()
    {
        $category = Category::factory()->create([
            'supplier_id' => $this->otherSupplier->id,
            'field_id' => $this->field->id,
        ]);

        $updateData = [
            'name' => [
                'en' => 'Updated Electronics',
                'ar' => 'إلكترونيات محدثة',
            ],
            'field_id' => $this->field->id,
        ];

        $response = $this->actingAs($this->supplier)
            ->postJson(route('categories.update', $category), $updateData);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => __('messages.errors.unauthorized_category_ownership'),
            ]);
    }

    public function test_supplier_can_delete_empty_category()
    {
        $category = Category::factory()->create([
            'supplier_id' => $this->supplier->id,
            'field_id' => $this->field->id,
        ]);

        $response = $this->actingAs($this->supplier)
            ->deleteJson(route('categories.destroy', $category));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Category deleted successfully',
            ]);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_supplier_cannot_delete_category_with_products()
    {
        $category = Category::factory()->create([
            'supplier_id' => $this->supplier->id,
            'field_id' => $this->field->id,
        ]);

        // Create a product in this category
        Product::factory()->create([
            'category_id' => $category->id,
            'supplier_id' => $this->supplier->id,
        ]);

        $response = $this->actingAs($this->supplier)
            ->deleteJson(route('categories.destroy', $category));

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => __('messages.errors.category_has_products'),
            ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_supplier_cannot_delete_other_suppliers_category()
    {
        $category = Category::factory()->create([
            'supplier_id' => $this->otherSupplier->id,
            'field_id' => $this->field->id,
        ]);

        $response = $this->actingAs($this->supplier)
            ->deleteJson(route('categories.destroy', $category));

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => __('messages.errors.unauthorized_category_ownership'),
            ]);
    }

    public function test_unauthenticated_user_cannot_access_categories()
    {
        $response = $this->getJson(route('categories.index'));
        $response->assertStatus(401);

        $response = $this->postJson(route('categories.store'), []);
        $response->assertStatus(401);
    }

    public function test_category_includes_products_count()
    {
        $category = Category::factory()->create([
            'supplier_id' => $this->supplier->id,
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

    public function test_category_image_upload_validation()
    {
        $categoryData = [
            'name' => [
                'en' => 'Electronics',
                'ar' => 'إلكترونيات',
            ],
            'field_id' => $this->field->id,
            'image' => UploadedFile::fake()->create('document.pdf', 1000), // Invalid file type
        ];

        $response = $this->actingAs($this->supplier)
            ->postJson(route('categories.store'), $categoryData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['image']);
    }

    public function test_category_name_validation()
    {
        $categoryData = [
            'name' => [
                'en' => '', // Empty English name
                'ar' => 'إلكترونيات',
            ],
            'field_id' => $this->field->id,
            'image' => UploadedFile::fake()->create('category.jpg', 100, 'image/jpeg'),
        ];

        $response = $this->actingAs($this->supplier)
            ->postJson(route('categories.store'), $categoryData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name.en']);
    }

    public function test_can_search_categories_using_index()
    {
        // Create categories with specific names
        Category::factory()->create([
            'name' => ['en' => 'Electronics', 'ar' => 'إلكترونيات'],
            'supplier_id' => $this->supplier->id,
            'field_id' => $this->field->id,
        ]);

        Category::factory()->create([
            'name' => ['en' => 'Clothing', 'ar' => 'ملابس'],
            'supplier_id' => $this->supplier->id,
            'field_id' => $this->field->id,
        ]);

        Category::factory()->create([
            'name' => ['en' => 'Electronics Accessories', 'ar' => 'ملحقات إلكترونيات'],
            'supplier_id' => $this->supplier->id,
            'field_id' => $this->field->id,
        ]);

        // Test search using index endpoint
        $response = $this->actingAs($this->supplier)
            ->getJson(route('categories.index', ['search' => 'Electronics']));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'image',
                        'supplier_id',
                        'field_id',
                        'field',
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
                str_contains(strtolower($category['name']['en']), 'electronics') ||
                str_contains(strtolower($category['name']['ar']), 'إلكترونيات')
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
            'supplier_id' => $this->supplier->id,
        ]);

        // Create categories for field2
        Category::factory()->count(2)->create([
            'field_id' => $field2->id,
            'supplier_id' => $this->supplier->id,
        ]);

        // Test filtering by field using index endpoint
        $response = $this->actingAs($this->supplier)
            ->getJson(route('categories.index', ['field_id' => $field1->id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'image',
                        'supplier_id',
                        'field_id',
                        'field',
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

    public function test_search_and_field_filter_validation()
    {
        // Test invalid search term (too short)
        $response = $this->actingAs($this->supplier)
            ->getJson(route('categories.index', ['search' => 'a']));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['search']);

        // Test invalid field_id (non-existent)
        $response = $this->actingAs($this->supplier)
            ->getJson(route('categories.index', ['field_id' => 999]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['field_id']);
    }

    public function test_search_with_arabic_text()
    {
        // Create categories with Arabic names
        Category::factory()->create([
            'name' => ['en' => 'Electronics', 'ar' => 'إلكترونيات'],
            'supplier_id' => $this->supplier->id,
            'field_id' => $this->field->id,
        ]);

        Category::factory()->create([
            'name' => ['en' => 'Clothing', 'ar' => 'ملابس'],
            'supplier_id' => $this->supplier->id,
            'field_id' => $this->field->id,
        ]);

        // Test search with Arabic text
        $response = $this->actingAs($this->supplier)
            ->getJson(route('categories.index', ['search' => 'إلكترونيات']));

        $response->assertStatus(200);

        $categories = $response->json('data');
        $this->assertGreaterThan(0, count($categories));

        // Verify Arabic search works
        foreach ($categories as $category) {
            $this->assertTrue(
                str_contains($category['name']['ar'], 'إلكترونيات')
            );
        }
    }
}
