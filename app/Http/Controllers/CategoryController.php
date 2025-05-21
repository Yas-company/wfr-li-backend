<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::active()->paginate(10);
        return CategoryResource::collection($categories);
    }

    public function show(Category $category)
    {
        $products = $category->products()->paginate(10);
        return ProductResource::collection($products);
    }
}
