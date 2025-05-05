<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => [
                    'en' => 'Vegetables',
                    'ar' => 'خضروات'
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Fruits',
                    'ar' => 'فواكه'
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Dairy Products',
                    'ar' => 'منتجات الألبان'
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Meat & Poultry',
                    'ar' => 'اللحوم والدواجن'
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Bakery & Bread',
                    'ar' => 'المخبوزات والخبز'
                ],
                'is_active' => false,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate($category);
        }
    }
}
