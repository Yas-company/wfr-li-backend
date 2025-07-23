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

    /**
     * Test supplier can update phone with OTP verification flow (all steps in one function).
     */
    public function test_supplier_can_update_phone_with_otp_verification_flow()
    {
        $supplier = User::factory()->create([
            'role' => UserRole::SUPPLIER,
            'status' => UserStatus::APPROVED,
            'is_verified' => true,
            'phone' => '+966500000001',
        ]);
        $supplier->fields()->attach(Field::factory()->create());
        $token = $supplier->createToken('test')->plainTextToken;
        $newPhone = '+966500000002';

        // Step 1: Request OTP
        $response = $this->withToken($token)->postJson(route('auth.request-otp-auth'), [
            'phone' => $newPhone,
        ]);
        $response->assertStatus(200);

        // Step 2: Verify OTP (fixed OTP is '123456')
        $response = $this->withToken($token)->postJson(route('auth.verify-otp-auth'), [
            'phone' => $newPhone,
            'otp' => '123456',
        ]);
        $response->assertStatus(200);

        // Step 3: Update profile with new phone
        $response = $this->withToken($token)->putJson(route('suppliers.profile.update'), [
            'phone' => $newPhone,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $supplier->id,
            'phone' => $newPhone,
        ]);
    }

    /**
     * Test supplier can request and verify OTP for a phone number.
     */
    public function test_supplier_can_request_and_verify_otp()
    {
        $supplier = User::factory()->create([
            'role' => UserRole::SUPPLIER,
            'status' => UserStatus::APPROVED,
            'is_verified' => true,
            'phone' => '+966500000001',
        ]);
        $supplier->fields()->attach(Field::factory()->create());
        $token = $supplier->createToken('test')->plainTextToken;
        $newPhone = '+966500000003';

        // Request OTP
        $response = $this->withToken($token)->postJson(route('auth.request-otp-auth'), [
            'phone' => $newPhone,
        ]);
        $response->assertStatus(200);

        // Verify OTP (fixed OTP is '123456')
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
}
