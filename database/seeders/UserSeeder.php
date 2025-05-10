<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'phone' => '1234567890',
            'country_code' => '966',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        // Create a buyer
        User::create([
            'name' => 'Test Buyer',
            'phone' => '1234567891',
            'country_code' => '966',
            'address' => 'Buyer Address',
            'location' => 'Buyer Location',
            'business_name' => 'Buyer Business',
            'lic_id' => 'BUYER123',
            'email' => 'buyer@gmail.com',
            'password' => Hash::make('password'),
            'role' => UserRole::BUYER,
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);
    }
}
