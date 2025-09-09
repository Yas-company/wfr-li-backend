<?php

namespace App\Services;

use App\Models\Favorite;
use App\Models\Product;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class FavoriteService
{
    public function toggleFavorite(array $data, User $user): Favorite
    {
        $productId = $data['product_id'];
        $product = Product::find($productId);

        $favorite = Favorite::updateOrCreate(
            ['user_id' => $user->id, 'product_id' => $productId],
            ['is_favorite' => $data['is_favorite']]
        );

        return $favorite;
    }

    public function getFavorites(User $user): LengthAwarePaginator
    {

        $favoriteProductIds = Favorite::where('user_id', $user->id)
            ->isFavorite()
            ->pluck('product_id');

        $products = Product::whereIn('id', $favoriteProductIds)
            ->isActive()
            ->with(['category', 'ratings', 'category.field', 'ratings.user', 'media'])
            ->latest()
            ->paginate(10);

        return $products;
    }
}
