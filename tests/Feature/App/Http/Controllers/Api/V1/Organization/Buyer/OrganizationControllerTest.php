<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1\Product\Buyer;

use Tests\TestCase;
use App\Models\User;
use App\Models\Organization;
use App\Enums\Organization\OrganizationRole;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrganizationControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $supplier;
    protected User $buyer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept-Language' => 'en',
        ]);

        $this->buyer = User::factory()->buyer()->create();
        $this->supplier = User::factory()->supplier()->create();
    }

    public function test_unauthenticated_user_cannot_register_as_organization()
    {
        $response = $this->postJson(route('buyer.organizations.store'), [
            'name' => 'Organization Name',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212121',
        ]);

        $response->assertStatus(401);
    }

    public function test_suppliers_cannot_register_as_organization()
    {
        $response = $this->actingAs($this->supplier)->postJson(route('buyer.organizations.store'), [
            'name' => 'Organization Name',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212121',
        ]);

        $response->assertStatus(401);
    }

    public function test_buyer_can_register_as_organization()
    {
        $response = $this->actingAs($this->buyer)->postJson(route('buyer.organizations.store'), [
            'name' => 'Organization Name',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212121',
        ]);

        $organizationId = $response->json('data.id');

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'tax_number',
                    'commercial_register_number',
                    'owner',
                    'users',
                ],
            ]);

        $this->assertDatabaseHas('organizations', [
            'name' => 'Organization Name',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212121',
            'created_by' => $this->buyer->id,
        ]);

        $this->assertDatabaseHas('organization_user', [
            'organization_id' => $organizationId,
            'user_id' => $this->buyer->id,
            'role' => OrganizationRole::OWNER,
        ]);
    }

    public function test_buyer_cannot_register_as_organization_with_invalid_tax_number()
    {
        $response = $this->actingAs($this->buyer)->postJson(route('buyer.organizations.store'), [
            'name' => 'Organization Name',
            'tax_number' => '3212121',
            'commercial_register_number' => '1212121',
        ]);

        $response->assertJsonValidationErrors('tax_number');

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'tax_number' => [
                        "The tax number field must be 16 digits."
                    ],
                ],
            ]);

        $this->assertDatabaseMissing('organizations', [
            'name' => 'Organization Name',
            'tax_number' => '3212121',
            'commercial_register_number' => '1212121',
            'created_by' => $this->buyer->id,
        ]);

        $response = $this->actingAs($this->buyer)->postJson(route('buyer.organizations.store'), [
            'name' => 'Organization Name',
            'tax_number' => '1212121212121212',
            'commercial_register_number' => '1212121',
        ]);

        $response->assertJsonValidationErrors('tax_number');

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'tax_number' => [
                        "The tax number field must start with one of the following: 3."
                    ],
                ],
            ]);

        $this->assertDatabaseMissing('organizations', [
            'name' => 'Organization Name',
            'tax_number' => '3212121',
            'commercial_register_number' => '1212121',
            'created_by' => $this->buyer->id,
        ]);
    }

    public function test_buyer_cannot_register_as_organization_with_invalid_commercial_register_number()
    {
        $response = $this->actingAs($this->buyer)->postJson(route('buyer.organizations.store'), [
            'name' => 'Organization Name',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '123456',
        ]);

        $response->assertJsonValidationErrors('commercial_register_number');

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'commercial_register_number' => [
                        "The commercial register number field must be 7 digits."
                    ],
                ],
            ]);

        $this->assertDatabaseMissing('organizations', [
            'name' => 'Organization Name',
            'tax_number' => '3212121',
            'commercial_register_number' => '1212121',
            'created_by' => $this->buyer->id,
        ]);
    }

    public function test_buyer_cannot_register_as_organization_with_existing_name()
    {
        Organization::factory()->create([
            'name' => 'Organization Name',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212121',
        ]);

        $response = $this->actingAs($this->buyer)->postJson(route('buyer.organizations.store'), [
            'name' => 'Organization Name',
            'tax_number' => '3212121212121211',
            'commercial_register_number' => '2121211',
        ]);

        $response->assertJsonValidationErrors('name');

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'name' => [
                        "The name has already been taken."
                    ],
                ],
            ]);

        $this->assertDatabaseMissing('organizations', [
            'name' => 'Organization Name',
            'tax_number' => '3212121212121211',
            'commercial_register_number' => '2121211',
            'created_by' => $this->buyer->id,
        ]);
    }

    public function test_buyer_cannot_register_as_organization_with_existing_tax_number()
    {
        Organization::factory()->create([
            'name' => 'Organization Name',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212121',
        ]);

        $response = $this->actingAs($this->buyer)->postJson(route('buyer.organizations.store'), [
            'name' => 'Different Organization Name',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '2121211',
        ]);

        $response->assertJsonValidationErrors('tax_number');

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'tax_number' => [
                        "The tax number has already been taken."
                    ],
                ],
            ]);

        $this->assertDatabaseMissing('organizations', [
            'name' => 'Different Organization Name',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '2121211',
            'created_by' => $this->buyer->id,
        ]);
    }

    public function test_buyer_cannot_register_as_organization_with_existing_commercial_register_number()
    {
        Organization::factory()->create([
            'name' => 'Organization Name',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212121',
        ]);

        $response = $this->actingAs($this->buyer)->postJson(route('buyer.organizations.store'), [
            'name' => 'Different Organization Name',
            'tax_number' => '3212121212121211',
            'commercial_register_number' => '1212121',
        ]);


        $response->assertJsonValidationErrors('commercial_register_number');

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'commercial_register_number' => [
                        "The commercial register number has already been taken."
                    ],
                ],
            ]);

        $this->assertDatabaseMissing('organizations', [
            'name' => 'Different Organization Name',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212121',
            'created_by' => $this->buyer->id,
        ]);
    }
}
