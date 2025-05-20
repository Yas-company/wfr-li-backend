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
                'id' => 1,
                'name' => [ 'en' => 'Rice & Grains', 'ar' => 'الأرز والحبوب' ],
                'is_active' => true,
            ],
            [
                'id' => 2,
                'name' => [ 'en' => 'Oils & Ghee', 'ar' => 'الزيوت والسمن' ],
                'is_active' => true,
            ],
            [
                'id' => 3,
                'name' => [ 'en' => 'Meat & Poultry', 'ar' => 'اللحوم والدواجن' ],
                'is_active' => true,
            ],
            [
                'id' => 4,
                'name' => [ 'en' => 'Vegetables', 'ar' => 'خضروات' ],
                'is_active' => true,
            ],
            [
                'id' => 5,
                'name' => [ 'en' => 'Canned Food & Tuna', 'ar' => 'المعلبات والتونة' ],
                'is_active' => true,
            ],
            [
                'id' => 6,
                'name' => [ 'en' => 'Juices & Drinks', 'ar' => 'العصائر والمشروبات' ],
                'is_active' => true,
            ],
            [
                'id' => 7,
                'name' => [ 'en' => 'Coffee & Tea', 'ar' => 'القهوة والشاي' ],
                'is_active' => true,
            ],
            [
                'id' => 8,
                'name' => [ 'en' => 'Dairy Products', 'ar' => 'منتجات الألبان' ],
                'is_active' => true,
            ],
            [
                'id' => 9,
                'name' => [ 'en' => 'Spices & Nuts', 'ar' => 'توابل ومكسرات' ],
                'is_active' => true,
            ],
            [
                'id' => 10,
                'name' => [ 'en' => 'Sauces & Condiments', 'ar' => 'الصلصات والمثلجات' ],
                'is_active' => true,
            ],
            [
                'id' => 11,
                'name' => [ 'en' => 'Desserts & Sweets', 'ar' => 'المثليات والحلويات' ],
                'is_active' => true,
            ],
            [
                'id' => 12,
                'name' => [ 'en' => 'Frozen Foods', 'ar' => 'منتجات مجمدة' ],
                'is_active' => true,
            ]

        ];

        foreach ($categories as $category) {
            Category::updateOrCreate([
                'id' => $category['id'],
            ], [
                ...$category,
            ]);
        }
    }
}
