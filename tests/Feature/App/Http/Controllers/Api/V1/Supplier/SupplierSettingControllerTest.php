<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1\Supplier;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Address;
use App\Models\Field;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SupplierSettingControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Test supplier registration creates a supplier record using factory
     */
    public function test_supplier_registration_creates_supplier_record(): void
    {
        // Create field and use the working registration format
        $field = Field::factory()->create();
        $data = $this->getValidRegistrationData(UserRole::SUPPLIER->value);
        $data['fields'] = [$field->id];

        $response = $this->postJson(route('auth.supplier.register'), $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id', 'name', 'phone', 'email', 'role', 'is_verified',
                    ],
                    'message',
                ],
            ])
            ->assertJsonPath('data.user.role', UserRole::SUPPLIER->value)
            ->assertJsonPath('data.user.is_verified', false);

        // Assert user was created with correct data
        $user = User::where('phone', $data['phone'])->first();
        $this->assertNotNull($user);
        $this->assertEquals($data['name'], $user->name);
        $this->assertEquals($data['phone'], $user->phone);
        $this->assertEquals($data['business_name'], $user->business_name);
        $this->assertEquals(UserRole::SUPPLIER, $user->role); // Compare with enum directly
        $this->assertEquals(UserStatus::PENDING, $user->status); // Compare with enum directly
        $this->assertCount(1, $user->fields);

        // Assert file uploads were stored
        Storage::disk('public')->assertExists($user->license_attachment);
        Storage::disk('public')->assertExists($user->commercial_register_attachment);

        // Assert supplier record was created
        $supplier = Supplier::where('user_id', $user->id)->first();
        $this->assertNotNull($supplier);
        $this->assertEquals($user->id, $supplier->user_id);
        $this->assertTrue((bool) $supplier->status); // Cast to boolean for assertion
    }

    /**
     * Test updating supplier status through settings endpoint
     */
    public function test_can_update_supplier_status(): void
    {
        // Create a supplier user using factory
        $user = User::factory()->create([
            'role' => UserRole::SUPPLIER,
            'status' => UserStatus::APPROVED,
        ]);

        // Create supplier record
        $supplier = Supplier::create([
            'user_id' => $user->id,
            'status' => true,
        ]);

        // Acting as the supplier user
        $this->actingAs($user, 'sanctum');

        // Test updating status to false
        $response = $this->putJson('/api/v1/suppliers/setting', [
            'status' => false,
        ]);

        // Assert successful update - using the correct SupplierResource structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'image',
                    'rating',
                    'fields',
                    'supplier_status',
                ],
            ]);

        // Assert supplier status was updated (cast to boolean for comparison)
        $supplier->refresh();
        $this->assertFalse((bool) $supplier->status);

        // Verify the response data (cast supplier_status to boolean)
        $responseData = $response->json('data');
        $this->assertEquals($user->id, $responseData['id']);
        $this->assertEquals($user->name, $responseData['name']);
        $this->assertFalse((bool) $responseData['supplier_status']); // Cast to boolean

        // Test updating status back to true
        $response = $this->putJson('/api/v1/suppliers/setting', [
            'status' => true,
        ]);

        $response->assertStatus(200);
        $supplier->refresh();
        $this->assertTrue((bool) $supplier->status);

        // Verify the response shows updated status
        $responseData = $response->json('data');
        $this->assertTrue((bool) $responseData['supplier_status']); // Cast to boolean
    }

    /**
     * Test supplier settings update requires authentication
     */
    public function test_supplier_settings_update_requires_authentication(): void
    {
        $response = $this->putJson('/api/v1/suppliers/setting', [
            'status' => false,
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test supplier settings update validation
     */
    public function test_supplier_settings_update_validation(): void
    {
        // Create a supplier user using factory
        $user = User::factory()->create([
            'role' => UserRole::SUPPLIER,
            'status' => UserStatus::APPROVED,
        ]);

        // Create supplier record
        Supplier::create([
            'user_id' => $user->id,
            'status' => true,
        ]);

        $this->actingAs($user, 'sanctum');

        // Test without status field
        $response = $this->putJson('/api/v1/suppliers/setting', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);

        // Test with invalid status value
        $response = $this->putJson('/api/v1/suppliers/setting', [
            'status' => 'invalid',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);

        // Test with valid boolean string
        $response = $this->putJson('/api/v1/suppliers/setting', [
            'status' => '1',
        ]);
        $response->assertStatus(200);
    }

    /**
     * Test complete supplier flow using the working registration format
     */
    public function test_complete_supplier_flow(): void
    {
        // Step 1: Register supplier using working format
        $field = Field::factory()->create();
        $data = $this->getValidRegistrationData(UserRole::SUPPLIER->value);
        $data['fields'] = [$field->id];
        $data['name'] = 'John Doe';
        $data['business_name'] = 'Doe Supplies';
        $data['email'] = 'john@doesupplies.com';

        $response = $this->postJson(route('auth.supplier.register'), $data);
        $response->assertStatus(201);

        // Step 2: Verify user and supplier were created
        $user = User::where('phone', $data['phone'])->first();
        $this->assertNotNull($user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals(UserRole::SUPPLIER, $user->role); // Compare with enum directly
        $this->assertEquals(UserStatus::PENDING, $user->status); // Compare with enum directly

        $supplier = Supplier::where('user_id', $user->id)->first();
        $this->assertNotNull($supplier);
        $this->assertEquals($user->id, $supplier->user_id);
        $this->assertTrue((bool) $supplier->status);

        // Step 3: Simulate admin approval
        $user->update(['status' => UserStatus::APPROVED]);

        // Step 4: Test settings update as approved supplier
        $this->actingAs($user, 'sanctum');

        $response = $this->putJson('/api/v1/suppliers/setting', [
            'status' => false,
        ]);

        $response->assertStatus(200);
        $supplier->refresh();
        $this->assertFalse((bool) $supplier->status);

        // Step 5: Verify the response contains the correct data
        $response->assertJsonFragment([
            'success' => true,
        ]);

        $responseData = $response->json('data');
        $this->assertEquals($user->id, $responseData['id']);
        $this->assertEquals('John Doe', $responseData['name']);
        $this->assertFalse((bool) $responseData['supplier_status']); // Cast to boolean
    }

    /**
     * Test that only supplier users can update supplier settings
     */
    public function test_only_supplier_users_can_update_settings(): void
    {
        // Create a regular user (not supplier) using factory
        $user = User::factory()->create([
            'role' => UserRole::BUYER,
            'status' => UserStatus::APPROVED,
        ]);

        $this->actingAs($user, 'sanctum');

        $response = $this->putJson('/api/v1/suppliers/setting', [
            'status' => false,
        ]);

        // This should fail because there's no supplier record for this user
        $response->assertStatus(500); // Or whatever error the service throws
    }

    /**
     * Test supplier settings update with factories only
     */
    public function test_supplier_settings_update_with_factories(): void
    {
        // Create field using factory
        $field = Field::factory()->create();

        // Create supplier user using factory
        $user = User::factory()->create([
            'role' => UserRole::SUPPLIER,
            'status' => UserStatus::APPROVED,
        ]);

        // Attach field to user
        $user->fields()->attach($field->id);

        // Create address using factory
        $address = Address::factory()->create([
            'user_id' => $user->id,
            'is_default' => true,
        ]);

        // Create supplier record
        $supplier = Supplier::create([
            'user_id' => $user->id,
            'status' => true,
        ]);

        $this->actingAs($user, 'sanctum');

        // Test updating status
        $response = $this->putJson('/api/v1/suppliers/setting', [
            'status' => false,
        ]);

        $response->assertStatus(200);

        // Verify supplier status was updated
        $supplier->refresh();
        $this->assertFalse((bool) $supplier->status);

        // Verify response structure (cast supplier_status to boolean)
        $responseData = $response->json('data');
        $this->assertEquals($user->id, $responseData['id']);
        $this->assertEquals($user->name, $responseData['name']);
        $this->assertFalse((bool) $responseData['supplier_status']); // Cast to boolean
    }

    /**
     * Test that buyer can see supplier status changes through supplier details API
     */
    public function test_buyer_can_see_supplier_status_changes(): void
    {
        // Create field using factory
        $field = Field::factory()->create();

        // Create supplier user using factory
        $supplier = User::factory()->create([
            'role' => UserRole::SUPPLIER,
            'status' => UserStatus::APPROVED,
        ]);

        // Attach field to supplier
        $supplier->fields()->attach($field->id);

        // Create supplier record with initial status true
        $supplierRecord = Supplier::create([
            'user_id' => $supplier->id,
            'status' => true,
        ]);

        // Create buyer user using factory
        $buyer = User::factory()->create([
            'role' => UserRole::BUYER,
            'status' => UserStatus::APPROVED,
        ]);

        // Step 1: Buyer checks supplier details (initial status should be true)
        $this->actingAs($buyer, 'sanctum');

        $response = $this->getJson("/api/v1/suppliers/{$supplier->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'image',
                    'rating',
                    'fields',
                    'categories',
                    'supplier_status',
                ],
            ]);

        // Verify initial status is true
        $responseData = $response->json('data');
        $this->assertEquals($supplier->id, $responseData['id']);
        $this->assertEquals($supplier->name, $responseData['name']);
        $this->assertTrue((bool) $responseData['supplier_status']); // Initial status is true

        // Step 2: Supplier changes their status to false
        $this->actingAs($supplier, 'sanctum');

        $updateResponse = $this->putJson('/api/v1/suppliers/setting', [
            'status' => false,
        ]);

        $updateResponse->assertStatus(200);

        // Verify the supplier record was updated
        $supplierRecord->refresh();
        $this->assertFalse((bool) $supplierRecord->status);

        // Step 3: Buyer checks supplier details again (status should now be false)
        $this->actingAs($buyer, 'sanctum');

        $response = $this->getJson("/api/v1/suppliers/{$supplier->id}");

        $response->assertStatus(200);

        $responseData = $response->json('data');
        $this->assertEquals($supplier->id, $responseData['id']);
        $this->assertEquals($supplier->name, $responseData['name']);
        $this->assertFalse((bool) $responseData['supplier_status']); // Status should now be false

        // Step 4: Supplier changes their status back to true
        $this->actingAs($supplier, 'sanctum');

        $updateResponse = $this->putJson('/api/v1/suppliers/setting', [
            'status' => true,
        ]);

        $updateResponse->assertStatus(200);

        // Step 5: Buyer checks supplier details one more time (status should be true again)
        $this->actingAs($buyer, 'sanctum');

        $response = $this->getJson("/api/v1/suppliers/{$supplier->id}");

        $response->assertStatus(200);

        $responseData = $response->json('data');
        $this->assertEquals($supplier->id, $responseData['id']);
        $this->assertEquals($supplier->name, $responseData['name']);
        $this->assertTrue((bool) $responseData['supplier_status']); // Status should be true again
    }

    /**
     * Helper method to get valid registration data (copied from AuthControllerTest)
     */
    protected function getValidRegistrationData(string $role = UserRole::BUYER->value): array
    {
        $data = [
            'name' => $this->faker->name,
            'phone' => '966'.$this->faker->numerify('########'),
            'country_code' => '966',
            'business_name' => $this->faker->company,
            'email' => $this->faker->safeEmail,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => $role,
            'address' => [ // Keep this as array for the request
                'name' => $this->faker->word,
                'street' => $this->faker->streetAddress,
                'city' => $this->faker->city,
                'phone' => '966'.$this->faker->numerify('########'),
                'latitude' => $this->faker->latitude,
                'longitude' => $this->faker->longitude,
            ],
        ];

        if ($role === UserRole::SUPPLIER->value) {
            $data['license_attachment'] = UploadedFile::fake()->create('license.pdf', 1000);
            $data['commercial_register_attachment'] = UploadedFile::fake()->create('commercial.pdf', 1000);
            $data['fields'] = [Field::factory()->create()->id];
        }

        return $data;
    }
}
