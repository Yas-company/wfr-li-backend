<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1\Category;

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
            ->getJson(route('categories.index').'?filter[name]=Electronics');

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
            ->getJson(route('categories.index').'?filter[field_id]='.$field1->id);

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
            ->getJson(route('categories.index').'?filter[name]=إلكترونيات');

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
            ->getJson(route('categories.index').'?include[]=products');

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
            ->getJson(route('categories.index').'?include[]=field');

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

    // Tests for getSupplierCategories() endpoint
    public function test_supplier_can_get_their_categories()
    {
        // Associate supplier with field and create categories
        $this->supplier->fields()->attach($this->field->id);

        $category1 = Category::factory()->create([
            'field_id' => $this->field->id,
            'name' => ['en' => 'Electronics', 'ar' => 'إلكترونيات'],
        ]);

        $category2 = Category::factory()->create([
            'field_id' => $this->field->id,
            'name' => ['en' => 'Accessories', 'ar' => 'ملحقات'],
        ]);

        // Create category in different field (should not be returned)
        $otherField = Field::factory()->create();
        $otherCategory = Category::factory()->create([
            'field_id' => $otherField->id,
            'name' => ['en' => 'Food', 'ar' => 'طعام'],
        ]);

        $response = $this->actingAs($this->supplier)
            ->getJson(route('supplier.categories.supplier'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                    ],
                ],
            ]);

        $responseData = $response->json('data');

        // Should return 2 categories from the supplier's field
        $this->assertCount(2, $responseData);

        $categoryIds = collect($responseData)->pluck('id')->toArray();
        $this->assertContains($category1->id, $categoryIds);
        $this->assertContains($category2->id, $categoryIds);
        $this->assertNotContains($otherCategory->id, $categoryIds);
    }

    public function test_supplier_with_multiple_fields_gets_all_related_categories()
    {
        $field2 = Field::factory()->create();

        // Associate supplier with both fields
        $this->supplier->fields()->attach([$this->field->id, $field2->id]);

        $category1 = Category::factory()->create(['field_id' => $this->field->id]);
        $category2 = Category::factory()->create(['field_id' => $field2->id]);

        $response = $this->actingAs($this->supplier)
            ->getJson(route('supplier.categories.supplier'));

        $response->assertStatus(200);

        $responseData = $response->json('data');
        $this->assertCount(2, $responseData);

        $categoryIds = collect($responseData)->pluck('id')->toArray();
        $this->assertContains($category1->id, $categoryIds);
        $this->assertContains($category2->id, $categoryIds);
    }

    public function test_supplier_with_no_fields_gets_empty_categories()
    {
        // Supplier has no fields associated
        $response = $this->actingAs($this->supplier)
            ->getJson(route('supplier.categories.supplier'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [],
            ]);
    }

    public function test_buyer_cannot_access_supplier_categories()
    {
        $response = $this->actingAs($this->buyer)
            ->getJson(route('supplier.categories.supplier'));

        // Should return 401 due to RoleMiddleware restricting to suppliers only
        $response->assertStatus(401);
    }

    public function test_unauthenticated_user_cannot_access_supplier_categories()
    {
        $response = $this->getJson(route('supplier.categories.supplier'));

        $response->assertStatus(401);
    }

    public function test_supplier_categories_response_structure()
    {
        $this->supplier->fields()->attach($this->field->id);

        Category::factory()->create([
            'field_id' => $this->field->id,
            'name' => ['en' => 'Test Category', 'ar' => 'فئة تجريبية'],
        ]);

        $response = $this->actingAs($this->supplier)
            ->getJson(route('supplier.categories.supplier'));

        $response->assertStatus(200);

        $responseData = $response->json('data');

        if (count($responseData) > 0) {
            $category = $responseData[0];

            // Verify CategorySelectResource structure
            $this->assertArrayHasKey('id', $category);
            $this->assertArrayHasKey('name', $category);
            $this->assertIsString($category['name']); // Name is translated string based on current locale
            $this->assertNotEmpty($category['name']);

            // Should not contain extra fields
            $this->assertArrayNotHasKey('field_id', $category);
            $this->assertArrayNotHasKey('created_at', $category);
            $this->assertArrayNotHasKey('updated_at', $category);
            $this->assertArrayNotHasKey('field', $category);
        }
    }

    public function test_supplier_categories_success_message()
    {
        $this->supplier->fields()->attach($this->field->id);

        $response = $this->actingAs($this->supplier)
            ->getJson(route('supplier.categories.supplier'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => __('messages.categories.retrieved_successfully'),
            ]);
    }

    public function test_supplier_categories_filtered_by_field_correctly()
    {
        $field2 = Field::factory()->create();
        $field3 = Field::factory()->create();

        // Associate supplier with only field1 and field2
        $this->supplier->fields()->attach([$this->field->id, $field2->id]);

        // Create categories in all three fields
        $category1 = Category::factory()->create(['field_id' => $this->field->id]);
        $category2 = Category::factory()->create(['field_id' => $field2->id]);
        $category3 = Category::factory()->create(['field_id' => $field3->id]); // Should not be returned

        $response = $this->actingAs($this->supplier)
            ->getJson(route('supplier.categories.supplier'));

        $response->assertStatus(200);

        $responseData = $response->json('data');
        $categoryIds = collect($responseData)->pluck('id')->toArray();

        // Should only get categories from supplier's fields
        $this->assertContains($category1->id, $categoryIds);
        $this->assertContains($category2->id, $categoryIds);
        $this->assertNotContains($category3->id, $categoryIds);
    }

    public function test_supplier_categories_return_localized_names()
    {
        $this->supplier->fields()->attach($this->field->id);

        $category = Category::factory()->create([
            'field_id' => $this->field->id,
            'name' => ['en' => 'Test Category', 'ar' => 'فئة تجريبية'],
        ]);

        // Test with English locale (default)
        $response = $this->actingAs($this->supplier)
            ->withHeader('Accept-Language', 'en')
            ->getJson(route('supplier.categories.supplier'));

        $response->assertStatus(200);

        $responseData = $response->json('data');
        if (count($responseData) > 0) {
            $returnedCategory = collect($responseData)->firstWhere('id', $category->id);
            $this->assertNotNull($returnedCategory);
            $this->assertEquals('Test Category', $returnedCategory['name']);
        }
    }

    public function test_different_suppliers_get_different_categories()
    {
        $field2 = Field::factory()->create();

        // Associate suppliers with different fields
        $this->supplier->fields()->attach($this->field->id);
        $this->otherSupplier->fields()->attach($field2->id);

        $category1 = Category::factory()->create(['field_id' => $this->field->id]);
        $category2 = Category::factory()->create(['field_id' => $field2->id]);

        // Test first supplier
        $response1 = $this->actingAs($this->supplier)
            ->getJson(route('supplier.categories.supplier'));

        $response1->assertStatus(200);
        $data1 = $response1->json('data');
        $categoryIds1 = collect($data1)->pluck('id')->toArray();

        $this->assertContains($category1->id, $categoryIds1);
        $this->assertNotContains($category2->id, $categoryIds1);

        // Test second supplier
        $response2 = $this->actingAs($this->otherSupplier)
            ->getJson(route('supplier.categories.supplier'));

        $response2->assertStatus(200);
        $data2 = $response2->json('data');
        $categoryIds2 = collect($data2)->pluck('id')->toArray();

        $this->assertNotContains($category1->id, $categoryIds2);
        $this->assertContains($category2->id, $categoryIds2);
    }
}
