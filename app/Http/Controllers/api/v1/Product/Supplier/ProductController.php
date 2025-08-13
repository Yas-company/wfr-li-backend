<?php

namespace App\Http\Controllers\api\v1\Product\Supplier;

use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Requests\Products\StoreProductRequest;
use App\Services\Contracts\ProductServiceInterface;
use App\Http\Requests\Products\UpdateProductRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;

class ProductController extends Controller
{
    use ApiResponse;

    /**
     * ProductController constructor.
     *
     * @param ProductServiceInterface $productService
     */
    public function __construct(protected ProductServiceInterface $productService) {}


    /**
     * Display a listing of supplier products.
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @OA\Get(
     *     path="/supplier/products",
     *     summary="List supplier products",
     *     tags={"Supplier Products"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Products fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ProductResource")),
     *             @OA\Property(
     *                 property="links", type="object",
     *                 @OA\Property(property="first", type="string", nullable=true),
     *                 @OA\Property(property="last", type="string", nullable=true),
     *                 @OA\Property(property="next", type="string", nullable=true),
     *                 @OA\Property(property="prev", type="string", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated"),
     *             @OA\Property(property="errors", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $products = $this->productService->getSupplierProducts(auth()->id());

        return $this->paginatedResponse($products, ProductResource::collection($products));
    }

    /**
     * Display the specified product.
     *
     * @param Product $product
     *
     * @return ProductResource
     * @OA\Get(
     *     path="/supplier/products/{product}",
     *     summary="Get product details",
     *     tags={"Supplier Products"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="product", in="path", required=true, description="Product ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Product retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function show(Product $product)
    {
        $this->authorize('view', $product);

        $product->load(['ratings', 'category', 'category.field', 'ratings.user']);

        return new ProductResource($product);
    }

    /**
     * Store a newly created product.
     *
     * @param StoreProductRequest $request
     *
     * @return JsonResponse
     * @OA\Post(
     *     path="/supplier/products",
     *     summary="Create product",
     *     tags={"Supplier Products"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","description","price","quantity","stock_qty","unit_type","status","category_id","min_order_quantity"},
     *             @OA\Property(property="name", type="object", example={"ar":"اسم المنتج","en":"Product name"}),
     *             @OA\Property(property="description", type="object", example={"ar":"وصف","en":"Description"}),
     *             @OA\Property(property="price", type="number", format="float", minimum=0.01, example=10.5),
     *             @OA\Property(property="quantity", type="number", minimum=1, example=10),
     *             @OA\Property(property="stock_qty", type="integer", minimum=0, example=100),
     *             @OA\Property(property="nearly_out_of_stock_limit", type="integer", nullable=true, example=5),
     *             @OA\Property(property="unit_type", type="string", example="PIECE"),
     *             @OA\Property(property="status", type="string", example="ACTIVE"),
     *             @OA\Property(property="category_id", type="integer", example=3),
     *             @OA\Property(property="min_order_quantity", type="number", minimum=1, example=1)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Product created",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Created"),
     *             @OA\Property(property="data", ref="#/components/schemas/ProductResource")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={"price":{"The price must be at least 0.01."}})
     *         )
     *     )
     * )
     */
    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->store($request->validated(), auth()->user());

