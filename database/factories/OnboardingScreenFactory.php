<?php

namespace Database\Factories;

use App\Models\OnboardingScreen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OnboardingScreen>
 */
class OnboardingScreenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'image' => $this->faker->imageUrl(),
            'title' => [
                'en' => fake()->sentence(3),
                'ar' => fake()->sentence(3),
            ],
            'description' => [
                'en' => fake()->paragraph(2),
                'ar' => fake()->paragraph(2),
            ],
            'order' => fake()->unique()->numberBetween(1, 10),
            'is_active' => fake()->boolean(80), // 80% chance of being active
        ];
    }

    /**
     * Indicate that the onboarding screen is active.
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the onboarding screen is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }
}
