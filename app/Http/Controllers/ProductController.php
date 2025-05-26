<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->paginate(10);
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