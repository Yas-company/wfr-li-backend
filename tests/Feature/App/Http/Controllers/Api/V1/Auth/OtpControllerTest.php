<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Enums\UserRole;
use App\Models\Address;
use App\Enums\UserStatus;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


class OtpControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_verified_buyer_can_request_otp()
    {
        $phone = '966501234567';
        $this->createBuyer(['phone' => $phone]);

        $response = $this->postJson(route('auth.request-otp'), ['phone' => $phone]);

        $response->assertStatus(200);
    }


    // helpers
    protected function createBuyer(array $attributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'role' => UserRole::BUYER,
            'status' => UserStatus::APPROVED,
            'is_verified' => true,
        ], $attributes));

        // Create associated address
        Address::factory()->create(['user_id' => $user->id]);

        return $user;
    }
}
