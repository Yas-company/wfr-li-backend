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
        // Fetch categories by English name
        $categories = [
            'Rice & Grains' => Category::where('name->en', 'Rice & Grains')->first(),
            'Oils & Ghee' => Category::where('name->en', 'Oils & Ghee')->first(),
            'Meat & Poultry' => Category::where('name->en', 'Meat & Poultry')->first(),
            'Vegetables' => Category::where('name->en', 'Vegetables')->first(),
            'Canned Food & Tuna' => Category::where('name->en', 'Canned Food & Tuna')->first(),
            'Juices & Drinks' => Category::where('name->en', 'Juices & Drinks')->first(),
            'Coffee & Tea' => Category::where('name->en', 'Coffee & Tea')->first(),
            'Dairy Products' => Category::where('name->en', 'Dairy Products')->first(),
        ];

        $products = [
            [
                'id' => 1,
                'name' => [ 'en' => 'Al Diyaf Basmati Rice 5kg', 'ar' => 'أرز الضياف بسمتي هندي 5 كجم' ],
                'description' => [ 'en' => 'Basmati rice with long grains from India', 'ar' => 'أرز بسمتي أبيض طويل الحبة من الهند' ],
                'stock_qty' => 20,
                'price' => 48,
                'price_before_discount' => 55,
                'category_id' => $categories['Rice & Grains']->id,
            ],
            [
                'id' => 2,
                'name' => [ 'en' => 'Premium Virgin Olive Oil 500ml', 'ar' => 'زيت زيتون صافي بكر ممتاز 500 مل' ],
                'description' => [ 'en' => 'Premium extra virgin olive oil produced by Masane company', 'ar' => 'زيت زيتون طبيعي بكر ممتاز من إنتاج شركة مصانع' ],
                'stock_qty' => 15,
                'price' => 70,
                'price_before_discount' => 80,
                'category_id' => $categories['Oils & Ghee']->id,
            ],
            [
                'id' => 3,
                'name' => [ 'en' => 'Aldeyra Whole Chicken 1100g', 'ar' => 'دجاجة كاملة الديرة 1100 جرام' ],
                'description' => [ 'en' => 'Premium fresh chicken slaughtered according to Islamic law', 'ar' => 'دجاج طازج ممتاز مذبوح حسب الشريعة الإسلامية' ],
                'stock_qty' => 25,
                'price' => 32,
                'price_before_discount' => 38,
                'category_id' => $categories['Meat & Poultry']->id,
            ],
            [
                'id' => 4,
                'name' => [ 'en' => 'Fresh Cucumber', 'ar' => 'خيار طازج' ],
                'description' => [ 'en' => 'Fresh green cucumber from local farms', 'ar' => 'خيار أخضر طازج من مزارع محلية' ],
                'stock_qty' => 40,
                'price' => 5,
                'price_before_discount' => 6,
                'category_id' => $categories['Vegetables']->id,
            ],
            [
                'id' => 5,
                'name' => [ 'en' => 'Fresh Tomato', 'ar' => 'طماطم طازجة' ],
                'description' => [ 'en' => 'Red, ripe, and fresh tomatoes from local farms', 'ar' => 'طماطم حمراء، ناضجة وطازجة من مزارع محلية' ],
                'stock_qty' => 35,
                'price' => 4,
                'price_before_discount' => 5,
                'category_id' => $categories['Vegetables']->id,
            ],
            [
                'id' => 6,
                'name' => [ 'en' => 'Noor Sunflower Oil 1.5L', 'ar' => 'زيت نور دوار الشمس 1.5 لتر' ],
                'description' => [ 'en' => 'Pure sunflower oil fortified with vitamin D', 'ar' => 'زيت دوار الشمس نقي مدعم بفيتامين د' ],
                'stock_qty' => 20,
                'price' => 30,
                'price_before_discount' => 35,
                'category_id' => $categories['Oils & Ghee']->id,
            ],
            [
                'id' => 7,
                'name' => [ 'en' => 'Tuna 750g', 'ar' => 'تونة 750 جم' ],
                'description' => [ 'en' => 'Premium tuna and avocado cream from the best types', 'ar' => 'كريمة التونة والأفوكادو المميزة من أفضل الأنواع' ],
                'stock_qty' => 18,
                'price' => 52,
                'price_before_discount' => 60,
                'category_id' => $categories['Canned Food & Tuna']->id,
            ],
            [
                'id' => 8,
                'name' => [ 'en' => 'Mazza Basmati Calrose Rice', 'ar' => 'أرز مزة بسمتي كالروز' ],
                'description' => [ 'en' => 'High quality medium grain Calrose rice', 'ar' => 'أرز كالروز متوسط الحبة عالي الجودة' ],
                'stock_qty' => 30,
                'price' => 35,
                'price_before_discount' => 40,
                'category_id' => $categories['Rice & Grains']->id,
            ],
            [
                'id' => 9,
                'name' => [ 'en' => 'Premium Virgin Olive Oil 500ml (Extra)', 'ar' => 'زيت زيتون صافي بكر ممتاز 500 مل (نسخة إضافية)' ],
                'description' => [ 'en' => 'Premium extra virgin olive oil produced by Masane company', 'ar' => 'زيت زيتون طبيعي بكر ممتاز من إنتاج شركة مصانع' ],
                'stock_qty' => 10,
                'price' => 70,
                'price_before_discount' => 80,
                'category_id' => $categories['Oils & Ghee']->id,
            ],
            [
                'id' => 10,
                'name' => [ 'en' => 'Al Diyaf Basmati Rice 5kg (Extra)', 'ar' => 'أرز الضياف بسمتي هندي 5 كجم (نسخة إضافية)' ],
                'description' => [ 'en' => 'Basmati rice with long grains from India', 'ar' => 'أرز بسمتي أبيض طويل الحبة من الهند' ],
                'stock_qty' => 10,
                'price' => 48,
                'price_before_discount' => 55,
                'category_id' => $categories['Rice & Grains']->id,
            ],
            [
                'id' => 11,
                'name' => [ 'en' => 'Al Rabie Orange Drink', 'ar' => 'شراب البرتقال الربيع' ],
                'description' => [ 'en' => 'Natural orange drink free from preservatives and artificial colors, rich in flavor', 'ar' => 'شراب برتقال طبيعي خالي من المواد الحافظة والألوان الاصطناعية غني بالنكهة' ],
                'stock_qty' => 25,
                'price' => 6,
                'price_before_discount' => 8,
                'category_id' => $categories['Juices & Drinks']->id,
            ],
            [
                'id' => 12,
                'name' => [ 'en' => 'Classic Coffee 200g', 'ar' => 'قهوة كلاسيك 200 جم' ],
                'description' => [ 'en' => 'High quality instant coffee', 'ar' => 'قهوة سريعة التحضير مكونة عالية الجودة' ],
                'stock_qty' => 12,
                'price' => 38,
                'price_before_discount' => 45,
                'category_id' => $categories['Coffee & Tea']->id,
            ],
            [
                'id' => 13,
                'name' => [ 'en' => 'Season Mango Juice', 'ar' => 'عصير مانجو سيزن' ],
                'description' => [ 'en' => 'Natural mango drink with a nutritional formula rich in vitamins and minerals', 'ar' => 'شراب مانجو طبيعي تركيبة غذائية مدعمة بالفيتامينات والمعادن' ],
                'stock_qty' => 18,
                'price' => 7,
                'price_before_discount' => 9,
                'category_id' => $categories['Juices & Drinks']->id,
            ],
            [
                'id' => 14,
                'name' => [ 'en' => 'Luna Powdered Milk', 'ar' => 'حليب مجفف لونا' ],
                'description' => [ 'en' => 'High quality powdered milk rich in calcium and vitamins', 'ar' => 'حليب مجفف عالي الجودة غني بالكالسيوم والفيتامينات' ],
                'stock_qty' => 20,
                'price' => 26,
                'price_before_discount' => 30,
                'category_id' => $categories['Dairy Products']->id,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['id' => $product['id']],
                $product
            );
        }
    }
}

