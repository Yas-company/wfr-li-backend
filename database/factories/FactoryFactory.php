<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FactoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'phone' => fake()->phoneNumber(),
            'number' => fake()->unique()->numerify('FACT-####'),
            'email' => fake()->unique()->companyEmail(),
            'password' => Hash::make('password'),
        ];
    }
} 