<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use Database\Factories\CategoryFactory;
use Database\Factories\ProductFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_products()
    {
        // Create categories first
        $category = CategoryFactory::new()->create([
            'name' => [
                'en' => 'Vegetables',
                'ar' => 'خضروات'
            ],
            'is_active' => true
        ]);

        ProductFactory::new()->count(3)->create([
            'category_id' => $category->id
        ]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->dump()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'image',
                        'price',
                        'stock_qty',
                        'category' => [
                            'id',
                            'name',
                            'is_active'
                        ],
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);

        // Assert pagination
        $this->assertEquals(3, count($response->json('data')));
    }

    public function test_returns_paginated_products()
    {
        $category = CategoryFactory::new()->create([
            'name' => [
                'en' => 'Vegetables',
                'ar' => 'خضروات'
            ],
            'is_active' => true
        ]);

        ProductFactory::new()->count(15)->create([
            'category_id' => $category->id
        ]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'image',
                        'price',
                        'stock_qty',
                        'category' => [
                            'id',
                            'name',
                            'is_active'
                        ],
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);

        // Assert pagination
        $this->assertEquals(10, count($response->json('data')));
        $this->assertEquals(1, $response->json('meta.current_page'));
        $this->assertEquals(2, $response->json('meta.last_page'));
    }
}
