<?php

namespace App\Services;

use App\Models\User;
use App\Models\Product;
use App\Services\Contracts\ProductServiceInterface;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductService implements ProductServiceInterface
{
    /**
     * Display a listing of the resource.
     *
     * @param array $filters
     *
     * @return Paginator
     */
    public function list(array $filters)
    {
        return Product::query()
            ->when(isset($filters['category_id']), function ($query) use ($filters) {
                $query->where('category_id', $filters['category_id']);
            })
            ->latest()
            ->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param array $data
     * @param User $user
     *
     * @return Product
     */
    public function create(array $data, User $user)
    {
        $data['supplier_id'] = $user->id;

        return Product::create($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @param array $data
     *
     * @return Product
     */
    public function update(int $id, array $data)
    {
        $product = Product::findOrFail($id);
        $product->update($data);

        return $product;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     */
    public function delete(int $id)
    {
        $product = Product::findOrFail($id);
        return $product->delete();
    }

    /**
     * Display the number of products.
     *
     * @return int
     */
    public function countTotal(): int
    {
        return Product::count();
    }

    /**
     * Display the number of expired products.
     *
     * @return int
     */
    public function countExpired(): int
    {
        return Product::where('stock_qty', 0)->count();
    }

    /**
     * Display the number of near expiry products.
     *
     * @return int
     *
     */
    public function countNearExpiry(): int
    {
        return Product::where('stock_qty', '<', 5)->count(); // مثال: أقل من 5
    }

    /**
     * Display the stock status counts.
     *
     * @return array
     */
    public function countStockStatuses(): array
    {
        return [
            'expired' => Product::where('stock_qty', 0)->count(),
            'near_expiry' => Product::where('stock_qty', '<', 5)->where('stock_qty', '>', 0)->count(),
        ];
    }

    /**
     * Attach media to the product.
     *
     * @param Product $product
     *
     * @return Product
     */
    public function attachMedia(Product $product)
    {
        $product->addMultipleMediaFromRequest(['images'])
            ->each(function ($fileAdder) {
                $fileAdder->sanitizingFileName(function($fileName) {
                            return strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName));
                        })
                    ->toMediaCollection('images');
            });

        return $product;
    }

    /**
     * Remove media from the product.
     *
     * @param Product $product
     * @param Media $media
     *
     * @return Product
     */
    public function removeMedia(Product $product, Media $media)
    {
        $media->delete();
        return $product;
    }
}
