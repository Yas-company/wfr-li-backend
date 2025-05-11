<?php

namespace Database\Seeders;

use App\Models\Factory;
use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $factory = Factory::first();

        if ($factory) {
            $suppliers = [
                [
                    'name' => 'Supplier One',
                    'phone' => '9876543210',
                    'address' => '123 Test Street, Test City',
                    'location' => 'Test City',
                    'email' => 'supplier1@test.com',
                    'password' => Hash::make('password'),
                    'is_verified' => true,
                ],
                [
                    'name' => 'Supplier Two',
                    'phone' => '9876543211',
                    'address' => '456 Test Avenue, Test City',
                    'location' => 'Test City',
                    'email' => 'supplier2@test.com',
                    'password' => Hash::make('password'),
                    'is_verified' => true,
                ],
                [
                    'name' => 'Supplier Three',
                    'phone' => '9876543212',
                    'address' => '789 Test Road, Test City',
                    'location' => 'Test City',
                    'email' => 'supplier3@test.com',
                    'password' => Hash::make('password'),
                    'is_verified' => true,
                ],
                [
                    'name' => 'Supplier Four',
                    'phone' => '9876543213',
                    'address' => '321 Test Lane, Test City',
                    'location' => 'Test City',
                    'email' => 'supplier4@test.com',
                    'password' => Hash::make('password'),
                    'is_verified' => true,
                ],
            ];

            foreach ($suppliers as $supplierData) {
                Supplier::create([
                    ...$supplierData,
                    'factory_id' => $factory->id,
                ]);
            }
        }
    }
}
