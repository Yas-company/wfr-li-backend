<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
            'country_code' => $this->faker->countryCode(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('Password123!'),
            'remember_token' => Str::random(10),
            'role' => 'buyer',
            'address' => $this->faker->address(),
            'latitude' => $this->faker->latitude(24.5, 25.0), // Riyadh area
            'longitude' => $this->faker->longitude(46.5, 47.0), // Riyadh area
            'business_name' => $this->faker->company(),
            'lic_id' => $this->faker->unique()->numerify('LIC-####'),
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
            'role' => 'buyer',
        ]);
    }
}
