<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1\Product\Buyer;

use App\Enums\Organization\OrganizationRole;
use App\Enums\Organization\OrganizationStatus;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

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
                    'members',
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
                        'The tax number field must be 16 digits.',
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
                        'The tax number field must start with one of the following: 3.',
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
                        'The commercial register number field must be 7 digits.',
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
            'status' => OrganizationStatus::APPROVED,
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
                        'The name has already been taken.',
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
            'status' => OrganizationStatus::APPROVED,
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
                        'The tax number has already been taken.',
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
            'status' => OrganizationStatus::APPROVED,
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
                        'The commercial register number has already been taken.',
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

    public function test_buyer_can_register_as_organization_with_existing_pending_name()
    {
        Organization::factory()->create([
            'name' => 'Organization Name',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212121',
            'status' => OrganizationStatus::PENDING,
        ]);

        $response = $this->actingAs($this->buyer)->postJson(route('buyer.organizations.store'), [
            'name' => 'Organization Name',
            'tax_number' => '3212121212121211',
            'commercial_register_number' => '1212122',
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
                    'members',
                ],
            ]);

        $this->assertDatabaseHas('organizations', [
            'name' => 'Organization Name',
            'tax_number' => '3212121212121211',
            'commercial_register_number' => '1212122',
            'created_by' => $this->buyer->id,
        ]);

        $this->assertDatabaseHas('organization_user', [
            'organization_id' => $organizationId,
            'user_id' => $this->buyer->id,
            'role' => OrganizationRole::OWNER,
        ]);
    }

    public function test_buyer_can_register_as_organization_with_existing_pending_tax_number()
    {
        Organization::factory()->create([
            'name' => 'Organization Name',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212121',
            'status' => OrganizationStatus::PENDING,
        ]);

        $response = $this->actingAs($this->buyer)->postJson(route('buyer.organizations.store'), [
            'name' => 'Different Organization Name',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212122',
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
                    'members',
                ],
            ]);

        $this->assertDatabaseHas('organizations', [
            'name' => 'Different Organization Name',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212122',
            'created_by' => $this->buyer->id,
        ]);

        $this->assertDatabaseHas('organization_user', [
            'organization_id' => $organizationId,
            'user_id' => $this->buyer->id,
            'role' => OrganizationRole::OWNER,
        ]);
    }

    public function test_buyer_can_register_as_organization_with_existing_pending_commercial_register_number()
    {
        Organization::factory()->create([
            'name' => 'Organization Name',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212121',
            'status' => OrganizationStatus::PENDING,
        ]);

        $response = $this->actingAs($this->buyer)->postJson(route('buyer.organizations.store'), [
            'name' => 'Different Organization Name',
            'tax_number' => '3212121212121211',
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
                    'members',
                ],
            ]);

        $this->assertDatabaseHas('organizations', [
            'name' => 'Different Organization Name',
            'tax_number' => '3212121212121211',
            'commercial_register_number' => '1212121',
            'created_by' => $this->buyer->id,
        ]);

        $this->assertDatabaseHas('organization_user', [
            'organization_id' => $organizationId,
            'user_id' => $this->buyer->id,
            'role' => OrganizationRole::OWNER,
        ]);
    }

    public function test_buyer_can_register_as_organization_with_existing_rejected_name()
    {
        Organization::factory()->create([
            'name' => 'Organization Name',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212121',
            'status' => OrganizationStatus::REJECTED,
        ]);

        $response = $this->actingAs($this->buyer)->postJson(route('buyer.organizations.store'), [
            'name' => 'Organization Name',
            'tax_number' => '3212121212121211',
            'commercial_register_number' => '1212122',
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
                    'members',
                ],
            ]);

        $this->assertDatabaseHas('organizations', [
            'name' => 'Organization Name',
            'tax_number' => '3212121212121211',
            'commercial_register_number' => '1212122',
            'created_by' => $this->buyer->id,
        ]);

        $this->assertDatabaseHas('organization_user', [
            'organization_id' => $organizationId,
            'user_id' => $this->buyer->id,
            'role' => OrganizationRole::OWNER,
        ]);
    }

    public function test_buyer_can_register_as_organization_with_existing_rejected_tax_number()
    {
        Organization::factory()->create([
            'name' => 'Organization Name',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212121',
            'status' => OrganizationStatus::REJECTED,
        ]);

        $response = $this->actingAs($this->buyer)->postJson(route('buyer.organizations.store'), [
            'name' => 'Different Organization Name',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212122',
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
                    'members',
                ],
            ]);

        $this->assertDatabaseHas('organizations', [
            'name' => 'Different Organization Name',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212122',
            'created_by' => $this->buyer->id,
        ]);

        $this->assertDatabaseHas('organization_user', [
            'organization_id' => $organizationId,
            'user_id' => $this->buyer->id,
            'role' => OrganizationRole::OWNER,
        ]);
    }

    public function test_buyer_can_register_as_organization_with_existing_rejected_commercial_register_number()
    {
        Organization::factory()->create([
            'name' => 'Organization Name',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212121',
            'status' => OrganizationStatus::REJECTED,
        ]);

        $response = $this->actingAs($this->buyer)->postJson(route('buyer.organizations.store'), [
            'name' => 'Different Organization Name',
            'tax_number' => '3212121212121211',
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
                    'members',
                ],
            ]);

        $this->assertDatabaseHas('organizations', [
            'name' => 'Different Organization Name',
            'tax_number' => '3212121212121211',
            'commercial_register_number' => '1212121',
            'created_by' => $this->buyer->id,
        ]);

        $this->assertDatabaseHas('organization_user', [
            'organization_id' => $organizationId,
            'user_id' => $this->buyer->id,
            'role' => OrganizationRole::OWNER,
        ]);
    }

    public function test_buyer_cannot_register_as_organization_when_having_approved_organization()
    {
        Organization::factory()->create([
            'name' => 'Organization Name',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212121',
            'status' => OrganizationStatus::APPROVED,
            'created_by' => $this->buyer->id,
        ]);

        $response = $this->actingAs($this->buyer)->postJson(route('buyer.organizations.store'), [
            'name' => 'Different Organization Name',
            'tax_number' => '3212121212121211',
            'commercial_register_number' => '1212122',
        ]);

        $response->assertStatus(422);
    }

    public function test_buyer_can_convert_rejected_organization_to_pending()
    {
        $organization = Organization::factory()->create([
            'name' => 'Organization Name',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212121',
            'status' => OrganizationStatus::REJECTED,
            'created_by' => $this->buyer->id,
        ]);

        $response = $this->actingAs($this->buyer)->postJson(route('buyer.organizations.store'), [
            'name' => 'Organization Name',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212121',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'tax_number',
                    'commercial_register_number',
                    'owner',
                    'members',
                ],
            ]);

        $this->assertDatabaseHas('organizations', [
            'name' => 'Organization Name',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212121',
            'created_by' => $this->buyer->id,
            'status' => OrganizationStatus::PENDING,
        ]);
    }

    public function test_unauthenticated_user_cannot_check_organization_status()
    {
        $response = $this->getJson(route('buyer.organizations.check'));

        $response->assertStatus(401);
    }

    public function test_supplier_cannot_check_organization_status()
    {
        $response = $this->actingAs($this->supplier)->getJson(route('buyer.organizations.check'));

        $response->assertStatus(401);
    }

    public function test_buyer_without_organization_cannot_check_status()
    {
        $response = $this->actingAs($this->buyer)->getJson(route('buyer.organizations.check'));

        $response->assertStatus(422);
    }

    public function test_buyer_with_pending_organization_can_check_status()
    {
        $organization = Organization::factory()->create([
            'name' => 'Test Organization',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212121',
            'status' => OrganizationStatus::PENDING,
            'created_by' => $this->buyer->id,
        ]);

        // Add the buyer as a member of the organization
        DB::table('organization_user')->insert([
            'organization_id' => $organization->id,
            'user_id' => $this->buyer->id,
            'role' => OrganizationRole::OWNER,
            'joined_at' => now(),
        ]);

        $response = $this->actingAs($this->buyer)->getJson(route('buyer.organizations.check'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => [
                        'value' => OrganizationStatus::PENDING->value,
                    ],
                ],
            ]);
    }

    public function test_buyer_with_approved_organization_can_check_status()
    {
        $organization = Organization::factory()->create([
            'name' => 'Approved Organization',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212121',
            'status' => OrganizationStatus::APPROVED,
            'created_by' => $this->buyer->id,
        ]);

        // Add the buyer as a member of the organization
        DB::table('organization_user')->insert([
            'organization_id' => $organization->id,
            'user_id' => $this->buyer->id,
            'role' => OrganizationRole::OWNER,
            'joined_at' => now(),
        ]);

        $response = $this->actingAs($this->buyer)->getJson(route('buyer.organizations.check'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => [
                        'value' => OrganizationStatus::APPROVED->value,
                    ],
                ],
            ]);
    }

    public function test_buyer_with_rejected_organization_can_check_status()
    {
        $organization = Organization::factory()->create([
            'name' => 'Rejected Organization',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212121',
            'status' => OrganizationStatus::REJECTED,
            'created_by' => $this->buyer->id,
        ]);

        // Add the buyer as a member of the organization
        DB::table('organization_user')->insert([
            'organization_id' => $organization->id,
            'user_id' => $this->buyer->id,
            'role' => OrganizationRole::OWNER,
            'joined_at' => now(),
        ]);

        $response = $this->actingAs($this->buyer)->getJson(route('buyer.organizations.check'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => [
                        'value' => OrganizationStatus::REJECTED->value,
                    ],
                ],
            ]);
    }

    public function test_unauthenticated_user_cannot_update_organization()
    {
        $organization = Organization::factory()->create([
            'status' => OrganizationStatus::PENDING,
        ]);

        $response = $this->putJson(route('buyer.organizations.update', $organization), [
            'name' => 'Updated Organization Name',
        ]);

        $response->assertStatus(401);
    }

    public function test_supplier_cannot_update_organization()
    {
        $organization = Organization::factory()->create([
            'status' => OrganizationStatus::PENDING,
        ]);

        $response = $this->actingAs($this->supplier)->putJson(route('buyer.organizations.update', $organization), [
            'name' => 'Updated Organization Name',
        ]);

        $response->assertStatus(401);
    }

    public function test_buyer_can_update_own_organization()
    {
        $organization = Organization::factory()->create([
            'name' => 'Original Organization',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212121',
            'status' => OrganizationStatus::APPROVED,
            'created_by' => $this->buyer->id,
        ]);

        // Add the buyer as a member of the organization
        DB::table('organization_user')->insert([
            'organization_id' => $organization->id,
            'user_id' => $this->buyer->id,
            'role' => OrganizationRole::OWNER,
            'joined_at' => now(),
        ]);

        $response = $this->actingAs($this->buyer)->putJson(route('buyer.organizations.update', $organization), [
            'name' => 'Updated Organization Name',
            'tax_number' => '3212121212121211',
            'commercial_register_number' => '1212122',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Updated Organization Name',
                    'tax_number' => '3212121212121211',
                    'commercial_register_number' => '1212122',
                    'status' => [
                        'value' => OrganizationStatus::PENDING->value,
                    ],
                ],
            ]);

        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'name' => 'Updated Organization Name',
            'tax_number' => '3212121212121211',
            'commercial_register_number' => '1212122',
            'status' => OrganizationStatus::PENDING,
        ]);
    }

    public function test_buyer_cannot_update_organization_they_do_not_own()
    {
        $otherBuyer = User::factory()->buyer()->create();
        $organization = Organization::factory()->create([
            'name' => 'Other Organization',
            'status' => OrganizationStatus::PENDING,
            'created_by' => $otherBuyer->id,
        ]);

        $response = $this->actingAs($this->buyer)->putJson(route('buyer.organizations.update', $organization), [
            'name' => 'Hacked Organization Name',
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'name' => 'Other Organization',
        ]);
    }

    public function test_buyer_can_update_organization_with_partial_data()
    {
        $organization = Organization::factory()->create([
            'name' => 'Original Organization',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212121',
            'status' => OrganizationStatus::APPROVED,
            'created_by' => $this->buyer->id,
        ]);

        $response = $this->actingAs($this->buyer)->putJson(route('buyer.organizations.update', $organization), [
            'name' => 'Updated Name Only',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Updated Name Only',
                    'tax_number' => '3212121212121212', // Should remain unchanged
                    'commercial_register_number' => '1212121', // Should remain unchanged
                    'status' => [
                        'value' => OrganizationStatus::PENDING->value,
                    ],
                ],
            ]);
    }

    public function test_organization_status_resets_to_pending_after_update()
    {
        $organization = Organization::factory()->create([
            'name' => 'Approved Organization',
            'status' => OrganizationStatus::APPROVED,
            'created_by' => $this->buyer->id,
        ]);

        $response = $this->actingAs($this->buyer)->putJson(route('buyer.organizations.update', $organization), [
            'name' => 'Updated Approved Organization',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => [
                        'value' => OrganizationStatus::PENDING->value,
                    ],
                ],
            ]);

        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'status' => OrganizationStatus::PENDING,
        ]);
    }

    public function test_buyer_cannot_update_organization_with_invalid_tax_number()
    {
        $organization = Organization::factory()->create([
            'status' => OrganizationStatus::PENDING,
            'created_by' => $this->buyer->id,
        ]);

        $response = $this->actingAs($this->buyer)->putJson(route('buyer.organizations.update', $organization), [
            'tax_number' => '1212121212121212', // Invalid: doesn't start with 3
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('tax_number')
            ->assertJson([
                'errors' => [
                    'tax_number' => [
                        'The tax number field must start with one of the following: 3.',
                    ],
                ],
            ]);
    }

    public function test_buyer_cannot_update_organization_with_invalid_commercial_register_number()
    {
        $organization = Organization::factory()->create([
            'status' => OrganizationStatus::PENDING,
            'created_by' => $this->buyer->id,
        ]);

        $response = $this->actingAs($this->buyer)->putJson(route('buyer.organizations.update', $organization), [
            'commercial_register_number' => '123456', // Invalid: only 6 digits instead of 7
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('commercial_register_number')
            ->assertJson([
                'errors' => [
                    'commercial_register_number' => [
                        'The commercial register number field must be 7 digits.',
                    ],
                ],
            ]);
    }

    public function test_buyer_cannot_update_organization_with_existing_approved_name()
    {
        // Create an approved organization with a specific name
        Organization::factory()->create([
            'name' => 'Existing Approved Organization',
            'status' => OrganizationStatus::APPROVED,
        ]);

        // Create the buyer's organization
        $buyerOrganization = Organization::factory()->create([
            'name' => 'Buyer Organization',
            'status' => OrganizationStatus::PENDING,
            'created_by' => $this->buyer->id,
        ]);

        $response = $this->actingAs($this->buyer)->putJson(route('buyer.organizations.update', $buyerOrganization), [
            'name' => 'Existing Approved Organization',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name')
            ->assertJson([
                'errors' => [
                    'name' => [
                        'The name has already been taken.',
                    ],
                ],
            ]);
    }

    public function test_buyer_can_update_organization_with_existing_pending_name()
    {
        // Create a pending organization with a specific name
        Organization::factory()->create([
            'name' => 'Existing Pending Organization',
            'status' => OrganizationStatus::PENDING,
        ]);

        // Create the buyer's organization
        $buyerOrganization = Organization::factory()->create([
            'name' => 'Buyer Organization',
            'status' => OrganizationStatus::PENDING,
            'created_by' => $this->buyer->id,
        ]);

        $response = $this->actingAs($this->buyer)->putJson(route('buyer.organizations.update', $buyerOrganization), [
            'name' => 'Existing Pending Organization',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Existing Pending Organization',
                ],
            ]);
    }

    public function test_buyer_can_keep_same_name_when_updating_other_fields()
    {
        $organization = Organization::factory()->create([
            'name' => 'Same Organization Name',
            'tax_number' => '3212121212121212',
            'status' => OrganizationStatus::APPROVED,
            'created_by' => $this->buyer->id,
        ]);

        $response = $this->actingAs($this->buyer)->putJson(route('buyer.organizations.update', $organization), [
            'name' => 'Same Organization Name', // Same name
            'tax_number' => '3212121212121211', // Different tax number
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Same Organization Name',
                    'tax_number' => '3212121212121211',
                ],
            ]);
    }

    public function test_update_organization_returns_proper_structure()
    {
        $organization = Organization::factory()->create([
            'name' => 'Test Organization',
            'tax_number' => '3212121212121212',
            'commercial_register_number' => '1212121',
            'status' => OrganizationStatus::PENDING,
            'created_by' => $this->buyer->id,
        ]);

        // Add the buyer as a member of the organization
        DB::table('organization_user')->insert([
            'organization_id' => $organization->id,
            'user_id' => $this->buyer->id,
            'role' => OrganizationRole::OWNER,
            'joined_at' => now(),
        ]);

        $response = $this->actingAs($this->buyer)->putJson(route('buyer.organizations.update', $organization), [
            'name' => 'Updated Organization',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'tax_number',
                    'commercial_register_number',
                    'status' => [
                        'value',
                        'label',
                    ],
                    'owner' => [
                        'id',
                        'name',
                        'phone',
                        'email',
                        'image',
                        'role',
                        'joined_at',
                    ],
                    'members',
                ],
            ]);
    }
}
