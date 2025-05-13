<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Factory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $factory = Factory::first();

        if (!$factory) {
            if ($this->command) {
                $this->command->info('No factory found. Please run FactorySeeder first.');
            }
            return;
        }

        // Create categories if they don't exist
        $categories = [
            'Vegetables' => [
                'en' => 'Vegetables',
                'ar' => 'خضروات'
            ],
            'Fruits' => [
                'en' => 'Fruits',
                'ar' => 'فواكه'
            ],
            'Dairy Products' => [
                'en' => 'Dairy Products',
                'ar' => 'منتجات الألبان'
            ],
            'Meat & Poultry' => [
                'en' => 'Meat & Poultry',
                'ar' => 'اللحوم والدواجن'
            ],
            'Bakery & Bread' => [
                'en' => 'Bakery & Bread',
                'ar' => 'المخبوزات والخبز'
            ]
        ];

        $categoryIds = [];
        foreach ($categories as $key => $name) {
            $category = Category::updateOrCreate(
                ['name->en' => $name['en']],
                [
                    'name' => $name,
                    'is_active' => true
                ]
            );
            $categoryIds[$key] = $category->id;
        }

        $products = [
            // Vegetables
            [
                'name' => [
                    'en' => 'Fresh Organic Tomatoes',
                    'ar' => 'طماطم عضوية طازجة'
                ],
                'price' => 4.99,
                'stock_qty' => 200,
                'category_id' => $categoryIds['Vegetables'],
                'factory_id' => $factory->id
            ],
            [
                'name' => [
                    'en' => 'Mixed Vegetables Pack',
                    'ar' => 'عبوة خضروات متنوعة'
                ],
                'price' => 6.99,
                'stock_qty' => 75,
                'category_id' => $categoryIds['Vegetables'],
                'factory_id' => $factory->id
            ],

            // Fruits
            [
                'name' => [
                    'en' => 'Fresh Fruits Basket',
                    'ar' => 'سلة فواكه طازجة'
                ],
                'price' => 19.99,
                'stock_qty' => 40,
                'category_id' => $categoryIds['Fruits'],
                'factory_id' => $factory->id
            ],

            // Dairy Products
            [
                'name' => [
                    'en' => 'Fresh Milk',
                    'ar' => 'حليب طازج'
                ],
                'price' => 2.99,
                'stock_qty' => 100,
                'category_id' => $categoryIds['Dairy Products'],
                'factory_id' => $factory->id
            ],
            [
                'name' => [
                    'en' => 'Organic Eggs (12 pieces)',
                    'ar' => 'بيض عضوي (12 قطعة)'
                ],
                'price' => 5.99,
                'stock_qty' => 80,
                'category_id' => $categoryIds['Dairy Products'],
                'factory_id' => $factory->id
            ],

            // Meat & Poultry
            [
                'name' => [
                    'en' => 'Fresh Chicken Breast',
                    'ar' => 'صدور دجاج طازجة'
                ],
                'price' => 12.99,
                'stock_qty' => 50,
                'category_id' => $categoryIds['Meat & Poultry'],
                'factory_id' => $factory->id
            ],
            [
                'name' => [
                    'en' => 'Fresh Fish Fillet',
                    'ar' => 'فيليه سمك طازج'
                ],
                'price' => 15.99,
                'stock_qty' => 30,
                'category_id' => $categoryIds['Meat & Poultry'],
                'factory_id' => $factory->id
            ],

            // Bakery & Bread
            [
                'name' => [
                    'en' => 'Whole Grain Bread',
                    'ar' => 'خبز القمح الكامل'
                ],
                'price' => 3.99,
                'stock_qty' => 150,
                'category_id' => $categoryIds['Bakery & Bread'],
                'factory_id' => $factory->id
            ]
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['name->en' => $product['name']['en']],
                $product
            );
        }
    }
}

