<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\IndexCategoryRequest;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Requests\Category\SearchCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Services\CategoryService;
use App\Models\Category;
use App\Traits\ApiResponse;
use OpenApi\Annotations as OA;

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
     * 
     * @OA\Get(
     *     path="/categories",
     *     summary="Get all categories",
     *     description="Get all categories",
     *     tags={"Categories"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(   
     *         name="field_id",
     *         in="query",
     *         description="Field ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(   
     *         name="search",
     *         in="query",
     *         description="Search by name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categories retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Categories retrieved successfully"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="object",
     *                         @OA\Property(property="en", type="string", example="Category 1"),
     *                         @OA\Property(property="ar", type="string", example="الفئة 1"),
     *                     ),
     *                     @OA\Property(property="image", type="string", format="binary", example="image.jpg"), 
     *                     @OA\Property(property="field", type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="object",
     *                             @OA\Property(property="en", type="string", example="Field 1"),
     *                             @OA\Property(property="ar", type="string", example="الحقل 1"),
     *                         ),       
     *                     ),
     *                     @OA\Property(property="products_count", type="integer", example=10),
     *                     @OA\Property(property="created_at", type="string", example="2021-01-01 00:00:00"),
     *                     @OA\Property(property="updated_at", type="string", example="2021-01-01 00:00:00"),
     *                 ),
     *             ),               
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid field_id parameter",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid field_id parameter"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="field_id", type="array",
     *                         @OA\Items(type="string", example="The field_id must be a valid integer."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid search parameter",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid search parameter"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="search", type="array",
     *                         @OA\Items(type="string", example="The search parameter must be a valid string."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Field not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Field not found"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="field_id", type="array",
     *                         @OA\Items(type="string", example="The specified field does not exist."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal server error"),
     *         ),
     *     ),
     * )
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
     * 
     * @OA\Get(
     *     path="/categories/{id}",
     *     summary="Get category by ID",
     *     description="Get a specific category by its ID",
     *     tags={"Categories"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Category ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="object",
     *                     @OA\Property(property="en", type="string", example="Category 1"),
     *                     @OA\Property(property="ar", type="string", example="الفئة 1"),
     *                 ),
     *                 @OA\Property(property="image", type="string", example="image.jpg"),
     *                 @OA\Property(property="field", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="object",
     *                         @OA\Property(property="en", type="string", example="Field 1"),
     *                         @OA\Property(property="ar", type="string", example="الحقل 1"),
     *                     ),
     *                 ),
     *                 @OA\Property(property="products_count", type="integer", example=10),
     *                 @OA\Property(property="created_at", type="string", example="2021-01-01 00:00:00"),
     *                 @OA\Property(property="updated_at", type="string", example="2021-01-01 00:00:00"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category not found"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal server error"),
     *         ),
     *     ),
     * )
     */
    public function show(Category $category)
    {
        $result = $this->categoryService->show($category);

        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 500);
        }

        return $this->successResponse( new CategoryResource($result), __('messages.category.retrieved_successfully'), 200);

    }

    /**
     * store category
     * 
     * @OA\Post(
     *     path="/categories",
     *     summary="Store a category",
     *     description="Store a new category",
     *     tags={"Categories"}, 
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "field_id", "image"},
     *                 @OA\Property(property="name[en]", type="string", example="Category Name"),
     *                 @OA\Property(property="name[ar]", type="string", example="اسم الفئة"),
     *                 @OA\Property(property="field_id", type="integer", example=1),
     *                 @OA\Property(property="image", type="string", format="binary", example="image.jpg"),
     *             )
     *         )
     *     ),
     *     @OA\Response(            
     *         response=201,
     *         description="Category created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="object",
     *                     @OA\Property(property="en", type="string", example="Category Name"),
     *                     @OA\Property(property="ar", type="string", example="اسم الفئة"),
     *                 ),
     *                 @OA\Property(property="image", type="string", example="image.jpg"),
     *                 @OA\Property(property="field", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="object",
     *                         @OA\Property(property="en", type="string", example="Field 1"),
     *                         @OA\Property(property="ar", type="string", example="الحقل 1"),
     *                     ),
     *                 ),
     *                 @OA\Property(property="products_count", type="integer", example=10),
     *                 @OA\Property(property="created_at", type="string", example="2021-01-01 00:00:00"),
     *                 @OA\Property(property="updated_at", type="string", example="2021-01-01 00:00:00"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="name", type="array",
     *                         @OA\Items(type="string", example="Category name is required."),
     *                     ),
     *                     @OA\Property(property="name.en", type="array",
     *                         @OA\Items(type="string", example="English name is required."),
     *                     ),
     *                     @OA\Property(property="name.ar", type="array",
     *                         @OA\Items(type="string", example="Arabic name is required."),
     *                     ),
     *                     @OA\Property(property="field_id", type="array",
     *                         @OA\Items(type="string", example="The field_id field is required."),
     *                     ),
     *                     @OA\Property(property="image", type="array",
     *                         @OA\Items(type="string", example="The image field is required."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Not authorized to create categories",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You are not authorized to create categories"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Field not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Field not found"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="field_id", type="array",
     *                         @OA\Items(type="string", example="The selected field_id is invalid."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity - Invalid image format or size",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unprocessable Entity"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="image", type="array",
     *                         @OA\Items(type="string", example="The image must be a file of type: jpeg, png, jpg, gif, svg."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal server error"),
     *         ),
     *     ),
     * )
     */
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $result = $this->categoryService->storeCategory($data, auth()->user());

        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 500);
        }

        return $this->successResponse( new CategoryResource($result), __('messages.category.created_successfully'), 200);

    }

    /**
     * Update the specified category in storage.
     * 
     * @OA\Post(
     *     path="/categories/{id}",
     *     summary="Update a category",
     *     description="Update an existing category",
     *     tags={"Categories"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Category ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name[en]", type="string", example="Updated Category Name"),
     *                 @OA\Property(property="name[ar]", type="string", example="اسم الفئة المحدث"),
     *                 @OA\Property(property="field_id", type="integer", example=1),
     *                 @OA\Property(property="image", type="string", format="binary", example="image.jpg"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="object",
     *                     @OA\Property(property="en", type="string", example="Updated Category Name"),
     *                     @OA\Property(property="ar", type="string", example="اسم الفئة المحدث"),
     *                 ),
     *                 @OA\Property(property="image", type="string", example="image.jpg"),
     *                 @OA\Property(property="field", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="object",
     *                         @OA\Property(property="en", type="string", example="Field 1"),
     *                         @OA\Property(property="ar", type="string", example="الحقل 1"),
     *                     ),
     *                 ),
     *                 @OA\Property(property="updated_at", type="string", example="2021-01-01 00:00:00"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="name", type="array",
     *                         @OA\Items(type="string", example="The name field is required."),
     *                     ),
     *                     @OA\Property(property="field_id", type="array",
     *                         @OA\Items(type="string", example="The field_id must be a valid integer."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category not found"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Not authorized to update this category",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You are not authorized to update this category"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal server error"),
     *         ),
     *     ),
     * )
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();
        $result = $this->categoryService->update($data, $category);

        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 500);
        }

        return $this->successResponse( new CategoryResource($result), __('messages.category.updated_successfully'), 200);

    }

    /**
     * Remove the specified category from storage.
     * 
     * @OA\Delete(
     *     path="/categories/{id}",
     *     summary="Delete a category",
     *     description="Delete an existing category",
     *     tags={"Categories"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Category ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category deleted successfully"),
     *             @OA\Property(property="data", type="null", example=null),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category not found"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Not authorized to delete this category",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You are not authorized to delete this category"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflict - Category has associated products",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Cannot delete category with associated products"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal server error"),
     *         ),
     *     ),
     * )
     */
    public function destroy(Category $category)
    {
        $result = $this->categoryService->destroy($category);

        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 500);
        }

        return $this->successResponse( null, __('messages.category.deleted_successfully'), 200);

    }

    /**
     * Get categories by field ID.
     * 
     * @OA\Get(
     *     path="/categories/field/{field_id}",
     *     summary="Get categories by field",
     *     description="Get all categories for a specific field",
     *     tags={"Categories"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="field_id",
     *         in="path",
     *         description="Field ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categories retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Categories retrieved successfully"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="object",
     *                         @OA\Property(property="en", type="string", example="Category 1"),
     *                         @OA\Property(property="ar", type="string", example="الفئة 1"),
     *                     ),
     *                     @OA\Property(property="image", type="string", example="image.jpg"),
     *                     @OA\Property(property="field", type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="object",
     *                             @OA\Property(property="en", type="string", example="Field 1"),
     *                             @OA\Property(property="ar", type="string", example="الحقل 1"),
     *                         ),
     *                     ),
     *                     @OA\Property(property="products_count", type="integer", example=10),
     *                     @OA\Property(property="created_at", type="string", example="2021-01-01 00:00:00"),
     *                     @OA\Property(property="updated_at", type="string", example="2021-01-01 00:00:00"),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid field_id parameter",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid field_id parameter"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="field_id", type="array",
     *                         @OA\Items(type="string", example="The field_id must be a valid integer."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Field not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Field not found"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="field_id", type="array",
     *                         @OA\Items(type="string", example="The specified field does not exist."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal server error"),
     *         ),
     *     ),
     * )
     */
    public function getCategoriesByField(int $field_id)
    {
        $result = $this->categoryService->getCategoriesByField($field_id);
        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 500);
        }

        return $this->successResponse( CategoryResource::collection($result), __('messages.categories.retrieved_successfully'), 200);
    }

    /**
     * Search categories.
     * 
     * @OA\Get(
     *     path="/categories/search",
     *     summary="Search categories",
     *     description="Search categories by name",
     *     tags={"Categories"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="field_id",
     *         in="query",
     *         description="Field ID filter",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categories retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Categories retrieved successfully"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="object",
     *                         @OA\Property(property="en", type="string", example="Category 1"),
     *                         @OA\Property(property="ar", type="string", example="الفئة 1"),
     *                     ),
     *                     @OA\Property(property="image", type="string", example="image.jpg"),
     *                     @OA\Property(property="field", type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="object",
     *                             @OA\Property(property="en", type="string", example="Field 1"),
     *                             @OA\Property(property="ar", type="string", example="الحقل 1"),
     *                         ),
     *                     ),
     *                     @OA\Property(property="products_count", type="integer", example=10),
     *                     @OA\Property(property="created_at", type="string", example="2021-01-01 00:00:00"),
     *                     @OA\Property(property="updated_at", type="string", example="2021-01-01 00:00:00"),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid search parameter",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid search parameter"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="search", type="array",
     *                         @OA\Items(type="string", example="The search parameter is required."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid field_id parameter",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid field_id parameter"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="field_id", type="array",
     *                         @OA\Items(type="string", example="The field_id must be a valid integer."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Field not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Field not found"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="field_id", type="array",
     *                         @OA\Items(type="string", example="The specified field does not exist."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal server error"),
     *         ),
     *     ),
     * )
     */
    public function search(SearchCategoryRequest $request)
    {
        $result = $this->categoryService->search($request);
        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 500);
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
