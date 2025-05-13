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
                    'name' => [
                        'en' => 'Supplier One',
                        'ar' => 'المورد الأول'
                    ],
                    'phone' => '9876543210',
                    'address' => [
                        'en' => '123 Test Street, Test City',
                        'ar' => '123 شارع التجربة، مدينة التجربة'
                    ],
                    'location' => 'Test City',
                    'email' => 'supplier1@test.com',
                    'password' => Hash::make('password'),
                    'is_verified' => true,
                ],
                [
                    'name' => [
                        'en' => 'Supplier Two',
                        'ar' => 'المورد الثاني'
                    ],
                    'phone' => '9876543211',
                    'address' => [
                        'en' => '456 Test Avenue, Test City',
                        'ar' => '456 شارع التجربة، مدينة التجربة'
                    ],
                    'location' => 'Test City',
                    'email' => 'supplier2@test.com',
                    'password' => Hash::make('password'),
                    'is_verified' => true,
                ],
                [
                    'name' => [
                        'en' => 'Supplier Three',
                        'ar' => 'المورد الثالث'
                    ],
                    'phone' => '9876543212',
                    'address' => [
                        'en' => '789 Test Road, Test City',
                        'ar' => '789 شارع التجربة، مدينة التجربة'
                    ],
                    'location' => 'Test City',
                    'email' => 'supplier3@test.com',
                    'password' => Hash::make('password'),
                    'is_verified' => true,
                ],
                [
                    'name' => [
                        'en' => 'Supplier Four',
                        'ar' => 'المورد الرابع'
                    ],
                    'phone' => '9876543213',
                    'address' => [
                        'en' => '321 Test Lane, Test City',
                        'ar' => '321 شارع التجربة، مدينة التجربة'
                    ],
                    'location' => 'Test City',
                    'email' => 'supplier4@test.com',
                    'password' => Hash::make('password'),
                    'is_verified' => true,
                ],
            ];

            foreach ($suppliers as $supplierData) {
                Supplier::updateOrCreate(
                    ['email' => $supplierData['email']],
                    [
                        'name' => $supplierData['name'],
                        'phone' => $supplierData['phone'],
                        'address' => $supplierData['address'],
                        'location' => $supplierData['location'],
                        'password' => $supplierData['password'],
                        'is_verified' => $supplierData['is_verified'],
                        'factory_id' => $factory->id,
                    ]
                );
            }
        }
    }
}
