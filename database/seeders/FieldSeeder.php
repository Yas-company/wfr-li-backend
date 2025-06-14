<?php

namespace Database\Seeders;

use App\Models\Field;
use Illuminate\Database\Seeder;

class FieldSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            [
                'id' => 1,
                'name' => [
                    'en' => 'Food Supplies',
                    'ar' => 'مواد غذائية'
                ],
                'image' => 'fields/food.png'
            ],
            [
                'id' => 2,
                'name' => [
                    'en' => 'Building Materials',
                    'ar' => 'مواد بناء'
                ],
                'image' => 'fields/building.png'
            ]
        ];

        foreach ($fields as $field) {
            Field::updateOrCreate(
                ['id' => $field['id']], // Search by ID
                $field // Data to update or create
            );
        }
    }
} 