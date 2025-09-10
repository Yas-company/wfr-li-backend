<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UnitType;
use App\Enums\UserRole;
use App\Models\Product;
use App\Models\Category;
use App\Enums\ProductStatus;
use Illuminate\Database\Seeder;
use App\Services\Product\ProductPricingCalculatorService;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $categories = Category::all();
        $suppliers = User::where('role', UserRole::SUPPLIER)->get();

        // Array of realistic product names in English and Arabic
        $productNames = [
            ['en' => 'Fresh Tomatoes', 'ar' => 'طماطم طازجة'],
            ['en' => 'Organic Apples', 'ar' => 'تفاح عضوي'],
            ['en' => 'Extra Virgin Olive Oil', 'ar' => 'زيت زيتون بكر ممتاز'],
            ['en' => 'Fresh Bread', 'ar' => 'خبز طازج'],
            ['en' => 'Greek Yogurt', 'ar' => 'لبن يوناني'],
            ['en' => 'Free Range Eggs', 'ar' => 'بيض طبيعي'],
            ['en' => 'Premium Rice', 'ar' => 'أرز فاخر'],
            ['en' => 'Organic Honey', 'ar' => 'عسل عضوي'],
            ['en' => 'Fresh Salmon', 'ar' => 'سلمون طازج'],
            ['en' => 'Artisan Cheese', 'ar' => 'جبن حرفي'],
            ['en' => 'Organic Spinach', 'ar' => 'سبانخ عضوية'],
            ['en' => 'Premium Coffee Beans', 'ar' => 'حبوب قهوة فاخرة'],
            ['en' => 'Fresh Chicken Breast', 'ar' => 'صدر دجاج طازج'],
            ['en' => 'Whole Wheat Pasta', 'ar' => 'مكرونة قمح كامل'],
            ['en' => 'Natural Almonds', 'ar' => 'لوز طبيعي'],
            ['en' => 'Fresh Orange Juice', 'ar' => 'عصير برتقال طازج'],
            ['en' => 'Organic Carrots', 'ar' => 'جزر عضوي'],
            ['en' => 'Premium Beef Steak', 'ar' => 'ستيك لحم فاخر'],
            ['en' => 'Traditional Dates', 'ar' => 'تمر تقليدي'],
            ['en' => 'Fresh Mint', 'ar' => 'نعناع طازج'],
        ];

        $productDescriptions = [
            ['en' => 'High quality fresh product sourced from local farms', 'ar' => 'منتج طازج عالي الجودة من المزارع المحلية'],
            ['en' => 'Premium organic product with no artificial additives', 'ar' => 'منتج عضوي فاخر بدون إضافات صناعية'],
            ['en' => 'Fresh and natural, perfect for daily consumption', 'ar' => 'طازج وطبيعي، مثالي للاستهلاك اليومي'],
            ['en' => 'Traditional quality with modern packaging', 'ar' => 'جودة تقليدية مع تغليف عصري'],
            ['en' => 'Nutritious and delicious, rich in vitamins', 'ar' => 'مغذي ولذيذ، غني بالفيتامينات'],
        ];

        $unitTypes = [
            UnitType::PIECE->value,
            UnitType::KG->value,
            UnitType::LITER->value,
            UnitType::PACK->value,
            UnitType::BOX->value,
            UnitType::DOZEN->value,
            UnitType::BOTTLE->value,
            UnitType::CAN->value,
        ];

        foreach ($categories as $category) {
            $productsCount = rand(8, 20);
            $categoryName = $category->getTranslation('name', 'en') ?? $category->name ?? 'Unknown Category';

            for ($i = 0; $i < $productsCount; $i++) {
                $nameIndex = array_rand($productNames);
                $descIndex = array_rand($productDescriptions);
                $unitType = $unitTypes[array_rand($unitTypes)];

                $basePrice = rand(10, 500);
                $discountRate = rand(1, 50) / 100;

                $prices = app(ProductPricingCalculatorService::class)->calculate($basePrice, $discountRate);

                Product::withoutSyncingToSearch(function() use(
                    $productNames,
                    $productDescriptions,
                    $nameIndex,
                    $descIndex,
                    $prices,
                    $discountRate,
                    $unitType,
                    $category,
                    $suppliers
                    ) {
                        Product::create([
                            'name' => $productNames[$nameIndex],
                            'description' => $productDescriptions[$descIndex],
                            'discount_rate' => $discountRate,
                            'base_price' => $prices->basePrice,
                            'price_before_discount' => $prices->priceBeforeDiscount,
                            'price' => $prices->priceAfterDiscount,
                            'price_after_taxes' => $prices->priceAfterTaxes,
                            'total_discount' => $prices->totalDiscount,
                            'platform_tax' => $prices->totalPlatformTax,
                            'country_tax' => $prices->totalCountryTax,
                            'other_tax' => $prices->totalOtherTax,
                            'total_taxes' => $prices->totalTaxes,
                            'quantity' => rand(10, 100),
                            'min_order_quantity' => rand(1, 5),
                            'stock_qty' => rand(50, 1000),
                            'nearly_out_of_stock_limit' => rand(5, 20),
                            'unit_type' => $unitType,
                            'status' => rand(0, 10) > 2 ? ProductStatus::PUBLISHED->value : ProductStatus::DRAFT->value, // 80% published
                            'is_active' => rand(0, 10) > 1, // 90% active
                            'is_featured' => rand(0, 10) > 7, // 30% featured
                            'category_id' => $category->id,
                            'supplier_id' => $suppliers->random()->id,
                        ]);
                });
            }
        }
    }
}
