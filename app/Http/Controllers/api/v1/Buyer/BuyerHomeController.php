<?php

namespace App\Http\Controllers\api\v1\Buyer;

use App\Dtos\BuyerHomeFilterDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\BuyerHomeRequest;
use App\Http\Resources\Buyer\HomePageBuyerResource;
use App\Services\BuyerHomeService;
use App\Traits\ApiResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Buyer",
 *     description="Buyer endpoints"
 * )
 */
class BuyerHomeController extends Controller
{
    use ApiResponse;

    public function __construct(protected BuyerHomeService $buyerHomeService) {}

    /**
     * Get the suppliers and products.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/buyer/suppliers-and-products",
     *     summary="Get suppliers and their products for buyer home page",
     *     description="Get latest 4 approved suppliers with their top 10 published products for the buyer home page",
     *     tags={"Buyer"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Suppliers and products retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Suppliers and products retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/HomePageBuyerResource"))
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
     *         response=403,
     *         description="Forbidden - Not authorized as buyer",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="You are not authorized to access buyer resources"),
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
    public function getSuppliersAndProducts(BuyerHomeRequest $request)
    {
        $filters = BuyerHomeFilterDto::fromRequest($request);
        $suppliers = $this->buyerHomeService->getSuppliersAndProducts($filters);

        return $this->successResponse(HomePageBuyerResource::collection($suppliers));
    }
}
