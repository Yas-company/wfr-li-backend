<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserRole;
use App\Enums\UserStatus;

class ApprovedUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'phone' => '1234567800',
            'country_code' => '966',
            'email' => 'admin1@gmail.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
            'is_verified' => true,
            'email_verified_at' => now(),
            'status' => UserStatus::APPROVED,
        ]);

        // Create buyer user
        User::create([
            'name' => 'Buyer User',
            'phone' => '1234567802',    
            'country_code' => '966',
            'email' => 'buyer1@gmail.com',
            'password' => Hash::make('password'),
            'role' => UserRole::BUYER,
            'is_verified' => true,
            'email_verified_at' => now(),
            'status' => UserStatus::APPROVED,
        ]);


        // Create supplier user
        User::create([
            'name' => 'Supplier User',
            'phone' => '1234567801',
            'country_code' => '966',
            'email' => 'supplier1@gmail.com',
            'password' => Hash::make('password'),
            'role' => UserRole::SUPPLIER,
            'is_verified' => true,
            'email_verified_at' => now(),   
            'status' => UserStatus::APPROVED,
        ]);
    }
}
