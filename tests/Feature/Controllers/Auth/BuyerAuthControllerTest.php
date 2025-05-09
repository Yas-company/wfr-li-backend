<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BuyerAuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private string $baseUrl = '/api/buyer';
    private UserFactory $userFactory;
    private array $validPassword = ['password' => 'Password123!', 'password_confirmation' => 'Password123!'];

    protected function setUp(): void
    {
        parent::setUp();
        $this->userFactory = UserFactory::new();
    }

    /**
     * @group buyer
     * @group auth
     * @group registration
     */
    public function test_buyer_can_register_with_valid_data(): void
    {
        $userData = $this->getValidRegistrationData();

        $response = $this->postJson("{$this->baseUrl}/register", $userData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => __('messages.registration_successful')
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'phone',
                        'email',
                        'address',
                        'location',
                        'business_name',
                        'lic_id',
                        'role',
                        'created_at',
                        'updated_at'
                    ],
                    'token'
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'phone' => $userData['phone'],
            'email' => $userData['email'],
            'role' => 'buyer'
        ]);
    }

    /**
     * @group buyer
     * @group auth
     * @group registration
     */
    public function test_buyer_cannot_register_with_duplicate_phone(): void
    {
        $existingUser = $this->userFactory->create();
        $userData = $this->getValidRegistrationData(['phone' => $existingUser->phone]);

        $response = $this->postJson("{$this->baseUrl}/register", $userData);

        $response->assertStatus(422)
            ->assertJson([
                'message' => __('messages.validation.unique.phone'),
                'errors' => [
                    'phone' => [__('messages.validation.unique.phone')]
                ]
            ]);
    }

    /**
     * @group buyer
     * @group auth
     * @group registration
     */
    public function test_buyer_cannot_register_with_duplicate_license_id(): void
    {
        $existingUser = $this->userFactory->create();
        $userData = $this->getValidRegistrationData(['lic_id' => $existingUser->lic_id]);

        $response = $this->postJson("{$this->baseUrl}/register", $userData);

        $response->assertStatus(422)
            ->assertJson([
                'message' => __('messages.validation.unique.lic_id'),
                'errors' => [
                    'lic_id' => [__('messages.validation.unique.lic_id')]
                ]
            ]);
    }

    /**
     * @group buyer
     * @group auth
     * @group login
     */
    public function test_buyer_can_login_with_valid_credentials(): void
    {
        $user = $this->userFactory->create();

        $response = $this->postJson("{$this->baseUrl}/login", [
            'phone' => $user->phone,
            'password' => 'Password123!'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => __('messages.login_successful')
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'phone',
                        'email',
                        'role'
                    ],
                    'token'
                ]
            ]);
    }

    /**
     * @group buyer
     * @group auth
     * @group login
     */
    public function test_buyer_cannot_login_with_invalid_credentials(): void
    {
        $user = $this->userFactory->create();

        $response = $this->postJson("{$this->baseUrl}/login", [
            'phone' => $user->phone,
            'password' => 'wrong_password'
        ]);

        $response->assertStatus(500)
            ->assertJson([
                'success' => false,
                'message' => __('messages.login_failed')
            ]);
    }

    /**
     * @group buyer
     * @group auth
     * @group logout
     */
    public function test_buyer_can_logout(): void
    {
        $user = $this->userFactory->create();
        $token = $user->createToken('buyer-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->postJson("{$this->baseUrl}/logout");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => __('messages.logout_successful')
            ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class
        ]);
    }

    /**
     * @group buyer
     * @group auth
     * @group profile
     */
    public function test_buyer_can_get_profile(): void
    {
        $user = $this->userFactory->create();
        $token = $user->createToken('buyer-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->getJson("{$this->baseUrl}/me");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'role' => 'buyer'
                ]
            ]);
    }

    /**
     * Get valid registration data with optional overrides
     */
    private function getValidRegistrationData(array $overrides = []): array
    {
        return array_merge(
            $this->userFactory->make()->toArray(),
            $this->validPassword,
            $overrides
        );
    }
}
