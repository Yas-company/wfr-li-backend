<?php

namespace App\Http\Controllers\api\v1\Category\Supplier;

use App\Http\Controllers\Controller;
use App\Http\Resources\Category\CategorySelectResource;
use App\Services\CategoryService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

class GetSupplierCategoriesController extends Controller
{
    use ApiResponse;

    public function __construct(public CategoryService $categoryService) {}

    /**
     * Get supplier categories
     *
     * @OA\Get(
     *     path="/api/v1/categories/supplier-categories",
     *     summary="Get supplier categories",
     *     tags={"Categories"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Supplier categories retrieved successfully",
     *
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/CategorySelectResource"))
     *     )
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Unauthorized"))
     *     )
     *
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Forbidden"))
     *     )
     *
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Not Found"))
     *     )
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Internal Server Error"))
     *     )
     * )
     */
    public function __invoke()
    {
        $result = $this->categoryService->getSupplierCategories(Auth::user());

        return $this->successResponse(CategorySelectResource::collection($result), __('messages.categories.retrieved_successfully'), statusCode: 200);
    }
}
