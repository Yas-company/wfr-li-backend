<?php

namespace Database\Seeders;

use App\Enums\Order\OrderStatus;
use App\Enums\Order\OrderType;
use App\Enums\Order\PaymentMethod;
use App\Enums\Order\PaymentStatus;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HighVolumeOrderSeeder extends Seeder
{
	public function run(): void
	{
		DB::disableQueryLog();

		// Read from env to avoid requiring CLI options on db:seed
		$ordersTarget = 3000_000;
		$chunkSize = 8000;
		$minItems = 1;
		$maxItems = 5;

		if ($ordersTarget <= 0) {
			$this->command?->warn('No orders to seed. Set SEED_ORDERS in env to a positive number.');
			return;
		}

		$buyerIds = DB::table('users')
			->where('role', UserRole::BUYER->value)
			->pluck('id')
			->all();

		$supplierIds = DB::table('users')
			->where('role', UserRole::SUPPLIER->value)
			->pluck('id')
			->all();

		$products = DB::table('products')
			->select('id', 'price')
			->get()
			->map(fn ($p) => ['id' => (int) $p->id, 'price' => (float) $p->price])
			->all();

		$addressByUserId = DB::table('addresses')
			->select('user_id', DB::raw('MIN(id) as id'))
			->groupBy('user_id')
			->pluck('id', 'user_id')
			->all();

		if (empty($buyerIds) || empty($supplierIds) || empty($products)) {
			$this->command?->error('Ensure buyers, suppliers, and products exist before running this seeder.');
			return;
		}

		for ($offset = 0; $offset < $ordersTarget; $offset += $chunkSize) {
			$currentChunk = min($chunkSize, $ordersTarget - $offset);
			$now = now();

			$ordersInsert = [];
			$orderMeta = [];

			for ($i = 0; $i < $currentChunk; $i++) {
				$buyerId = $buyerIds[array_rand($buyerIds)];
				$supplierId = $supplierIds[array_rand($supplierIds)];

				$ordersInsert[] = [
					'user_id' => $buyerId,
					'supplier_id' => $supplierId,
                    'order_type' => OrderType::INDIVIDUAL->value,
					// Pick a single status to simplify downstream filters and reduce branching
					'status' => OrderStatus::DELIVERED->value,
					'total' => rand(100, 200),
					'total_products' => rand(10, 20),
					'created_at' => $now,
					'updated_at' => $now,
				];

				$orderMeta[] = [
					'buyer_id' => $buyerId,
					'items_count' => mt_rand($minItems, $maxItems),
				];
			}

			DB::beginTransaction();

			DB::table('orders')->insert($ordersInsert);
			$firstOrderId = (int) DB::getPdo()->lastInsertId();
			$lastOrderId = $firstOrderId + $currentChunk - 1;

			// Build order_details rows (1:1 with orders)
			$orderDetailsInsert = [];
			for ($i = 0; $i < $currentChunk; $i++) {
				$orderId = $firstOrderId + $i;
				$buyerId = $orderMeta[$i]['buyer_id'];
				$orderDetailsInsert[] = [
					'order_id' => $orderId,
					'shipping_address_id' => $addressByUserId[$buyerId] ?? null,
					'payment_status' => PaymentStatus::PAID->value,
					'payment_method' => PaymentMethod::Tap->value,
					'tracking_number' => (string) $orderId,
					'created_at' => $now,
					'updated_at' => $now,
				];
			}
			if (!empty($orderDetailsInsert)) {
				DB::table('order_details')->insert($orderDetailsInsert);
			}

			// Build order_product rows
			$orderProductsInsert = [];
			for ($i = 0; $i < $currentChunk; $i++) {
				$orderId = $firstOrderId + $i;
				$itemsCount = $orderMeta[$i]['items_count'];
				for ($k = 0; $k < $itemsCount; $k++) {
					$prod = $products[array_rand($products)];
					$orderProductsInsert[] = [
						'order_id' => $orderId,
						'product_id' => $prod['id'],
						'quantity' => mt_rand(1, 5),
						'price' => $prod['price'],
						'created_at' => $now,
						'updated_at' => $now,
					];
				}
			}
			if (!empty($orderProductsInsert)) {
				foreach (array_chunk($orderProductsInsert, 10000) as $chunk) {
					DB::table('order_product')->insert($chunk);
				}
			}

			// Aggregate totals into orders (single SQL per chunk)
			DB::statement(
				'UPDATE orders o
					JOIN (
						SELECT order_id, SUM(quantity * price) AS total, SUM(quantity) AS total_products
						FROM order_product
						WHERE order_id BETWEEN ? AND ?
						GROUP BY order_id
					) t ON t.order_id = o.id
				SET o.total = t.total, o.total_products = t.total_products',
				[$firstOrderId, $lastOrderId]
			);

			DB::commit();

			$this->command?->info("Inserted {$currentChunk} orders [" . ($offset + $currentChunk) . "/{$ordersTarget}]");
		}
	}
}


