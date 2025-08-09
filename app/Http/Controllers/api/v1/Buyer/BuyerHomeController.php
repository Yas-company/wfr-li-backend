<?php

namespace App\Http\Controllers\api\v1\Buyer;

use App\Enums\ProductStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Buyer\HomePageBuyerResource;
use App\Models\Product;
use App\Models\User;
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
     *     @OA\Response(
     *         response=200,
     *         description="Suppliers and products retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Suppliers and products retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/HomePageBuyerResource"))
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
     *         description="Forbidden - Not authorized as buyer",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You are not authorized to access buyer resources"),
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
    public function getSuppliersAndProducts()
    {
        $suppliers = User::role(UserRole::SUPPLIER->value)
            ->where('status', UserStatus::APPROVED)
            ->select('id', 'name', 'image')
            ->latest()
            ->take(4)
            ->get();

        $allProducts = Product::whereIn('supplier_id', $suppliers->pluck('id'))
            ->published()
            ->with([
                'media',
                'currentUserFavorite',
                'ratings',
                'category',
                'category.field',
                'ratings.user'
            ])
            ->latest() 
            ->get()
            ->groupBy('supplier_id');

        // Attach top 10 products per supplier
        foreach ($suppliers as $supplier) {
            $supplier->setRelation('products',
                $allProducts->get($supplier->id, collect())->take(10)
            );
        }

        return $this->successResponse(HomePageBuyerResource::collection($suppliers));
    }
}
