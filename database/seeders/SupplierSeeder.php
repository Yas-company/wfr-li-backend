<?php

namespace Database\Seeders;

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
        $suppliers = [
            [
                'name' => [
                    'en' => 'Supplier One',
                    'ar' => 'المورد الأول'
                ],
                'phone' => '9876543210',
                'address' => 'الرياض',
                'latitude' => 24.7136,
                'longitude' => 46.6753,
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
                'address' => 'الرياض',
                'latitude' => 24.7136,
                'longitude' => 46.6753,
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
                'address' => 'الرياض',
                'latitude' => 24.7136,
                'longitude' => 46.6753,
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
                'address' => 'الرياض',
                'latitude' => 24.7136,
                'longitude' => 46.6753,
                'email' => 'supplier4@test.com',
                'password' => Hash::make('password'),
                'is_verified' => true,
            ],
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::updateOrCreate(
                ['email' => $supplierData['email']],
                $supplierData
            );
        }
    }
}
