<?php

namespace Database\Factories;

use App\Enums\Order\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => $this->faker->randomElement(PaymentStatus::cases()),
            'amount' => $this->faker->randomFloat(2, 100, 5000),
            'currency' => 'SAR',
        ];
    }
}
