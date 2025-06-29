<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        return $this->successResponse( CategoryResource::collection($result), 'Categories retrieved successfully', 200);
        // try {
        //     $user = Auth::user();

        //     if (!$this->canManageCategories($user)) {
        //         return $this->errorResponse('Only approved suppliers can view categories.', 403);
        //     }

        //     $categories = Category::where('supplier_id', $user->id)
        //         ->orderBy('created_at', 'desc')
        //         ->get();

        //     return $this->successResponse(
        //         CategoryResource::collection($categories),
        //         'Categories retrieved successfully'
        //     );
        // } catch (\Exception $e) {
        //     return $this->errorResponse('Failed to retrieve categories.', 500);
        // }
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
        // try {
        //     $user = Auth::user();

        //     if (!$this->canManageCategories($user)) {
        //         return $this->errorResponse('Only approved suppliers can view categories.', 403);
        //     }

        //     if (!$this->ownsCategory($user, $category)) {
        //         return $this->errorResponse('You can only view your own categories.', 403);
        //     }

        //     return $this->successResponse(
        //         new CategoryResource($category),
        //         'Category retrieved successfully'
        //     );
        // } catch (\Exception $e) {
        //     return $this->errorResponse('Failed to retrieve category.', 500);
        // }
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

        // $user = Auth::user();
        
        // try {
        // if (!$this->canManageCategories($user)) {
        //         return $this->errorResponse('Only approved suppliers can add categories.', 403);
        // }

        //     $validated = $request->validated();

        //     $created = [];

        //     DB::beginTransaction();

        //     try {
        //         foreach ($validated['categories'] as $catData) {
        //             $created[] = Category::create([
        //                 'name' => $catData['name'],
        //                 'supplier_id' => $user->id,
        //             ]);
        //         }

        //         DB::commit();

        //         return $this->successResponse(
        //             CategoryResource::collection($created),
        //             'Categories created successfully'
        //         );
        //     } catch (\Exception $e) {
        //         DB::rollBack();
        //         throw $e;
        //     }
        // } catch (\Exception $e) {
        //     return $this->errorResponse('Failed to create categories.', 500);
        // }
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
        // try {
        //     $user = Auth::user();

        //     if (!$this->canManageCategories($user)) {
        //         return $this->errorResponse('Only approved suppliers can edit categories.', 403);
        //     }

        //     if (!$this->ownsCategory($user, $category)) {
        //         return $this->errorResponse('You can only edit your own categories.', 403);
        //     }

        //     $validated = $request->validated();

        //     $category->update([
        //         'name' => $validated['name'],
        //     ]);

        //     return $this->successResponse(
        //         new CategoryResource($category),
        //         'Category updated successfully'
        //     );
        // } catch (\Exception $e) {
        //     return $this->errorResponse('Failed to update category.', 500);
        // }
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
        // try {
        //     $user = Auth::user();

        //     if (!$this->canManageCategories($user)) {
        //         return $this->errorResponse('Only approved suppliers can delete categories.', 403);
        //     }

        //     if (!$this->ownsCategory($user, $category)) {
        //         return $this->errorResponse('You can only delete your own categories.', 403);
        //     }

        //     // Check if category has associated products
        //     if ($category->products()->exists()) {
        //         return $this->errorResponse(
        //             'Cannot delete category. It has associated products.',
        //             422
        //         );
        //     }

        //     $category->delete();

        //     return $this->successResponse(null, 'Category deleted successfully');
        // } catch (\Exception $e) {
        //     return $this->errorResponse('Failed to delete category.', 500);
        // }
    }

    public function getCategoriesByField(int $field_id)
    {
        $result = $this->categoryService->getCategoriesByField($field_id);
        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 500);
        }

        return $this->successResponse( CategoryResource::collection($result), 'Categories retrieved successfully', 200);
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
