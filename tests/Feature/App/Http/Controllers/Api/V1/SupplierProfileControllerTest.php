<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Field;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function createSupplier(array $attributes = []): User
    {
        $supplier = User::factory()->create(array_merge([
            'role' => UserRole::SUPPLIER,
            'status' => UserStatus::APPROVED,
            'is_verified' => true,
            'phone' => '+966500000001', // KSA phone
        ], $attributes));
        $supplier->fields()->attach(Field::factory()->create());

        return $supplier;
    }

    /**
     * Test supplier can update phone with OTP verification flow.
     */
    public function test_supplier_can_update_phone_with_otp_verification_flow()
    {
        $supplier = $this->createSupplier();
        $token = $supplier->createToken('test')->plainTextToken;
        $newPhone = '+966500000002';

        // Step 1: Request OTP for the new phone
        $response = $this->withToken($token)->postJson(route('auth.request-otp-auth'), [
            'phone' => $newPhone,
        ]);
        $response->assertStatus(200);

        // Step 2: Update phone with OTP verification
        $response = $this->withToken($token)->putJson(route('suppliers.phone.update'), [
            'phone' => $newPhone,
            'otp' => '123456', // Fixed OTP for testing
        ]);
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('users', [
            'id' => $supplier->id,
            'phone' => $newPhone,
        ]);
    }

    /**
     * Test supplier cannot update phone without OTP.
     */
    public function test_supplier_cannot_update_phone_without_otp()
    {
        $supplier = $this->createSupplier();
        $token = $supplier->createToken('test')->plainTextToken;
        $newPhone = '+966500000002';

        $response = $this->withToken($token)->putJson(route('suppliers.phone.update'), [
            'phone' => $newPhone,
            // Missing OTP
        ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['otp']);
    }

    /**
     * Test supplier cannot update phone with invalid OTP.
     */
    public function test_supplier_cannot_update_phone_with_invalid_otp()
    {
        $supplier = $this->createSupplier();
        $token = $supplier->createToken('test')->plainTextToken;
        $newPhone = '+966500000002';

        $response = $this->withToken($token)->putJson(route('suppliers.phone.update'), [
            'phone' => $newPhone,
            'otp' => '000000', // Invalid OTP
        ]);
        
        $response->assertStatus(422);
        $response->assertJson([
            'message' => __('messages.invalid_otp')
        ]);
    }

    /**
     * Test supplier cannot update phone to an already verified phone number.
     */
    public function test_supplier_cannot_update_phone_to_existing_verified_phone()
    {
        $supplier = $this->createSupplier();
        $existingUser = $this->createSupplier(['phone' => '+966500000002']);
        $token = $supplier->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->putJson(route('suppliers.phone.update'), [
            'phone' => '+966500000002', // Already exists
            'otp' => '123456',
        ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['phone']);
    }

    /**
     * Test supplier can request and verify OTP for a phone number.
     */
    public function test_supplier_can_request_and_verify_otp()
    {
        $supplier = $this->createSupplier();
        $token = $supplier->createToken('test')->plainTextToken;
        $newPhone = '+966500000003';

        // Request OTP
        $response = $this->withToken($token)->postJson(route('auth.request-otp-auth'), [
            'phone' => $newPhone,
        ]);
        $response->assertStatus(200);

        // Verify OTP
        $response = $this->withToken($token)->postJson(route('auth.verify-otp-auth'), [
            'phone' => $newPhone,
            'otp' => '123456',
        ]);
        $response->assertStatus(200);
    }

    /**
     * Test supplier can update profile without changing phone (no OTP required).
     */
    public function test_supplier_can_update_profile_without_phone_change()
    {
        $supplier = $this->createSupplier(['name' => 'Old Name']);
        $token = $supplier->createToken('test')->plainTextToken;

        $response = $this->updateSupplierProfile([
            'name' => 'New Name',
        ], $token);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $supplier->id,
            'name' => 'New Name',
        ]);
    }

    /**
     * Test supplier can update profile with business name and email (should succeed).
     */
    public function test_supplier_can_update_profile_with_business_name_and_email()
    {
        $supplier = $this->createSupplier([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'phone' => '+966500000001',
        ]);
        $token = $supplier->createToken('test')->plainTextToken;

        $response = $this->updateSupplierProfile([
            'name' => 'New Name',
            'business_name' => 'New Business Name',
            'email' => $supplier->email,
        ], $token);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $supplier->id,
            'name' => 'New Name',
            'business_name' => 'New Business Name',
            'email' => 'old@example.com',
        ]);
    }

    /**
     * Test supplier can update image when they don't have an old photo.
     */
    public function test_supplier_can_update_image_without_old_photo()
    {
        $supplier = $this->createSupplier(['image' => null]);
        $token = $supplier->createToken('test')->plainTextToken;

        $response = $this->changeSupplierImage($token);
        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $supplier->id,
        ]);

        // Check that the image field is not null after update
        $supplier->refresh();
        $this->assertNotNull($supplier->image);
        $this->assertStringContainsString('users/', $supplier->image);
    }

    /**
     * Test supplier can update image when they have an old photo.
     */
    public function test_supplier_can_update_image_with_old_photo()
    {
        $supplier = $this->createSupplier(['image' => 'users/old-image.jpg']);
        $token = $supplier->createToken('test')->plainTextToken;

        $response = $this->changeSupplierImage($token);
        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $supplier->id,
        ]);

        // Check that the image field has been updated
        $supplier->refresh();
        $this->assertNotNull($supplier->image);
        $this->assertStringContainsString('users/', $supplier->image);
        $this->assertNotEquals('users/old-image.jpg', $supplier->image);
    }

    /**
     * Test supplier can delete their account.
     */
    public function test_supplier_can_delete_account()
    {
        $supplier = $this->createSupplier();
        $token = $supplier->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->deleteJson(route('suppliers.profile.delete'));
        $response->assertStatus(200);

        $this->assertDatabaseMissing('users', [
            'id' => $supplier->id,
        ]);
    }

    protected function requestOtpAuth(string $phone, string $token)
    {
        return $this->withToken($token)->postJson(route('auth.request-otp-auth'), [
            'phone' => $phone,
        ]);
    }

    protected function verifyOtpAuth(string $phone, string $token, string $otp = '123456')
    {
        return $this->withToken($token)->postJson(route('auth.verify-otp-auth'), [
            'phone' => $phone,
            'otp' => $otp,
        ]);
    }

    protected function updateSupplierProfile(array $data, string $token)
    {
        return $this->withToken($token)->putJson(route('suppliers.profile.update'), $data);
    }

    protected function changeSupplierImage(string $token)
    {
        return $this->withToken($token)
            ->postJson(route('suppliers.image.change'), [
                'image' => \Illuminate\Http\UploadedFile::fake()->image('test-image.jpg', 100, 100),
            ]);
    }
}
