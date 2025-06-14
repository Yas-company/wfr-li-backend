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

        // Create buyer user
        User::create([
            'name' => 'Buyer User',
            'phone' => '1234567891',
            'country_code' => '966',
            'email' => 'buyer@gmail.com',
            'password' => Hash::make('password'),
            'role' => UserRole::BUYER,
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);


        // Create supplier user
        User::create([
            'name' => 'Supplier User',
            'phone' => '1234567892',
            'country_code' => '966',
            'email' => 'supplier@gmail.com',
            'password' => Hash::make('password'),
            'role' => UserRole::SUPPLIER,
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);
    }
}
