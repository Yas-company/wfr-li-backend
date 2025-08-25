<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1\Category;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Category;
use App\Models\Field;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetAllCategoriesControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $supplier;
    protected User $buyer;
    protected Field $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept-Language' => 'en',
        ]);

        $this->supplier = User::factory()->create([
            'role' => UserRole::SUPPLIER,
            'status' => UserStatus::APPROVED,
        ]);

        $this->buyer = User::factory()->create([
            'role' => UserRole::BUYER,
            'status' => UserStatus::APPROVED,
        ]);

        $this->field = Field::factory()->create();
    }

    public function test_supplier_can_get_all_categories()
    {
        $this->createCategories();

        $response = $this->actingAs($this->supplier)
            ->getJson(route('supplier.categories.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name'
                    ],
                ],
            ]);
    }

    protected function createCategories()
    {
        Category::factory()->count(5)->create([
            'field_id' => $this->field->id,
        ]);
    }

}
