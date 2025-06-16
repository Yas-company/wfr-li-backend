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
                    'en' => 'Food & Groceries',
                    'ar' => 'مواد غذائية'
                ],
            ],
            [
                'id' => 2,
                'name' => [
                    'en' => 'Building Materials',
                    'ar' => 'مواد بناء'
                ],
            ]
        ];

        foreach ($fields as $field) {
            Field::updateOrCreate(
                ['id' => $field['id']],
                $field
            );
        }
    }
} 