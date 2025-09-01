<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyerProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function createBuyer(array $attributes = []): User
    {
        static $phoneCounter = 0;
        $phoneCounter++;

        $buyer = User::factory()->create(array_merge([
            'role' => UserRole::BUYER,
            'status' => UserStatus::APPROVED,
            'is_verified' => true,
            'phone' => '+96650000000'.$phoneCounter, // Ensure unique phone numbers
        ], $attributes));

        return $buyer;
    }

    /**
     * Test buyer can update phone with OTP verification flow.
     */
    public function test_buyer_can_update_phone_with_otp_verification_flow()
    {
        $buyer = $this->createBuyer();
        $token = $buyer->createToken('test')->plainTextToken;
        $newPhone = '+966500000002';

        // Step 1: Request OTP for the new phone
        $response = $this->withToken($token)->postJson(route('auth.request-otp-auth'), [
            'phone' => $newPhone,
        ]);
        $response->assertStatus(200);

        // Step 2: Update phone with OTP verification
        $response = $this->withToken($token)->putJson(route('buyers.phone.update'), [
            'phone' => $newPhone,
            'otp' => '123456', // Fixed OTP for testing
        ]);
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('users', [
            'id' => $buyer->id,
            'phone' => $newPhone,
        ]);
    }

    /**
     * Test buyer cannot update phone without OTP.
     */
    public function test_buyer_cannot_update_phone_without_otp()
    {
        $buyer = $this->createBuyer();
        $token = $buyer->createToken('test')->plainTextToken;
        $newPhone = '+966500000002';

        $response = $this->withToken($token)->putJson(route('buyers.phone.update'), [
            'phone' => $newPhone,
            // Missing OTP
        ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['otp']);
    }

    /**
     * Test buyer cannot update phone with invalid OTP.
     */
    public function test_buyer_cannot_update_phone_with_invalid_otp()
    {
        $buyer = $this->createBuyer();
        $token = $buyer->createToken('test')->plainTextToken;
        $newPhone = '+966500000002';

        $response = $this->withToken($token)->putJson(route('buyers.phone.update'), [
            'phone' => $newPhone,
            'otp' => '000000', // Invalid OTP
        ]);
        
        $response->assertStatus(422);
        $response->assertJson([
            'message' => __('messages.invalid_otp')
        ]);
    }

    /**
     * Test buyer cannot update phone to an already verified phone number.
     */
    public function test_buyer_cannot_update_phone_to_existing_verified_phone()
    {
        $buyer = $this->createBuyer();
        $existingUser = $this->createBuyer(['phone' => '+966500000002']);
        $token = $buyer->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->putJson(route('buyers.phone.update'), [
            'phone' => '+966500000002', // Already exists
            'otp' => '123456',
        ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['phone']);
    }

    /**
     * Test buyer can request and verify OTP for a phone number.
     */
    public function test_buyer_can_request_and_verify_otp()
    {
        $buyer = $this->createBuyer();
        $token = $buyer->createToken('test')->plainTextToken;
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
     * Test buyer can update profile without changing phone (no OTP required).
     */
    public function test_buyer_can_update_profile_without_phone_change()
    {
        $buyer = $this->createBuyer(['name' => 'Old Name']);
        $token = $buyer->createToken('test')->plainTextToken;

        $response = $this->updateBuyerProfile([
            'name' => 'New Name',
        ], $token);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $buyer->id,
            'name' => 'New Name',
        ]);
    }

    /**
     * Test buyer can update profile with name and email (should succeed).
     */
    public function test_buyer_can_update_profile_with_name_and_email()
    {
        $buyer = $this->createBuyer([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'phone' => '+966500000001',
        ]);
        $token = $buyer->createToken('test')->plainTextToken;

        $response = $this->updateBuyerProfile([
            'name' => 'New Name',
            'email' => 'new@example.com',
        ], $token);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $buyer->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
            'phone' => '+966500000001', // Should remain unchanged
        ]);
    }


    /**
     * Test buyer can update image when they don't have an old photo.
     */
    public function test_buyer_can_update_image_without_old_photo()
    {
        $buyer = $this->createBuyer(['image' => null]);
        $token = $buyer->createToken('test')->plainTextToken;

        $response = $this->changeBuyerImage($token);
        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $buyer->id,
        ]);

        // Check that the image field is not null after update
        $buyer->refresh();
        $this->assertNotNull($buyer->image);
        $this->assertStringContainsString('users/', $buyer->image);
    }

    /**
     * Test buyer can update image when they have an old photo.
     */
    public function test_buyer_can_update_image_with_old_photo()
    {
        $buyer = $this->createBuyer(['image' => 'users/old-image.jpg']);
        $token = $buyer->createToken('test')->plainTextToken;

        $response = $this->changeBuyerImage($token);
        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $buyer->id,
        ]);

        // Check that the image field has been updated
        $buyer->refresh();
        $this->assertNotNull($buyer->image);
        $this->assertStringContainsString('users/', $buyer->image);
        $this->assertNotEquals('users/old-image.jpg', $buyer->image);
    }

    /**
     * Test buyer can delete their account.
     */
    public function test_buyer_can_delete_account()
    {
        $buyer = $this->createBuyer();
        $token = $buyer->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->deleteJson(route('buyers.profile.delete'));
        $response->assertStatus(200);

        $this->assertDatabaseMissing('users', [
            'id' => $buyer->id,
        ]);
    }

    /**
     * Test buyer cannot update profile with duplicate email.
     */
    public function test_buyer_cannot_update_profile_with_duplicate_email()
    {
        $existingBuyer = $this->createBuyer(['email' => 'existing@example.com']);
        $buyer = $this->createBuyer(['email' => 'buyer@example.com']);
        $token = $buyer->createToken('test')->plainTextToken;

        $response = $this->updateBuyerProfile([
            'email' => $existingBuyer->email,
        ], $token);
        $response->assertStatus(422);
    }

    /**
     * Test buyer can access buyer profile endpoints.
     */
    public function test_buyer_can_access_buyer_profile_endpoints()
    {
        $buyer = $this->createBuyer();
        $token = $buyer->createToken('test')->plainTextToken;

        // Try to update buyer profile (should work)
        $response = $this->withToken($token)->putJson(route('buyers.profile.update'), [
            'name' => 'New Name',
        ]);

        // The middleware should allow buyers to access buyer endpoints
        $response->assertStatus(200);
    }

    /**
     * Test supplier cannot access buyer profile endpoints.
     */
    public function test_supplier_cannot_access_buyer_profile_endpoints()
    {
        $supplier = User::factory()->create([
            'role' => UserRole::SUPPLIER,
            'status' => UserStatus::APPROVED,
        ]);
        $token = $supplier->createToken('test')->plainTextToken;

        // Try to update buyer profile
        $response = $this->withToken($token)->putJson(route('buyers.profile.update'), [
            'name' => 'New Name',
        ]);

        // The middleware should block suppliers from accessing buyer endpoints
        $response->assertStatus(401);

        // Try to change buyer image
        $response = $this->withToken($token)->postJson(route('buyers.image.change'), [
            'image' => \Illuminate\Http\UploadedFile::fake()->image('test-image.jpg', 100, 100),
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test unauthenticated user cannot access buyer profile endpoints.
     */
    public function test_unauthenticated_user_cannot_access_buyer_profile_endpoints()
    {
        // Try to update buyer profile
        $response = $this->putJson(route('buyers.profile.update'), [
            'name' => 'New Name',
        ]);
        $response->assertStatus(401);

        // Try to change buyer image
        $response = $this->postJson(route('buyers.image.change'), [
            'image' => \Illuminate\Http\UploadedFile::fake()->image('test-image.jpg', 100, 100),
        ]);
        $response->assertStatus(401);
    }

    /**
     * Test buyer cannot upload invalid image format.
     */
    public function test_buyer_cannot_upload_invalid_image_format()
    {
        $buyer = $this->createBuyer();
        $token = $buyer->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->postJson(route('buyers.image.change'), [
            'image' => \Illuminate\Http\UploadedFile::fake()->create('test.txt', 100),
        ]);
        $response->assertStatus(422);
    }

    /**
     * Test buyer cannot upload image larger than 2MB.
     */
    public function test_buyer_cannot_upload_large_image()
    {
        $buyer = $this->createBuyer();
        $token = $buyer->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->postJson(route('buyers.image.change'), [
            'image' => \Illuminate\Http\UploadedFile::fake()->image('test-image.jpg', 100, 100)->size(3000),
        ]);
        $response->assertStatus(422);
    }

    /**
     * Test buyer can update multiple profile fields at once.
     */
    public function test_buyer_can_update_multiple_profile_fields()
    {
        $buyer = $this->createBuyer([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);
        $token = $buyer->createToken('test')->plainTextToken;

        $response = $this->updateBuyerProfile([
            'name' => 'New Name',
            'email' => 'new@example.com',
        ], $token);
        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $buyer->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
        ]);
    }

    /**
     * Test buyer profile update response structure.
     */
    public function test_buyer_profile_update_response_structure()
    {
        $buyer = $this->createBuyer();
        $token = $buyer->createToken('test')->plainTextToken;

        $response = $this->updateBuyerProfile([
            'name' => 'New Name',
        ], $token);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'image',
                ],
            ]);
    }

    /**
     * Test buyer image change response structure.
     */
    public function test_buyer_image_change_response_structure()
    {
        $buyer = $this->createBuyer();
        $token = $buyer->createToken('test')->plainTextToken;

        $response = $this->changeBuyerImage($token);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'image',
                ],
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

    protected function updateBuyerProfile(array $data, string $token)
    {
        return $this->withToken($token)->putJson(route('buyers.profile.update'), $data);
    }

    protected function changeBuyerImage(string $token)
    {
        return $this->withToken($token)
            ->postJson(route('buyers.image.change'), [
                'image' => \Illuminate\Http\UploadedFile::fake()->image('test-image.jpg', 100, 100),
            ]);
    }
}
