<?php

namespace Tests\Feature;

use Database\Factories\CategoryFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_active_categories()
    {

        CategoryFactory::new()->count(2)->sequence(
            [
                'name' => ['en' => 'Category 1', 'ar' => 'التصنيف 1'],
                'is_active' => true,
            ],
            [
                'name' => ['en' => 'Category 2', 'ar' => 'التصنيف 2'],
                'is_active' => true,
            ],
        )->create();

        $this->get('/api/categories')
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'image',
                        'is_active',
                    ],
                ],
            ]);
        $this->assertDatabaseCount('categories', 2);
    }

    public function test_get_only_active_categories()
    {

        CategoryFactory::new()->count(2)->sequence(
            [
                'name' => ['en' => 'Category 1', 'ar' => 'التصنيف 1'],
                'is_active' => true,
            ],
            [
                'name' => ['en' => 'Category 2', 'ar' => 'التصنيف 2'],
                'is_active' => false,
            ],
        )->create();

        $this->get('/api/categories')
            ->assertSuccessful()
            ->assertJsonCount(1);
    }

    public function test_get_arabic_categories()
    {
        CategoryFactory::new()->create([
            'name' => ['en' => 'Category 1', 'ar' => 'التصنيف 1'],
            'is_active' => true,
        ]);

        $response = $this->get('/api/categories', [
            'Accept-Language' => 'ar'
        ])
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'image',
                        'is_active'
                    ]
                ]
            ]);

        $this->assertEquals('التصنيف 1', $response->json('data.0.name'));
    }

    public function test_get_english_categories()
    {
        CategoryFactory::new()->create([
            'name' => ['en' => 'Category 1', 'ar' => 'التصنيف 1'],
            'is_active' => true,
        ]);

        $response = $this->get('/api/categories', [
            'Accept-Language' => 'en'
        ])
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'image',
                        'is_active'
                    ]
                ]
            ]);

        $this->assertEquals('Category 1', $response->json('data.0.name'));
    }
}
