<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->orderByDesc('is_featured')
            ->paginate(10);
        return ProductResource::collection($products);
    }

    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function filter(Request $request)
    {
        $products = Product::filterAndSearch($request->all())
            ->with('category')
            ->paginate(10);

        return ProductResource::collection($products);
    }

    public function related(Product $product)
    {
        $related = $product->related();
        return ProductResource::collection($related);
    }
}
