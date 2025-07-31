<?php

namespace App\Imports\Excel;

use App\Enums\UnitType;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Enums\ProductStatus;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class ProductImport implements ToModel, WithHeadingRow, WithProgressBar
{

    use Importable;

    public function __construct(protected int $supplierId, protected string $dir)
    {
        //
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        if(empty($row['name_en']) || empty($row['name_ar'])) {
            return null;
        }

        $imagePath = storage_path('imports/'.$this->dir.'/images/'.$row['image']);

        if(!file_exists($imagePath)) {
            return null;
        }

        $price = rand(100, 1000);
        $priceBeforeDiscount = $price;

        if(rand(1, 10) <= 3) {
            $priceBeforeDiscount = $price + rand(0, 100);
        }

        $quantity = (int) $row['quantity'];
        $minOrderQuantity = rand(1, $quantity);
        $nearlyOutOfStockLimit = min(5, $quantity);
        $categoryArName = $row['category_name_ar'];
        $categoryEnName = $row['category_name_en'];

        $category = Category::where('name->ar', $categoryArName)->orWhere('name->en', $categoryEnName)->first();

        if(!$category) {
            $category = Category::create([
                'name' => [
                    'ar' => $categoryArName,
                    'en' => $categoryEnName,
                ],
            ]);
        }

        $product = new Product([
            'name' => [
                'en' => $row['name_en'],
                'ar' => $row['name_ar'],
            ],
            'description' => [
                'en' => $row['description_en'] ?? Str::random(16),
                'ar' => $row['description_ar'] ?? Str::random(16),
            ],
            'price' => $price,
            'price_before_discount' => $priceBeforeDiscount,
            'quantity' => $quantity,
            'min_order_quantity' => $minOrderQuantity,
            'stock_qty' => $quantity,
            'nearly_out_of_stock_limit' => $nearlyOutOfStockLimit,
            'unit_type' => UnitType::tryFrom($row['unit']) ?? UnitType::tryFrom(0),
            'status' => ProductStatus::PUBLISHED,
            'is_active' => true,
            'category_id' => $category->id,
            'supplier_id' => $this->supplierId,
        ]);


        $product->addMedia($imagePath)->preservingOriginal()->toMediaCollection('images');

        return $product;
    }
}
