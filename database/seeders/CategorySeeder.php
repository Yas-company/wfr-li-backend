<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Field;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Food & Beverage
            ['en' => 'Fruits', 'ar' => 'فواكه'],
            ['en' => 'Vegetables', 'ar' => 'خضروات'],
            ['en' => 'Meat', 'ar' => 'لحوم'],

            // Electronics
            ['en' => 'Mobile Phones', 'ar' => 'هواتف محمولة'],
            ['en' => 'Laptops', 'ar' => 'أجهزة كمبيوتر محمولة'],

            // Home Appliances
            ['en' => 'Refrigerators', 'ar' => 'ثلاجات'],
            ['en' => 'Washing Machines', 'ar' => 'غسالات'],

            // Clothing
            ['en' => 'Men\'s Clothing', 'ar' => 'ملابس رجالية'],
            ['en' => 'Women\'s Clothing', 'ar' => 'ملابس نسائية'],

            // Health & Beauty
            ['en' => 'Skincare', 'ar' => 'العناية بالبشرة'],
            ['en' => 'Makeup', 'ar' => 'مكياج'],
        ];

        $fieldIds = Field::pluck('id')->toArray();

        foreach ($categories as $category) {
            Category::factory()->create([
                'name' => $category,
                'field_id' => fake()->randomElement($fieldIds),
            ]);
        }

    }
}
