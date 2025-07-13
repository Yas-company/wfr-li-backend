<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\Rating;
use App\Enums\UserRole;
use App\Models\Product;
use Illuminate\Database\Seeder;
use App\Enums\Morphs\RatingModel;
use App\Enums\Order\OrderStatus;

class RatingSeeder extends Seeder
{
    public function run()
    {
        $buyers = User::where('role', UserRole::BUYER)->get();
        $products = Product::all();

        foreach ($buyers as $customer) {
            $ratedProducts = $products->random(rand(1, 10));

            foreach ($ratedProducts as $product) {
                Rating::factory()->create([
                    'user_id' => $customer->id,
                    'rateable_id' => $product->id,
                    'rateable_type' => RatingModel::PRODUCT,
                ]);
            }
        }

        $suppliers = User::where('role', UserRole::SUPPLIER)->get();

        foreach ($buyers->random(10) as $customer) {
            $ratedSuppliers = $suppliers->random(rand(1, 3));

            foreach ($ratedSuppliers as $supplier) {
                Rating::factory()->create([
                    'user_id' => $customer->id,
                    'rateable_id' => $supplier->id,
                    'rateable_type' => RatingModel::USER,
                ]);
            }
        }

        foreach ($buyers as $customer) {
            $orders = Order::where('user_id', $customer->id)
                ->where('status', OrderStatus::DELIVERED)
                ->get();

            $ordersToRate = $orders->random(rand(
                ceil($orders->count() * 0.3),
                ceil($orders->count() * 0.7)
            ));

            foreach ($ordersToRate as $order) {
                Rating::factory()->create([
                    'user_id' => $customer->id,
                    'rateable_id' => $order->id,
                    'rateable_type' => RatingModel::ORDER,
                    'rating' => rand(3, 5),
                ]);
            }
        }
    }
}
