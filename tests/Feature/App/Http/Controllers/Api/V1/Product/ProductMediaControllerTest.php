<?php

namespace Tests\Feature\Api\V1\Product;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductMediaControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $product;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withHeaders([
            'Accept-Language' => 'en',
        ]);

        Storage::fake('media');

        $this->user = User::factory()->create([
            'role' => UserRole::SUPPLIER,
        ]);

        $this->category = Category::factory()->create([
            'supplier_id' => $this->user->id,
        ]);

        $this->product = Product::factory()->create([
            'supplier_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);
    }

    public function test_supplier_can_attach_multiple_images_to_product()
    {
        $images = [
            UploadedFile::fake()->image('product1.jpg', 100, 100),
            UploadedFile::fake()->image('product2.png', 200, 200),
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('products.attach-media', $this->product), [
                'images' => $images,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'images' => [
                        '*' => ['id', 'name', 'original', 'thumb', 'preview'],
                    ],
                ],
                'message',
            ])
            ->assertJson(['message' => 'Product updated successfully']);

        $this->assertCount(2, $this->product->fresh()->getMedia('images'));

        foreach ($this->product->getMedia('images') as $index => $media) {
            $this->assertStringContainsString("products/{$media->id}/", $media->getPath());
            $this->assertTrue(Storage::disk('media')->exists("products/{$media->id}/{$images[$index]->getClientOriginalName()}"));
        }
    }


    public function test_supplier_cannot_attach_images_without_authentication()
    {
        $images = [UploadedFile::fake()->image('product.jpg')];

        $response = $this->postJson(route('products.attach-media', $this->product), [
            'images' => $images,
        ]);

        $response->assertStatus(401);
        $this->assertCount(0, $this->product->fresh()->getMedia('images'));
    }

    public function test_supplier_cannot_attach_empty_image_array()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('products.attach-media', $this->product), [
                'images' => [],
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['images' => 'You must have an image for the product']);

        $this->assertCount(0, $this->product->fresh()->getMedia('images'));
    }

    public function test_supplier_cannot_attach_more_than_five_images()
    {
        $images = [
            UploadedFile::fake()->image('product1.jpg'),
            UploadedFile::fake()->image('product2.jpg'),
            UploadedFile::fake()->image('product3.jpg'),
            UploadedFile::fake()->image('product4.jpg'),
            UploadedFile::fake()->image('product5.jpg'),
            UploadedFile::fake()->image('product6.jpg'),
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('products.attach-media', $this->product), [
                'images' => $images,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['images' => 'You must attach at most 5 images']);

        $this->assertCount(0, $this->product->fresh()->getMedia('images'));
    }


    public function test_supplier_cannot_attach_non_image_files()
    {
        $images = [UploadedFile::fake()->create('document.pdf', 100)];

        $response = $this->actingAs($this->user)
            ->postJson(route('products.attach-media', $this->product), [
                'images' => $images,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                "images.0"=> [
                    "The images.0 field must be an image.",
                    "The images.0 field must be a file of type: jpeg, png, jpg, gif, webp."
                ]
            ]);

        $this->assertCount(0, $this->product->fresh()->getMedia('images'));
    }

    public function test_supplier_cannot_attach_images_larger_than_2mb()
    {
        $images = [UploadedFile::fake()->image('product.jpg')->size(3000)];

        $response = $this->actingAs($this->user)
            ->postJson(route('products.attach-media', $this->product), [
                'images' => $images,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['images.0' => 'The images.0 field must not be greater than 2048 kilobytes.']);

        $this->assertCount(0, $this->product->fresh()->getMedia('images'));
    }

    public function test_supplier_cannot_attach_images_to_non_existent_product()
    {
        $images = [UploadedFile::fake()->image('product.jpg')];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson(route('products.attach-media', 999), [
                'images' => $images,
            ]);

        $response->assertStatus(404);
    }

    public function test_supplier_can_delete_media_from_product()
    {
        $images = [
            UploadedFile::fake()->image('product1.jpg', 100, 100),
            UploadedFile::fake()->image('product2.png', 200, 200),
        ];

        $this->actingAs($this->user)
            ->postJson(route('products.attach-media', $this->product), [
                'images' => $images,
            ]);

        $media1 = $this->product->getFirstMedia('images');
        $media2 = $this->product->getLastMedia('images');

        $response = $this->actingAs($this->user)
            ->deleteJson(route('products.media.destroy', [$this->product, $media1]));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Media deleted successfully']);

        $this->assertCount(1, $this->product->fresh()->getMedia('images'));
        $this->assertFalse(Storage::disk('media')->exists("products/{$media1->id}/product1.jpg"));
        $this->assertTrue(Storage::disk('media')->exists("products/{$media2->id}/product2.png"));
    }

    public function test_supplier_cannot_delete_non_existent_media()
    {
        $response = $this->actingAs($this->user)
            ->deleteJson(route('products.media.destroy', [$this->product, 999]));

        $response->assertStatus(404);
    }

    public function test_supplier_cannot_delete_media_from_non_existent_product()
    {
        $images = [
            UploadedFile::fake()->image('product1.jpg', 100, 100),
            UploadedFile::fake()->image('product2.png', 200, 200),
        ];

        $this->actingAs($this->user)
            ->postJson(route('products.attach-media', $this->product), [
                'images' => $images,
            ]);

        $media = $this->product->getFirstMedia('images');

        $response = $this->actingAs($this->user)
            ->deleteJson(route('products.media.destroy', [999, $media->id]));

        $response->assertStatus(404);
    }
}
