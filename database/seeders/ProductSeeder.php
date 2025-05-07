<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            $this->command->info('No categories found. Please run CategorySeeder first.');
            return;
        }

        // Get category IDs
        $vegetablesCategory = Category::where('name->en', 'Vegetables')->first();
        $fruitsCategory = Category::where('name->en', 'Fruits')->first();
        $dairyCategory = Category::where('name->en', 'Dairy Products')->first();
        $meatCategory = Category::where('name->en', 'Meat & Poultry')->first();
        $bakeryCategory = Category::where('name->en', 'Bakery & Bread')->first();

        $products = [
            // Vegetables
            [
                'name' => [
                    'en' => 'Fresh Organic Tomatoes',
                    'ar' => 'طماطم عضوية طازجة'
                ],
                'image' => 'products/tomatoes.jpg',
                'price' => 4.99,
                'stock_qty' => 200,
                'category_id' => $vegetablesCategory->id
            ],
            [
                'name' => [
                    'en' => 'Mixed Vegetables Pack',
                    'ar' => 'عبوة خضروات متنوعة'
                ],
                'image' => 'products/vegetables.jpg',
                'price' => 6.99,
                'stock_qty' => 75,
                'category_id' => $vegetablesCategory->id
            ],

            // Fruits
            [
                'name' => [
                    'en' => 'Fresh Fruits Basket',
                    'ar' => 'سلة فواكه طازجة'
                ],
                'image' => 'products/fruits.jpg',
                'price' => 19.99,
                'stock_qty' => 40,
                'category_id' => $fruitsCategory->id
            ],

            // Dairy Products
            [
                'name' => [
                    'en' => 'Fresh Milk',
                    'ar' => 'حليب طازج'
                ],
                'image' => 'products/milk.jpg',
                'price' => 2.99,
                'stock_qty' => 100,
                'category_id' => $dairyCategory->id
            ],
            [
                'name' => [
                    'en' => 'Organic Eggs (12 pieces)',
                    'ar' => 'بيض عضوي (12 قطعة)'
                ],
                'image' => 'products/eggs.jpg',
                'price' => 5.99,
                'stock_qty' => 80,
                'category_id' => $dairyCategory->id
            ],

            // Meat & Poultry
            [
                'name' => [
                    'en' => 'Fresh Chicken Breast',
                    'ar' => 'صدور دجاج طازجة'
                ],
                'image' => 'products/chicken.jpg',
                'price' => 12.99,
                'stock_qty' => 50,
                'category_id' => $meatCategory->id
            ],
            [
                'name' => [
                    'en' => 'Fresh Fish Fillet',
                    'ar' => 'فيليه سمك طازج'
                ],
                'image' => 'products/fish.jpg',
                'price' => 15.99,
                'stock_qty' => 30,
                'category_id' => $meatCategory->id
            ],

            // Bakery & Bread
            [
                'name' => [
                    'en' => 'Whole Grain Bread',
                    'ar' => 'خبز القمح الكامل'
                ],
                'image' => 'products/bread.jpg',
                'price' => 3.99,
                'stock_qty' => 150,
                'category_id' => $bakeryCategory->id
            ]
        ];

        foreach ($products as $product) {
            Product::updateOrCreate($product);
        }
    }
}

