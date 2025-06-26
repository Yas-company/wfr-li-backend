<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Page;

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

    public function page($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();
        return view('pages.show', compact('page'));
    }
}
