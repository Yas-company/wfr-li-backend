<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Field;
use App\Enums\UserRole;
use App\Models\Address;
use App\Models\Supplier;
use Illuminate\Database\Seeder;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        $customers = User::factory()
            ->count(25)
            ->buyer()
            ->create();


        $suppliers = User::factory()
            ->count(10)
            ->supplier()
            ->create();

        $fields = Field::all();

        foreach ($suppliers as $supplier) {
            Supplier::create([
                'user_id' => $supplier->id,
                'status' => true, // Default active status
            ]);
            $supplier->fields()->sync($fields);
        }

        foreach ($customers as $customer) {
            Address::factory()
                ->count(rand(1, 3))
                ->create(['user_id' => $customer->id]);
        }
    }
}
