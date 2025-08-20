<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Lang;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_requires_authentication()
    {
        $response = $this->getJson('/api/v1/pages/privacy-policy');
        $response->assertStatus(401);
    }

    public function test_show_page_returns_page_data_when_authenticated()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Page::create([
            'slug' => 'privacy-policy',
            'title' => [
                'en' => 'Privacy Policy',
                'ar' => 'سياسة الخصوصية',
            ],
            'content' => [
                'en' => '<h2>Privacy Policy</h2>',
                'ar' => '<h2>سياسة الخصوصية</h2>',
            ],
            'is_active' => true,
        ]);

        // Act
        $response = $this->withHeaders(['Accept-Language' => 'en'])
            ->getJson('/api/v1/pages/privacy-policy');

        // Assert
        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'title', 'slug', 'content'],
            ])
            ->assertJsonPath('data.slug', 'privacy-policy');

        $this->assertSame(
            Lang::get('messages.page.fetched_successfully', [], 'en'),
            $response->json('message')
        );
    }

    public function test_show_page_returns_not_found_for_unknown_slug()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Act
        $response = $this->withHeaders(['Accept-Language' => 'en'])
            ->getJson('/api/v1/pages/unknown-slug');

        // Assert
        $response->assertStatus(404);
    }
}