        return $this->createdResponse(new ProductResource($product));
    }

    /**
     * Update the specified product.
     *
     * @param UpdateProductRequest $request
     * @param Product $product
     *
     * @return JsonResponse
     * @OA\Post(
     *     path="/supplier/products/{product}",
     *     summary="Update product",
     *     tags={"Supplier Products"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="product", in="path", required=true, description="Product ID", @OA\Schema(type="integer", example=1)),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","description","price","quantity","stock_qty","unit_type","status","category_id","min_order_quantity"},
     *             @OA\Property(property="name", type="object", example={"ar":"اسم المنتج","en":"Product name"}),
     *             @OA\Property(property="description", type="object", example={"ar":"وصف","en":"Description"}),
     *             @OA\Property(property="price", type="number", format="float", minimum=0.01, example=10.5),
     *             @OA\Property(property="quantity", type="number", minimum=0, example=10),
     *             @OA\Property(property="stock_qty", type="integer", minimum=0, example=100),
     *             @OA\Property(property="nearly_out_of_stock_limit", type="integer", nullable=true, example=5),
     *             @OA\Property(property="unit_type", type="string", example="PIECE"),
     *             @OA\Property(property="status", type="string", example="ACTIVE"),
     *             @OA\Property(property="category_id", type="integer", example=3),
     *             @OA\Property(property="image", type="string", format="binary", nullable=true, description="Optional product image"),
     *             @OA\Property(property="min_order_quantity", type="number", minimum=1, example=1)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Product updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/ProductResource")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Product not found"),
     *     @OA\Response(response=422, description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={"price":{"The price must be at least 0.01."}})
     *         )
     *     )
     * )
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product = $this->productService->update($product, $request->validated());

        return $this->successResponse(new ProductResource($product), 'Product updated successfully');
    }

    /**
     * Remove the specified product.
     *
     * @param Product $product
     *
     * @return JsonResponse
     * @OA\Delete(
     *     path="/supplier/products/{product}",
     *     summary="Delete product",
     *     tags={"Supplier Products"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="product", in="path", required=true, description="Product ID", @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="Product deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Delete deleted successfully"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        $this->productService->delete($product);

        return $this->successResponse(null, 'Delete deleted successfully');
    }

    /**
     * Display the number of expired products.
     *
     * @return JsonResponse
     * @OA\Get(
     *     path="/supplier/products/expired/count",
     *     summary="Expired products count",
     *     tags={"Supplier Products"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Count retrieved",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", type="object", example={"expired": 4})
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function expiredCount()
    {
        return $this->successResponse(['expired' => $this->productService->countExpired(auth()->user())]);
    }

    /**
     * Display the number of near expiry products.
     *
     * @return JsonResponse
     * @OA\Get(
     *     path="/supplier/products/near-expiry/count",
     *     summary="Near expiry products count",
     *     tags={"Supplier Products"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Count retrieved",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", type="object", example={"near_expiry": 7})
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function nearExpiryCount()
    {
        return $this->successResponse(['near_expiry' => $this->productService->countNearExpiry(auth()->user())]);
    }


    /**
     * Display related products for the specified product.
     *
     * @param Product $product
     */
    public function related(Product $product)
    {
        $related = $product->related();

        return ProductResource::collection($related);
    }

    /**
     * Display the available products.
     *
     * @return JsonResponse
     * @OA\Get(
     *     path="/supplier/products/available",
     *     summary="Available products",
     *     tags={"Supplier Products"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Products fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ProductResource"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function getAvailableProducts()
    {
        $products = $this->productService->getAvailableProducts(auth()->user()->id);

        return $this->paginatedResponse($products, ProductResource::collection($products));
    }

    /**
     * Display the nearly out of stock products.
     *
     * @return JsonResponse
     * @OA\Get(
     *     path="/supplier/products/nearly-out-of-stock",
     *     summary="Nearly out of stock products",
     *     tags={"Supplier Products"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Products fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ProductResource"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function getNearlyOutOfStockProducts()
    {
        $products = $this->productService->getNearlyOutOfStockProducts(auth()->user()->id);

        return $this->paginatedResponse($products, ProductResource::collection($products));
    }

    /**
     * Display the out of stock products.
     *
     * @return JsonResponse
     * @OA\Get(
     *     path="/supplier/products/out-of-stock",
     *     summary="Out of stock products",
     *     tags={"Supplier Products"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Products fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ProductResource"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function getOutOfStockProducts()
    {
        $products = $this->productService->getOutOfStockProducts(auth()->user()->id);

        return $this->paginatedResponse($products, ProductResource::collection($products));
    }
}
