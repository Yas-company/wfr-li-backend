<?php

namespace App\Http\Controllers\api\v1\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\IndexCategoryRequest;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
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
     *
     *     @OA\Parameter(
     *         name="field_id",
     *         in="query",
     *         description="Field ID",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by name",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Categories retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Categories retrieved successfully"),
     *             @OA\Property(property="data", type="array",
     *
     *                 @OA\Items(type="object",
     *
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
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid field_id parameter",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Invalid field_id parameter"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="field_id", type="array",
     *
     *                         @OA\Items(type="string", example="The field_id must be a valid integer."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Invalid search parameter",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Invalid search parameter"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="search", type="array",
     *
     *                         @OA\Items(type="string", example="The search parameter must be a valid string."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Field not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Field not found"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="field_id", type="array",
     *
     *                         @OA\Items(type="string", example="The specified field does not exist."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Internal server error"),
     *         ),
     *     ),
     * )
     */
    public function index(IndexCategoryRequest $request)
    {
        $result = $this->categoryService->index();

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
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Category ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Category retrieved successfully",
     *
     *         @OA\JsonContent(
     *
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
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Category not found"),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Internal server error"),
     *         ),
     *     ),
     * )
     */
    public function show(Category $category)
    {
        $result = $this->categoryService->show($category);

        return $this->successResponse(new CategoryResource($result), __('messages.category.retrieved_successfully'), 200);
    }
}
