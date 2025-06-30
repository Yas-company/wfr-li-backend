<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Requests\SearchCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Services\CategoryService;
use App\Models\Category;
use App\Traits\ApiResponse;


class CategoryController extends Controller
{
    use ApiResponse;
    public $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the supplier's categories.
     */
    public function index()
    {
        $result = $this->categoryService->index();

        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 500);
        }

        // Return the resource collection directly for proper pagination structure
        return $this->paginatedResponse($result, CategoryResource::collection($result), 'Categories retrieved successfully', 200);
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category)
    {
        $result = $this->categoryService->show($category);

        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 500);
        }

        return $this->successResponse( new CategoryResource($result), 'Category retrieved successfully', 200);

    }

    /**
     * Store a newly created category in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $result = $this->categoryService->storeCategory($request);

        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 500);
        }

        return $this->successResponse( new CategoryResource($result), 'Category created successfully', 200);

    }

    /**
     * Update the specified category in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $result = $this->categoryService->update($request, $category);

        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 500);
        }

        return $this->successResponse( new CategoryResource($result), 'Category updated successfully', 200);

    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        $result = $this->categoryService->destroy($category);

        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 500);
        }

        return $this->successResponse( null, 'Category deleted successfully', 200);

    }

    public function getCategoriesByField(int $field_id)
    {
        $result = $this->categoryService->getCategoriesByField($field_id);
        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 500);
        }

        return $this->successResponse( CategoryResource::collection($result), 'Categories retrieved successfully', 200);
    }

    public function search(SearchCategoryRequest $request)
    {
        $result = $this->categoryService->search($request);
        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 500);
        }

        return $this->paginatedResponse($result, CategoryResource::collection($result), 'Categories retrieved successfully', 200);
    }

    /**
     * Check if user can manage categories.
     */
    // private function canManageCategories($user): bool
    // {
    //     return $user && $user->isSupplier() && $user->isApproved();
    // }

    /**
     * Check if user owns the category.
     */
//     private function ownsCategory($user, Category $category): bool
//     {
//         return $category->supplier_id === $user->id;
//     }
}
