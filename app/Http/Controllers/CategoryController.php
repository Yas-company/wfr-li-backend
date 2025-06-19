<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponse;

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isSupplier() || !$user->isApproved()) {
            return $this->errorResponse('Only approved suppliers can add categories.');
        }

        $validated = $request->validate([
            'categories' => 'required|array|min:1',
            'categories.*.name' => 'required|array',
        ]);

        $created = [];
        foreach ($validated['categories'] as $catData) {
            $created[] = Category::create([
                'name' => $catData['name'],
                'supplier_id' => $user->id,
            ]);
        }

        return $this->successResponse(CategoryResource::collection($created), 'Categories created successfully');
    }
}
