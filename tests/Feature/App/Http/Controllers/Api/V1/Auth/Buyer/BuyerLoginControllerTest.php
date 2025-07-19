<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1\Auth\Buyer;

use Tests\TestCase;
use App\Models\User;
use App\Enums\UserRole;
use App\Models\Address;
use App\Enums\UserStatus;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


class BuyerLoginControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_buyer_can_login()
    {
        $phone = '966501234567';
        $this->createBuyer(['phone' => $phone]);

        $this->postJson(route('auth.request-otp'), ['phone' => $phone]);

        $response = $this->postJson(route('auth.buyer.login'), [
            'phone' => $phone,
            'otp' => '123456',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user' => ['id', 'name', 'phone', 'is_verified'],
                    'token'
                ],
                'message'
            ]);
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
