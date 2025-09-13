<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Address;
use App\Models\Field;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
        // Create field and supplier user using factories
        $field = Field::factory()->create();
        $user = User::factory()->create([
            'role' => UserRole::SUPPLIER,
            'status' => UserStatus::PENDING,
            'is_verified' => false,
        ]);

        // Attach field to user
        $user->fields()->attach($field->id);

        // Create supplier record
        $supplier = Supplier::create([
            'user_id' => $user->id,
            'is_open' => true,
        ]);

        // Assert user was created with correct data
        $this->assertEquals(UserRole::SUPPLIER, $user->role);
        $this->assertEquals(UserStatus::PENDING, $user->status);
        $this->assertFalse($user->is_verified);
        $this->assertCount(1, $user->fields);

        // Assert supplier record was created
        $this->assertNotNull($supplier);
        $this->assertEquals($user->id, $supplier->user_id);
        $this->assertTrue((bool) $supplier->is_open);
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
            'is_open' => true,
        ]);

        // Acting as the supplier user
        $this->actingAs($user, 'sanctum');

        // Test updating status to false
        $response = $this->putJson(route('suppliers.setting.update'), [
            'is_open' => false,
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
                    'fields',
                    'supplier_status',
                ],
            ]);

        // Assert supplier status was updated (cast to boolean for comparison)
        $supplier->refresh();
        $this->assertFalse((bool) $supplier->is_open);

        // Verify the response data (cast supplier_status to boolean)
        $responseData = $response->json('data');
        $this->assertEquals($user->id, $responseData['id']);
        $this->assertEquals($user->name, $responseData['name']);
        $this->assertFalse((bool) $responseData['supplier_status']); // Cast to boolean

        // Test updating status back to true
        $response = $this->putJson(route('suppliers.setting.update'), [
            'is_open' => true,
        ]);

        $response->assertStatus(200);
        $supplier->refresh();
        $this->assertTrue((bool) $supplier->is_open);

        // Verify the response shows updated status
        $responseData = $response->json('data');
        $this->assertTrue((bool) $responseData['supplier_status']); // Cast to boolean
    }

    /**
     * Test supplier settings update requires authentication
     */
    public function test_supplier_settings_update_requires_authentication(): void
    {
        $response = $this->putJson(route('suppliers.setting.update'), [
            'is_open' => false,
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
            'is_open' => true,
        ]);

        $this->actingAs($user, 'sanctum');

        // Test without status field
        $response = $this->putJson(route('suppliers.setting.update'), []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['is_open']);

        // Test with invalid status value
        $response = $this->putJson(route('suppliers.setting.update'), [
            'is_open' => 'invalid',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['is_open']);

        // Test with valid boolean string
        $response = $this->putJson(route('suppliers.setting.update'), [
            'is_open' => '1',
        ]);
        $response->assertStatus(200);
    }

    /**
     * Test complete supplier flow using factories
     */
    public function test_complete_supplier_flow(): void
    {
        // Step 1: Create supplier user and related data using factories
        $field = Field::factory()->create();
        $user = User::factory()->create([
            'role' => UserRole::SUPPLIER,
            'status' => UserStatus::PENDING,
            'name' => 'John Doe',
            'business_name' => 'Doe Supplies',
        ]);

        // Attach field to user
        $user->fields()->attach($field->id);

        // Create address using factory
        Address::factory()->create([
            'user_id' => $user->id,
            'is_default' => true,
        ]);

        // Create supplier record
        $supplier = Supplier::create([
            'user_id' => $user->id,
            'is_open' => true,
        ]);

        // Step 2: Verify user and supplier were created
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals(UserRole::SUPPLIER, $user->role);
        $this->assertEquals(UserStatus::PENDING, $user->status);
        $this->assertEquals($user->id, $supplier->user_id);
        $this->assertTrue((bool) $supplier->is_open);

        // Step 3: Simulate admin approval
        $user->update(['status' => UserStatus::APPROVED]);

        // Step 4: Test settings update as approved supplier
        $this->actingAs($user, 'sanctum');

        $response = $this->putJson(route('suppliers.setting.update'), [
            'is_open' => false,
        ]);

        $response->assertStatus(200);
        $supplier->refresh();
        $this->assertFalse((bool) $supplier->is_open);

        // Step 5: Verify the response contains the correct data
        $response->assertJsonFragment([
            'success' => true,
        ]);

        $responseData = $response->json('data');
        $this->assertEquals($user->id, $responseData['id']);
        $this->assertEquals('John Doe', $responseData['name']);
        $this->assertFalse((bool) $responseData['supplier_status']);
    }

    /**
     * Test that only supplier users can update supplier settings
     */
    public function test_only_supplier_users_can_update_settings(): void
    {
        // Create a buyer user using factory
        $buyer = User::factory()->create([
            'role' => UserRole::BUYER,
            'status' => UserStatus::APPROVED,
        ]);

        // Create address for buyer
        Address::factory()->create([
            'user_id' => $buyer->id,
            'is_default' => true,
        ]);

        $this->actingAs($buyer, 'sanctum');

        $response = $this->putJson(route('suppliers.setting.update'), [
            'is_open' => false,
        ]);

        // This should fail because buyers cannot access supplier-only routes
        $response->assertStatus(401); // Role middleware returns 401
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
            'is_open' => true,
        ]);

        $this->actingAs($user, 'sanctum');

        // Test updating status
        $response = $this->putJson(route('suppliers.setting.update'), [
            'is_open' => false,
        ]);

        $response->assertStatus(200);

        // Verify supplier status was updated
        $supplier->refresh();
        $this->assertFalse((bool) $supplier->is_open);

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

        // Create address for supplier
        Address::factory()->create([
            'user_id' => $supplier->id,
            'is_default' => true,
        ]);

        // Create supplier record with initial status true
        $supplierRecord = Supplier::create([
            'user_id' => $supplier->id,
            'is_open' => true,
        ]);

        // Create buyer user using factory
        $buyer = User::factory()->create([
            'role' => UserRole::BUYER,
            'status' => UserStatus::APPROVED,
        ]);

        // Create address for buyer
        Address::factory()->create([
            'user_id' => $buyer->id,
            'is_default' => true,
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

        $updateResponse = $this->putJson(route('suppliers.setting.update'), [
            'is_open' => false,
        ]);

        $updateResponse->assertStatus(200);

        // Verify the supplier record was updated
        $supplierRecord->refresh();
        $this->assertFalse((bool) $supplierRecord->is_open);

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

        $updateResponse = $this->putJson(route('suppliers.setting.update'), [
            'is_open' => true,
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
     * Test that supplier setting not exists
     */
    public function test_supplier_setting_not_exists(): void
    {
        // Create a buyer user without a supplier record
        $buyer = User::factory()->create([
            'role' => UserRole::BUYER,
            'status' => UserStatus::APPROVED,
        ]);

        // Create address for buyer
        Address::factory()->create([
            'user_id' => $buyer->id,
            'is_default' => true,
        ]);

        $this->actingAs($buyer, 'sanctum');

        // Try to update supplier settings when no supplier record exists
        $response = $this->putJson(route('suppliers.setting.update'), [
            'is_open' => false,
        ]);

        // This should fail because buyers cannot access supplier-only routes
        $response->assertStatus(401); // Role middleware returns 401
    }

    /**
     * Test updating minimum order amount successfully
     */
    public function test_supplier_can_update_min_order_amount(): void
    {
        // Create a supplier user using factory
        $user = User::factory()->create([
            'role' => UserRole::SUPPLIER,
            'status' => UserStatus::APPROVED,
        ]);

        $this->actingAs($user, 'sanctum');

        $minOrderAmount = 150.50;

        // Test updating minimum order amount
        $response = $this->putJson(route('suppliers.min-order-amount.update'), [
            'min_order_amount' => $minOrderAmount,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'key',
                    'key_label',
                    'value',
                ],
            ])
            ->assertJson([
                'success' => true,
                'message' => __('messages.suppliers.min_order_amount_updated'),
            ]);

        // Verify the setting was created/updated
        $responseData = $response->json('data');
        $this->assertEquals($user->id, $responseData['user_id']);
        $this->assertEquals('order.min_order_amount', $responseData['key']);
        $this->assertEquals($minOrderAmount, (float) $responseData['value']);

        // Verify in database
        $this->assertDatabaseHas('settings', [
            'user_id' => $user->id,
            'key' => 'order.min_order_amount',
            'value' => $minOrderAmount,
        ]);
    }

    /**
     * Test updating minimum order amount with zero value
     */
    public function test_supplier_can_update_min_order_amount_to_zero(): void
    {
        // Create a supplier user using factory
        $user = User::factory()->create([
            'role' => UserRole::SUPPLIER,
            'status' => UserStatus::APPROVED,
        ]);

        $this->actingAs($user, 'sanctum');

        // Test updating minimum order amount to 0
        $response = $this->putJson(route('suppliers.min-order-amount.update'), [
            'min_order_amount' => 0,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $responseData = $response->json('data');
        $this->assertEquals(0, (float) $responseData['value']);
    }

    /**
     * Test minimum order amount validation
     */
    public function test_min_order_amount_update_validation(): void
    {
        // Create a supplier user using factory
        $user = User::factory()->create([
            'role' => UserRole::SUPPLIER,
            'status' => UserStatus::APPROVED,
        ]);

        $this->actingAs($user, 'sanctum');

        // Test without min_order_amount field
        $response = $this->putJson(route('suppliers.min-order-amount.update'), []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['min_order_amount']);

        // Test with negative value
        $response = $this->putJson(route('suppliers.min-order-amount.update'), [
            'min_order_amount' => -10,
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['min_order_amount']);

        // Test with non-numeric value
        $response = $this->putJson(route('suppliers.min-order-amount.update'), [
            'min_order_amount' => 'invalid',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['min_order_amount']);
    }

    /**
     * Test updating minimum order amount multiple times
     */
    public function test_supplier_can_update_min_order_amount_multiple_times(): void
    {
        // Create a supplier user using factory
        $user = User::factory()->create([
            'role' => UserRole::SUPPLIER,
            'status' => UserStatus::APPROVED,
        ]);

        $this->actingAs($user, 'sanctum');

        // First update
        $response = $this->putJson(route('suppliers.min-order-amount.update'), [
            'min_order_amount' => 100.00,
        ]);
        $response->assertStatus(200);

        // Second update (should update existing record)
        $response = $this->putJson(route('suppliers.min-order-amount.update'), [
            'min_order_amount' => 200.00,
        ]);
        $response->assertStatus(200);

        $responseData = $response->json('data');
        $this->assertEquals(200.00, (float) $responseData['value']);

        // Verify only one record exists in database
        $settingsCount = Setting::where('user_id', $user->id)
            ->where('key', 'order.min_order_amount')
            ->count();
        $this->assertEquals(1, $settingsCount);
    }

    /**
     * Test getting supplier settings successfully
     */
    public function test_supplier_can_get_settings(): void
    {
        // Create a supplier user using factory
        $user = User::factory()->create([
            'role' => UserRole::SUPPLIER,
            'status' => UserStatus::APPROVED,
        ]);

        // Create some settings for the supplier
        Setting::create([
            'user_id' => $user->id,
            'key' => 'order.min_order_amount',
            'value' => 150.00,
        ]);

        Setting::create([
            'user_id' => $user->id,
            'key' => 'some.other.setting',
            'value' => 'test_value',
        ]);

        $this->actingAs($user, 'sanctum');

        // Test getting supplier settings
        $response = $this->getJson(route('suppliers.settings.get'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'key',
                        'key_label',
                        'value',
                    ],
                ],
            ])
            ->assertJson([
                'success' => true,
                'message' => __('messages.suppliers.settings_fetched'),
            ]);

        $responseData = $response->json('data');
        $this->assertCount(2, $responseData);

        // Verify the settings data
        $minOrderSetting = collect($responseData)->firstWhere('key', 'order.min_order_amount');
        $this->assertNotNull($minOrderSetting);
        $this->assertEquals($user->id, $minOrderSetting['user_id']);
        $this->assertEquals(150.00, (float) $minOrderSetting['value']);
    }

    /**
     * Test getting supplier settings when no settings exist
     */
    public function test_supplier_can_get_empty_settings(): void
    {
        // Create a supplier user using factory
        $user = User::factory()->create([
            'role' => UserRole::SUPPLIER,
            'status' => UserStatus::APPROVED,
        ]);

        $this->actingAs($user, 'sanctum');

        // Test getting supplier settings when none exist
        $response = $this->getJson(route('suppliers.settings.get'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [],
            ]);
    }

    /**
     * Test that getting supplier settings requires authentication
     */
    public function test_get_supplier_settings_requires_authentication(): void
    {
        $response = $this->getJson(route('suppliers.settings.get'));
        $response->assertStatus(401);
    }

    /**
     * Test that updating minimum order amount requires authentication
     */
    public function test_update_min_order_amount_requires_authentication(): void
    {
        $response = $this->putJson(route('suppliers.min-order-amount.update'), [
            'min_order_amount' => 100.00,
        ]);
        $response->assertStatus(401);
    }

    /**
     * Test complete flow: update min order amount and retrieve settings
     */
    public function test_complete_min_order_amount_flow(): void
    {
        // Create a supplier user using factory
        $user = User::factory()->create([
            'role' => UserRole::SUPPLIER,
            'status' => UserStatus::APPROVED,
        ]);

        $this->actingAs($user, 'sanctum');

        // Step 1: Update minimum order amount
        $minOrderAmount = 250.75;
        $updateResponse = $this->putJson(route('suppliers.min-order-amount.update'), [
            'min_order_amount' => $minOrderAmount,
        ]);

        $updateResponse->assertStatus(200);

        // Step 2: Get settings and verify the update
        $getResponse = $this->getJson(route('suppliers.settings.get'));

        $getResponse->assertStatus(200);
        $responseData = $getResponse->json('data');
        $this->assertCount(1, $responseData);

        $setting = $responseData[0];
        $this->assertEquals($user->id, $setting['user_id']);
        $this->assertEquals('order.min_order_amount', $setting['key']);
        $this->assertEquals($minOrderAmount, (float) $setting['value']);
        $this->assertNotNull($setting['key_label']);
    }
}
