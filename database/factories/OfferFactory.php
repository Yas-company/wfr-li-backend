<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Offer>
 */
class OfferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
           'code' => strtoupper(Str::random(6)), // رمز العرض عشوائي
        'title' => $this->faker->sentence(3),
        'description' => $this->faker->optional()->paragraph(),
        'discount_value' => $this->faker->randomFloat(2, 5, 50), // بين 5 إلى 50
        'discount_type' => $this->faker->randomElement(['percentage', 'fixed']),
        'usage_limit' => $this->faker->optional()->numberBetween(10, 100),
        'usage_count' => 0,
        'start_date' => Carbon::now()->subDays(rand(0, 5)),
        'end_date' => Carbon::now()->addDays(rand(5, 10)),
        'is_active' => true,
        
        ];
    }
}
