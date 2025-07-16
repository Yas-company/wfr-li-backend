<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users with supplier role
        $supplierUsers = User::where('role', UserRole::SUPPLIER->value)->get();

        // Create supplier records for each supplier user
        foreach ($supplierUsers as $user) {
            Supplier::create([
                'user_id' => $user->id,
                'status' => true, // Default active status
            ]);
        }

        $this->command->info('Created '.$supplierUsers->count().' supplier records.');
    }
}
