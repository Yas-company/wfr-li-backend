<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cities = ['makkah', 'jeddah', 'riyadh', 'dammam', 'medina'];

        return [
            'name' => $this->faker->name(),
            'street' => $this->faker->streetAddress(),
            'city' => $this->faker->randomElement($cities),
            'phone' => '9665' . $this->faker->numberBetween(10000000, 99999999),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'is_default' => $this->faker->boolean(),
        ];
    }
}
