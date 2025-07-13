<?php

namespace Database\Factories;

use App\Enums\Order\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'total' => $this->faker->randomFloat(2, 100, 5000),
            'total_discount' => $this->faker->randomFloat(2, 0, 500),
            'status' => $this->faker->randomElement(OrderStatus::cases()),
        ];
    }
}
