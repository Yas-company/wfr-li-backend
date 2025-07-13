<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $categories = Category::all();
        $suppliers = User::where('role', UserRole::SUPPLIER)->get();

        foreach ($categories as $category) {
            Product::factory()
                ->count(rand(5, 15))
                ->create([
                    'category_id' => $category->id,
                    'supplier_id' => $suppliers->random()->id,
                ]);
        }
    }
}

