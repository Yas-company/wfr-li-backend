<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\IndexCategoryRequest;
use App\Http\Requests\Category\SearchCategoryRequest;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
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
    public function index(IndexCategoryRequest $request)
    {
        $data = $request->validated();
        $result = $this->categoryService->index($data, auth()->user());

        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], null, 500);
        }

        // Return the resource collection directly for proper pagination structure
        return $this->paginatedResponse($result, CategoryResource::collection($result), __('messages.categories.retrieved_successfully'), 200);
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category)
    {
        $result = $this->categoryService->show($category);

        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], null, 500);
        }

        return $this->successResponse(new CategoryResource($result), __('messages.categories.retrieved_successfully'), 200);

    }

    /**
     * Store a newly created category in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $result = $this->categoryService->storeCategory($data, auth()->user());

        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], null, 500);
        }

        return $this->successResponse(new CategoryResource($result), __('messages.categories.created_successfully'), 200);

    }

    /**
     * Update the specified category in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();
        $result = $this->categoryService->update($data, $category);

        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], null, 500);
        }

        return $this->successResponse(new CategoryResource($result), __('messages.categories.updated_successfully'), 200);

    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        $result = $this->categoryService->destroy($category);

        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], null, 500);
        }

        return $this->successResponse(null, __('messages.categories.deleted_successfully'), 200);

    }

    public function getCategoriesByField(int $field_id)
    {
        $result = $this->categoryService->getCategoriesByField($field_id);
        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], null, 500);
        }

        return $this->successResponse(CategoryResource::collection($result), __('messages.categories.retrieved_successfully'), 200);
    }

    public function search(SearchCategoryRequest $request)
    {
        $data = $request->validated();
        $result = $this->categoryService->search($data);
        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], null, 500);
        }

        return $this->paginatedResponse($result, CategoryResource::collection($result), __('messages.categories.retrieved_successfully'), 200);
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
