<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Enums\UserRole;
use App\Models\Address;
use App\Models\Payment;
use App\Models\Product;
use App\Models\OrderDetail;
use App\Models\OrderProduct;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $customers = User::where('role', UserRole::BUYER)->get();
        $products = Product::all();

        foreach ($customers as $customer) {
            // Create 1-5 orders per customer
            $orders = Order::factory()
                ->count(rand(1, 5))
                ->create([
                    'user_id' => $customer->id,
                    'supplier_id' => fake()->randomElement(User::where('role', UserRole::SUPPLIER)->pluck('id')->toArray()),
                ]);

            foreach ($orders as $order) {
                // Create order details
                $address = Address::where('user_id', $customer->id)->inRandomOrder()->first();

                $orderDetail = OrderDetail::factory()->create([
                    'order_id' => $order->id,
                    'shipping_address_id' => $address->id,
                ]);

                // Create payment
                Payment::factory()->create([
                    'user_id' => $customer->id,
                    'amount' => $order->total,
                    'status' => $orderDetail->payment_status,
                    'payment_method' => $orderDetail->payment_method,
                ]);

                // Add 1-10 products to each order
                $orderProducts = $products->random(rand(1, 10));
                $total = 0;

                foreach ($orderProducts as $product) {
                    $quantity = rand(1, 5);
                    $price = $product->price;

                    OrderProduct::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $price,
                    ]);

                    $total += $quantity * $price;
                }

                // Update order total
                $order->update(['total' => $total]);
            }
        }
    }
}
