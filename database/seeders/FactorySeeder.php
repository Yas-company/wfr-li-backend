<?php

namespace Database\Seeders;

use App\Models\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FactorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Factory::create([
            'name' => 'Test Factory',
            'phone' => '1234567890',
            'number' => 'FACT-0001',
            'email' => 'factory@test.com',
            'password' => Hash::make('password'),
        ]);
    }
}
