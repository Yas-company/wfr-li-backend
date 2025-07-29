<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1\Product\Buyer;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $supplier;

    protected User $buyer;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept-Language' => 'en',
        ]);

        Product::factory()->count(3)->create();

        $this->buyer = User::factory()->buyer()->create();
    }


    public function test_buyer_can_view_product_details()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->buyer)->getJson(route('buyer.products.show', $product->id));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'image',
                    'price',
                    'price_before_discount',
                    'quantity',
                    'stock_qty',
                    'nearly_out_of_stock_limit',
                    'status',
                    'is_favorite',
                    'unit_type',
                    'avg_rating',
                ],
            ]);
    }

    public function test_buyer_can_view_all_products()
    {
        $response = $this->actingAs($this->buyer)->getJson(route('buyer.products.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'image',
                        'price',
                        'price_before_discount',
                        'quantity',
                        'stock_qty',
                        'nearly_out_of_stock_limit',
                        'status',
                        'is_favorite',
                        'unit_type',
                        'avg_rating',
                    ],
                ],
                'links' => [
                    'first',
                    'last',
                    'next',
                    'prev',
                ],
            ]);
    }
}
