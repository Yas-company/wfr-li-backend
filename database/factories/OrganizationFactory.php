<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'tax_number' => $this->faker->numberBetween(1000000000, 9999999999),
            'commercial_register_number' => $this->faker->numberBetween(1000000000, 9999999999),
            'created_by' => User::factory(),
        ];
    }
}
