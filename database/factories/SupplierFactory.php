<?php

namespace Database\Factories;

use App\Models\Factory as FactoryModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'location' => fake()->city(),
            'factory_id' => FactoryModel::factory(),
            'email' => fake()->unique()->companyEmail(),
            'password' => Hash::make('password'),
            'is_verified' => fake()->boolean(80), // 80% chance of being verified
        ];
    }
}
