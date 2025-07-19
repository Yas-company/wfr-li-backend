<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1\Auth\Buyer;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BuyerRegistrationControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_buyer_can_register()
    {
        $response = $this->postJson(route('auth.buyer.register'), $this->getValidRegistrationData());

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id', 'name', 'phone', 'email', 'role', 'is_verified',
                    ],
                    'message',
                    'requires_verification',
                ],
            ])
            ->assertJsonPath('data.requires_verification', true);

        $this->assertDatabaseHas('users', [
            'phone' => $response->json('data.user.phone'),
            'role' => UserRole::BUYER,
            'is_verified' => false,
            'status' => UserStatus::APPROVED,
        ]);
    }

    public function test_buyer_cannot_register_with_existing_verified_phone()
    {
        $user = $this->createBuyer();
        $data = $this->getValidRegistrationData();
        $data['phone'] = $user->phone;

        $response = $this->postJson(route('auth.buyer.register'), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
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

    protected function getValidRegistrationData(): array
    {
        return [
            'name' => $this->faker->name,
            'phone' => '966'.$this->faker->numerify('########'),
            'address' => [ // Keep this as array for the request
                'name' => $this->faker->word,
                'street' => $this->faker->streetAddress,
                'city' => $this->faker->city,
                'phone' => '966'.$this->faker->numerify('########'),
                'latitude' => $this->faker->latitude,
                'longitude' => $this->faker->longitude,
            ],
        ];
    }
}
