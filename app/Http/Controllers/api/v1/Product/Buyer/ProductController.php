<?php

namespace App\Http\Controllers\api\v1\Product\Buyer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\SimilarProductResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\Contracts\ProductServiceInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;

class ProductController extends Controller
{
    use ApiResponse;

    /**
     * ProductController constructor.
     */
    public function __construct(protected ProductServiceInterface $productService) {}

    /**
     * Display a listing of the resource.
     *
     *
     * @return JsonResponse
     * @OA\Get(
     *     path="/buyer/products",
     *     summary="Get all products",
     *     tags={"Buyer Products"},
     *     description="Retrieve a paginated list of products available for purchase.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Products fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Products fetched successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/ProductResource")
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string", nullable=true, example="https://api.example.com/buyer/products?page=1"),
     *                 @OA\Property(property="last", type="string", nullable=true, example="https://api.example.com/buyer/products?page=3"),
     *                 @OA\Property(property="next", type="string", nullable=true, example="https://api.example.com/buyer/products?page=2"),
     *                 @OA\Property(property="prev", type="string", nullable=true, example=null)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - User not authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated"),
     *             @OA\Property(property="errors", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error - Invalid query parameters",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={"per_page": {"The per page must be an integer."}})
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $products = $this->productService->getProductsForBuyer();

        return $this->paginatedResponse($products, ProductResource::collection($products));
    }

    /**
     * Display the specified resource.
     *
     *
     * @return ProductResource
     * @OA\Get(
     *     path="/buyer/products/{product}",
     *     summary="Get product details",
     *     tags={"Buyer Products"},
     *     description="Retrieve full details for a specific product.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - User not authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated"),
     *             @OA\Property(property="errors", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error - Invalid path parameter",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={"product": {"The selected product is invalid."}})
     *         )
     *     )
     * )
     */
    public function show(Product $product)
    {
        $product->load(['ratings', 'category', 'category.field', 'ratings.user']);

        return new ProductResource($product);
    }

    /**
     * Display related products for the specified product.
     *
     * @return AnonymousResourceCollection
     */
    public function related(Product $product)
    {
        $related = $product->related()->load(['category', 'category.field']);

        return ProductResource::collection($related);
    }

    /**
     * Get similar products.
     *
     *
     * @return JsonResponse
     * @OA\Get(
     *     path="/buyer/products/{product}/similar",
     *     summary="Get similar products",
     *     tags={"Buyer Products"},
     *     description="Retrieve a list of similar products based on the specified product.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Similar products retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Similar products retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/ProductResource")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - User not authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated"),
     *             @OA\Property(property="errors", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error - Invalid path parameter",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={"product": {"The selected product is invalid."}})
     *         )
     *     )
     * )
     */
    public function getSimilarProducts(Product $product)
    {
        $similarProducts = $this->productService->getSimilarProducts($product);

        return $this->successResponse(ProductResource::collection($similarProducts), __('messages.products.similar_products_retrieved_successfully'));
    }
}
