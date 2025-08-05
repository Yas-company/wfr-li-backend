<?php

namespace App\Services;

use App\Enums\ProductStatus;
use App\Models\User;
use App\Models\Product;
use App\Filters\JsonColumnFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Services\Contracts\ProductServiceInterface;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService implements ProductServiceInterface
{
    /**
     * Display a listing of the resource.
     *
     * @param array $filters
     *
     * @return Paginator
     */
    public function getSupplierProducts(int $supplierId)
    {
        return QueryBuilder::for(Product::class)
                ->allowedFilters([
                    AllowedFilter::exact('category_id'),
                    AllowedFilter::custom('name', new JsonColumnFilter),
                    AllowedFilter::exact('price'),
                ])
                ->allowedSorts([
                    'id',
                    'created_at',
                ])
                ->allowedIncludes([
                    'category',
                    'supplier',
                ])
                ->defaultSort('id')
                ->where('supplier_id', $supplierId)
                ->paginate(10);
    }

        /**
     * Display a listing of the resource.
     *
     * @param array $filters
     *
     * @return Paginator
     */
    public function getProductsForBuyer()
    {
        return QueryBuilder::for(Product::query()->forUsers())
                ->allowedFilters([
                    AllowedFilter::exact('category_id'),
                    AllowedFilter::exact('supplier_id'),
                    AllowedFilter::custom('name', new JsonColumnFilter),
                    'price',
                ])
                ->allowedSorts([
                    'id',
                    'created_at',
                    'price'
                ])
                ->allowedIncludes([
                    'category',
                    'supplier',
                ])
                ->defaultSort('id')
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
    public function store(array $data, User $user)
    {
        $data['supplier_id'] = $user->id;

        return Product::create($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Product $product
     * @param array $data
     *
     * @return Product
     */
    public function update(Product $product, array $data)
    {
        $product->update($data);

        return $product;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Product $product
     */
    public function delete(Product $product)
    {
        return $product->delete();
    }

    /**
     * Display the number of products.
     *
     * @return int
     */
    public function countTotal(User $user): int
    {
        return Product::where('supplier_id', $user->id)->count();
    }

    /**
     * Display the number of expired products.
     *
     * @return int
     */
    public function countExpired(User $user): int
    {
        return Product::where('supplier_id', $user->id)->where('stock_qty', 0)->count();
    }

    /**
     * Display the number of near expiry products.
     *
     * @return int
     *
     */
    public function countNearExpiry(User $user): int
    {
        return Product::where('supplier_id', $user->id)->whereColumn('stock_qty', '<', 'nearly_out_of_stock_limit')->count();
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

    /**
     * Get the available products.
     *
     */
    public function getAvailableProducts(int $supplierId): LengthAwarePaginator
    {
        return Product::where('supplier_id', $supplierId)
        ->isActive()
        ->whereColumn('stock_qty', '>', 'nearly_out_of_stock_limit')
        ->with(['media', 'favorites','ratings', 'category', 'category.field', 'ratings.user'])
        ->paginate(10);
    }

    /**
     * Get the  nearly out of stock products.
     *
     */
    public function getNearlyOutOfStockProducts(int $supplierId): LengthAwarePaginator
    {
        return Product::where('supplier_id', $supplierId)
            ->isActive()
            ->whereColumn('stock_qty', '<=', 'nearly_out_of_stock_limit')
            ->where('stock_qty', '>', 0)
            ->with(['media', 'favorites','ratings', 'category', 'category.field', 'ratings.user'])
            ->paginate(10);
    }

    /**
     * Get the out of stock products.
     */
    public function getOutOfStockProducts(int $supplierId): LengthAwarePaginator
    {
        return Product::where('supplier_id', $supplierId)
            ->isActive()
            ->where('stock_qty', '<=', 0)
            ->with(['media', 'favorites','ratings', 'category', 'category.field', 'ratings.user'])
            ->paginate(10);
    }

    /**
     * Get the similar products.
     */
    public function getSimilarProducts(Product $product)
    {
        $products = Product::where('category_id', $product->category_id)
            ->published()
            ->where('id', '!=', $product->id)
            ->isActive()
            ->with(['media', 'currentUserFavorite', 'category', 'category.field', 'ratings', 'ratings.user'])
            ->take(5)
            ->latest()
            ->get();

        if ($products->count() == 0) {
            $products = Product::where('supplier_id', $product->supplier_id)
                ->published()
                ->where('id', '!=', $product->id)
                ->isActive()
                ->with(['media', 'currentUserFavorite', 'ratings', 'category', 'category.field', 'supplier'])
                ->take(5)
                ->latest()
                ->get();
        }

        return $products;
    }
}
