<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdsResource;
use App\Http\Services\Contracts\SupplierServiceInterface;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Suppliers",
 *     description="Supplier-related endpoints"
 * )
 */
class SupplierController extends Controller
{
    use ApiResponse;

    public function __construct(protected SupplierServiceInterface $supplierService) {}

    /**
     * Get supplier advertisements
     *
     * @OA\Get(
     *     path="/supplier/{supplierId}/ads",
     *     summary="Get supplier ads",
     *     description="Get all active advertisements for a specific supplier",
     *     tags={"Suppliers"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="supplierId",
     *         in="path",
     *         description="Supplier ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Supplier ads retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ads retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AdsResource"))
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
     *         description="Forbidden - Not authorized to access supplier data",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You are not authorized to access supplier data"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Supplier not found or no ads available",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Supplier not found or no ads available"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
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
    public function ads($supplierId)
    {
        $ads = $this->supplierService->getAds($supplierId);
        return $this->successResponse(AdsResource::collection($ads), __('messages.ads.retrieved_successfully'));
    }


    /**
     * Get supplier products with optional filtering
     *
     * @OA\Get(
     *     path="/supplier/{supplierId}/products",
     *     summary="Get supplier products",
     *     description="Get paginated products for a specific supplier with optional category and search filtering",
     *     tags={"Suppliers"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="supplierId",
     *         in="path",
     *         description="Supplier ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter by category ID",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term for product names and descriptions",
     *         required=false,
     *         @OA\Schema(type="string", example="apple")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Supplier products retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Products retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ProductResource")),
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="last_page", type="integer", example=5),
     *             @OA\Property(property="per_page", type="integer", example=15),
     *             @OA\Property(property="total", type="integer", example=75),
     *             @OA\Property(property="from", type="integer", example=1),
     *             @OA\Property(property="to", type="integer", example=15),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid filter parameters",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid filter parameters"),
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
     *         description="Forbidden - Not authorized to access supplier data",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You are not authorized to access supplier data"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Supplier not found or no products available",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Supplier not found or no products available"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
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
    public function products(Request $request, $supplierId)
    {
        $filters = [
            'supplier_id' => $supplierId,
            'category_id' => $request->get('category_id'),
            'search' => $request->get('search'),
        ];

        $products = $this->supplierService->getProducts($filters);

        return $this->paginatedResponse($products,
            ProductResource::collection($products),
            __('messages.products.retrieved_successfully')
        );

    }

    /**
     * Get specific product details
     *
     * @OA\Get(
     *     path="/supplier/products/{id}",
     *     summary="Get product details",
     *     description="Get detailed information about a specific product",
     *     tags={"Suppliers"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product retrieved successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/ProductResource")
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
     *         description="Forbidden - Not authorized to access product data",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You are not authorized to access product data"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product not found"),
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
    public function showProduct($id)
    {
        $product = $this->supplierService->getProductById($id);

        if (!$product) {
            return $this->notFoundResponse(__('messages.products.not_found'));
        }

        return $this->successResponse(new ProductResource($product), __('messages.products.retrieved_successfully'));
    }

}
