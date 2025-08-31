<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Address;
use App\Models\Field;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_buyer_can_verify_otp()
    {
        $data = $this->getValidRegistrationData(UserRole::BUYER->value);
        $this->postJson(route('auth.buyer.register'), $data);

        $user = User::where('phone', $data['phone'])->first();

        $response = $this->postJson(route('auth.verify-otp'), [
            'phone' => $user->phone,
            'otp' => '123456',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user' => ['id', 'name', 'phone', 'is_verified'],
                    'token',
                ],
                'message',
            ])
            ->assertJsonPath('data.user.is_verified', true);

        $this->assertTrue(User::find($user->id)->is_verified);
    }

    public function test_cannot_verify_with_invalid_otp()
    {
        $data = $this->getValidRegistrationData(UserRole::BUYER->value);
        $this->postJson(route('auth.buyer.register'), $data);

        $user = User::where('phone', $data['phone'])->first();

        $response = $this->postJson(route('auth.verify-otp'), [
            'phone' => $user->phone,
            'otp' => '098181',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', __('messages.invalid_otp'));
    }

    public function test_can_request_password_reset()
    {
        $user = $this->createBuyer();

        $response = $this->postJson(route('auth.forgot-password'), [
            'phone' => $user->phone,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['phone'],
                'message',
            ])
            ->assertJsonPath('message', __('messages.otp_sent'));
    }

    public function test_can_reset_password_with_valid_otp()
    {
        $user = $this->createBuyer();
        $newPassword = 'NewPassword123!';

        $this->postJson(route('auth.forgot-password'), ['phone' => $user->phone]);

        $response = $this->postJson(route('auth.verify-otp'), [
            'phone' => $user->phone,
            'otp' => '123456',
        ]);

        $response = $this->postJson(route('auth.reset-password'), [
            'phone' => $user->phone,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user' => ['id', 'name', 'phone'],
                    'token',
                ],
                'message',
            ]);

        $this->assertTrue(Hash::check($newPassword, $user->fresh()->password));
    }

    public function test_cannot_reset_password_without_otp_verification()
    {
        $user = $this->createBuyer();
        $newPassword = 'NewPassword123!';

        $response = $this->postJson(route('auth.reset-password'), [
            'phone' => $user->phone,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', __('messages.invalid_otp'));
    }

    public function test_authenticated_user_can_get_profile()
    {
        $user = $this->createBuyer();

        $response = $this->actingAs($user)
            ->getJson(route('auth.me'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'phone', 'email', 'role'],
            ]);
    }

    public function test_authenticated_user_can_change_password()
    {
        $oldPassword = 'OldPassword123!';
        $newPassword = 'NewPassword123!';
        $user = $this->createBuyer(['password' => Hash::make($oldPassword)]);

        $response = $this->actingAs($user)
            ->postJson(route('auth.change-password'), [
                'current_password' => $oldPassword,
                'password' => $newPassword,
                'password_confirmation' => $newPassword,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', __('messages.password_changed_successful'));

        $this->assertTrue(Hash::check($newPassword, $user->fresh()->password));
    }

    public function test_cannot_change_password_with_wrong_current_password()
    {
        $user = $this->createBuyer(['password' => Hash::make('OldPassword123!')]);

        $response = $this->actingAs($user)
            ->postJson(route('auth.change-password'), [
                'current_password' => 'wrong',
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', __('messages.invalid_current_password'));
    }

    public function test_authenticated_user_can_logout()
    {
        $user = $this->createBuyer([
            'password' => Hash::make('Password123!'),
        ]);

        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson(route('auth.logout'));

        $response->assertStatus(200)
            ->assertJsonPath('message', __('messages.logout_successful'));

        $this->assertCount(0, $user->fresh()->tokens);
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
