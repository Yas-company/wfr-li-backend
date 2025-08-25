<?php

namespace App\Http\Controllers\api\v1\Category\Supplier;

use App\Traits\ApiResponse;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Category\CategorySelectResource;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class GetAllCategoriesController extends Controller
{
    use ApiResponse;

    /**
     * Display all categories.
     *
     * @OA\Get(
     *     path="/supplier/categories",
     *     summary="Get all categories",
     *     description="Get all categories",
     *     tags={"Categories"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="filter[name]",
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
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal server error"),
     *         ),
     *     ),
     * )
     */
    public function __invoke(CategoryService $categoryService): JsonResponse
    {
        $categories = $categoryService->getAll();

        return $this->successResponse(CategorySelectResource::collection($categories));
    }
}
