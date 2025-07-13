<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'phone' => $this->faker->unique()->phoneNumber(),
            'business_name' => $this->faker->company(),
            'country_code' => $this->faker->countryCode(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('Password123!'),
            'remember_token' => Str::random(10),
            'role' => $this->faker->randomElement(UserRole::cases()),
            'business_name' => $this->faker->company(),
            'otp_expiry' => now()->addMinutes(5),
            'is_verified' => true,
            'status' => UserStatus::APPROVED,
            'license_attachment' => $this->faker->imageUrl(),
            'commercial_register_attachment' => $this->faker->imageUrl(),
            'image' => $this->faker->imageUrl(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Configure the model factory for a buyer role.
     */
    public function buyer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::BUYER,
            'business_name' => null,
        ]);
    }

    public function supplier()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => UserRole::SUPPLIER,
                'business_name' => $this->faker->company(),
            ];
        });
    }
}
