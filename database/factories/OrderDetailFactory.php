<?php

namespace Database\Factories;

use App\Enums\Order\PaymentMethod;
use App\Enums\Order\PaymentStatus;
use App\Enums\Order\ShippingMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderDetail>
 */
class OrderDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payment_status' => $this->faker->randomElement(PaymentStatus::cases()),
            'payment_method' => $this->faker->randomElement(PaymentMethod::cases()),
            'tracking_number' => $this->faker->uuid(),
            'estimated_delivery_date' => $this->faker->dateTimeBetween('+1 day', '+1 week'),
            'notes' => $this->faker->paragraph(),
            'shipping_method' => $this->faker->randomElement(ShippingMethod::cases()),
        ];
    }
}
