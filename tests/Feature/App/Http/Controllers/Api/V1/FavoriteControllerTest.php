<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FavoriteControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected User $otherUser;

    protected User $supplier;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept-Language' => 'en',
        ]);

        $this->user = User::factory()->buyer()->create();
        $this->otherUser = User::factory()->create();
        $this->supplier = User::factory()->supplier()->create();
        $this->category = Category::factory()->create();
    }

    public function test_user_can_add_product_to_favorites()
    {
        $product = Product::factory()->create(['supplier_id' => $this->supplier->id, 'category_id' => $this->category->id, 'is_active' => true]);

        $response = $this->actingAs($this->user)->postJson(route('favorite.toggle'), [
            'product_id' => $product->id,
            'is_favorite' => true,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_user_can_remove_product_from_favorites()
    {
        $product = Product::factory()->create(['supplier_id' => $this->supplier->id, 'category_id' => $this->category->id, 'is_active' => true]);

        // First add to favorites
        $this->actingAs($this->user)->postJson(route('favorite.toggle'), [
            'product_id' => $product->id,
            'is_favorite' => true,
        ]);

        // Then remove from favorites
        $response = $this->actingAs($this->user)->postJson(route('favorite.toggle'), [
            'product_id' => $product->id,
            'is_favorite' => false,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'is_favorite' => false,
        ]);
    }

    public function test_cannot_favorite_inactive_product()
    {
        $product = Product::factory()->create(['supplier_id' => $this->supplier->id, 'category_id' => $this->category->id, 'is_active' => false]);

        $response = $this->actingAs($this->user)->postJson(route('favorite.toggle'), [
            'product_id' => $product->id,
            'is_favorite' => true,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['product_id']);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $this->user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_cannot_favorite_nonexistent_product()
    {
        $response = $this->actingAs($this->user)->postJson(route('favorite.toggle'), [
            'product_id' => 99999,
            'is_favorite' => true,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['product_id']);
    }

    public function test_product_id_is_required()
    {
        $response = $this->actingAs($this->user)->postJson(route('favorite.toggle'), [
            'is_favorite' => true,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['product_id']);
    }

    public function test_is_favorite_is_required()
    {
        $product = Product::factory()->create(['supplier_id' => $this->supplier->id, 'category_id' => $this->category->id, 'is_active' => true]);

        $response = $this->actingAs($this->user)->postJson(route('favorite.toggle'), [
            'product_id' => $product->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['is_favorite']);
    }

    public function test_is_favorite_must_be_boolean()
    {
        $product = Product::factory()->create(['supplier_id' => $this->supplier->id, 'category_id' => $this->category->id, 'is_active' => true]);

        $response = $this->actingAs($this->user)->postJson(route('favorite.toggle'), [
            'product_id' => $product->id,
            'is_favorite' => 'invalid',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['is_favorite']);
    }

    public function test_unauthenticated_user_cannot_toggle_favorites()
    {
        $product = Product::factory()->create(['supplier_id' => $this->supplier->id, 'category_id' => $this->category->id, 'is_active' => true]);

        $response = $this->postJson(route('favorite.toggle'), [
            'product_id' => $product->id,
            'is_favorite' => true,
        ]);

        $response->assertStatus(401);
    }

    public function test_user_can_toggle_same_product_multiple_times()
    {
        $product = Product::factory()->create(['supplier_id' => $this->supplier->id, 'category_id' => $this->category->id, 'is_active' => true]);

        // Add to favorites
        $response1 = $this->actingAs($this->user)->postJson(route('favorite.toggle'), [
            'product_id' => $product->id,
            'is_favorite' => true,
        ]);

        $response1->assertStatus(200);
        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->user->id,
            'product_id' => $product->id,
        ]);

        // Remove from favorites
        $response2 = $this->actingAs($this->user)->postJson(route('favorite.toggle'), [
            'product_id' => $product->id,
            'is_favorite' => false,
        ]);

        $response2->assertStatus(200);
        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'is_favorite' => false,
        ]);

        // Add to favorites again
        $response3 = $this->actingAs($this->user)->postJson(route('favorite.toggle'), [
            'product_id' => $product->id,
            'is_favorite' => true,
        ]);

        $response3->assertStatus(200);
        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_different_users_can_favorite_same_product()
    {
        $product = Product::factory()->create(['supplier_id' => $this->supplier->id, 'category_id' => $this->category->id, 'is_active' => true]);

        // First user adds to favorites
        $response1 = $this->actingAs($this->user)->postJson(route('favorite.toggle'), [
            'product_id' => $product->id,
            'is_favorite' => true,
        ]);

        // Second user adds to favorites
        $response2 = $this->actingAs($this->otherUser)->postJson(route('favorite.toggle'), [
            'product_id' => $product->id,
            'is_favorite' => true,
        ]);

        $response1->assertStatus(200);
        $response2->assertStatus(200);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->user->id,
            'product_id' => $product->id,
        ]);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->otherUser->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_adding_already_favorited_product_returns_success()
    {
        $product = Product::factory()->create(['supplier_id' => $this->supplier->id, 'category_id' => $this->category->id, 'is_active' => true]);

        // Add to favorites first time
        $this->actingAs($this->user)->postJson(route('favorite.toggle'), [
            'product_id' => $product->id,
            'is_favorite' => true,
        ]);

        // Try to add to favorites again
        $response = $this->actingAs($this->user)->postJson(route('favorite.toggle'), [
            'product_id' => $product->id,
            'is_favorite' => true,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        // Should still exist in database
        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_removing_non_favorited_product_returns_success()
    {
        $product = Product::factory()->create(['supplier_id' => $this->supplier->id, 'category_id' => $this->category->id, 'is_active' => true]);

        // Try to remove from favorites without adding first
        $response = $this->actingAs($this->user)->postJson(route('favorite.toggle'), [
            'product_id' => $product->id,
            'is_favorite' => false,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        // Should exist in database with is_favorite = false
        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'is_favorite' => false,
        ]);
    }
}
