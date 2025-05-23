<?php

namespace App\Http\Controllers;

use App\Models\Category;

class HomeController extends Controller
{

    public function index()
    {
        $categories = Category::paginate(8);
        return view('welcome', compact('categories'));
    }

    public function categoryProducts(Category $category)
    {
        $products = $category->products()->paginate(12);
        return view('category-products', compact('category', 'products'));
    }
}
