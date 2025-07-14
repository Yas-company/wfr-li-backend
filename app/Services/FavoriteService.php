<?php

namespace App\Services;

use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class FavoriteService
{
    public function toggleFavorite(array $data)
    {
        $user = auth()->user();
        $productId = $data['product_id'];
        $product = Product::find($productId);
        if (!$product->is_active) {
            return ["error" => "Product is not active"];
        }
        $favorite = Favorite::updateOrCreate(
            ['user_id' => $user->id, 'product_id' => $productId],
                ['is_favorite' => $data['is_favorite']]
        );
        return $favorite;
    }

    public function getFavorites(): LengthAwarePaginator
    {
        $user = auth()->user();
        
        $favoriteProductIds = Favorite::where('user_id', $user->id)
            ->where('is_favorite', true)
            ->pluck('product_id');
            
        $products = Product::whereIn('id', $favoriteProductIds)
            ->where('is_active', true)
            ->with(['category', 'ratings'])
            ->latest()
            ->paginate(10);
            
        return $products;
    }
}