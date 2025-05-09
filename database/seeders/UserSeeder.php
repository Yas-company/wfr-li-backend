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
            'address' => 'Admin Address',
            'location' => 'Admin Location',
            'business_name' => 'Admin Business',
            'lic_id' => 'ADMIN123',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        // // Create a buyer
        // User::create([
        //     'name' => 'Test Buyer',
        //     'phone' => '1234567891',
        //     'address' => 'Buyer Address',
        //     'location' => 'Buyer Location',
        //     'business_name' => 'Buyer Business',
        //     'lic_id' => 'BUYER123',
        //     'email' => 'buyer@gmail.com',
        //     'password' => Hash::make('password'),
        //     'role' => UserRole::BUYER,
        //     'is_verified' => true,
        //     'email_verified_at' => now(),
        // ]);

        // // Create a supplier
        // User::create([
        //     'name' => 'Test Supplier',
        //     'phone' => '1234567892',
        //     'address' => 'Supplier Address',
        //     'location' => 'Supplier Location',
        //     'business_name' => 'Supplier Business',
        //     'lic_id' => 'SUPPLIER123',
        //     'email' => 'supplier@gmail.com',
        //     'password' => Hash::make('password'),
        //     'role' => UserRole::SUPPLIER,
        //     'is_verified' => true,
        //     'email_verified_at' => now(),
        // ]);

        // // Create a factory
        // User::create([
        //     'name' => 'Test Factory',
        //     'phone' => '1234567893',
        //     'address' => 'Factory Address',
        //     'location' => 'Factory Location',
        //     'business_name' => 'Factory Business',
        //     'lic_id' => 'FACTORY123',
        //     'email' => 'factory@gmail.com',
        //     'password' => Hash::make('password'),
        //     'role' => UserRole::FACTORY,
        //     'is_verified' => true,
        //     'email_verified_at' => now(),
        // ]);

        // // Create an unverified buyer
        // User::create([
        //     'name' => 'Unverified Buyer',
        //     'phone' => '1234567894',
        //     'address' => 'Unverified Address',
        //     'location' => 'Unverified Location',
        //     'business_name' => 'Unverified Business',
        //     'lic_id' => 'UNVERIFIED123',
        //     'email' => 'unverified@gmail.com',
        //     'password' => Hash::make('password'),
        //     'role' => UserRole::BUYER,
        //     'is_verified' => false,
        // ]);
    }
}
