<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::active()->get();
        return CategoryResource::collection($categories);
    }

    public function show(Category $category)
    {
        $category->load('products');
        return new CategoryResource($category);
    }
}
