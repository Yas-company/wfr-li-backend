<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => [
                'en' => $this->faker->word(),
                'ar' => $this->faker->word(),
            ],
            'description' => [
                'en' => $this->faker->text(),
                'ar' => $this->faker->text(),
            ],
            'image' => $this->faker->imageUrl(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'price_before_discount' => $this->faker->randomFloat(2, 10, 1000),
            'stock_qty' => $this->faker->numberBetween(10, 1000),
            'nearly_out_of_stock_limit' => $this->faker->numberBetween(5, 20),
            'quantity' => $this->faker->numberBetween(10, 100),
            'unit_type' => $this->faker->numberBetween(0, 3),
            'status' => $this->faker->numberBetween(0, 1),
            'is_active' => true,
            'is_featured' => $this->faker->boolean(20),
            'min_order_quantity' => $this->faker->numberBetween(2, 10),
            'category_id' => Category::factory()
        ];
    }
}
