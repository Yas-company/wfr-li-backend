<?php

namespace App\Http\Controllers\api\v1\Buyer;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\Buyer\HomePageBuyerResource;
use App\Models\Category;
use App\Models\Page;
use App\Models\User;
use App\Traits\ApiResponse;

class HomeController extends Controller
{
    use ApiResponse;

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

    public function getSuppliersAndProducts()
    {

        $data = User::role(UserRole::SUPPLIER->value)
            ->with(['products' => function ($query) {
                $query
                    ->latest()
                    ->take(10);
            }])
            ->select('id', 'name', 'image')
            ->latest()
            ->take(4)
            ->get();

        return $this->successResponse(HomePageBuyerResource::collection($data), __('messages.success'));
    }
}
