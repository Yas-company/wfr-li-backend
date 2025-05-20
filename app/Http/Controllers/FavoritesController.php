<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;

class FavoritesController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $favorites = $user->favoriteProducts()->with('category')->paginate(10);
        return ProductResource::collection($favorites);
    }

    public function store(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        $user = $request->user();
        $user->favoriteProducts()->syncWithoutDetaching([$request->product_id]);
        return response()->json(['message' => __('messages.added_to_favorites')]);
    }

    public function destroy(Request $request, Product $product)
    {
        $user = $request->user();
        $user->favoriteProducts()->detach($product->id);
        return response()->json(['message' => __('messages.removed_from_favorites')]);
    }
} 