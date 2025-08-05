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
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $products = $this->productService->getSupplierProducts(auth()->id());

        return $this->paginatedResponse($products, ProductResource::collection($products));
    }

    /**
     * Display the specified resource.
     *
     * @param Product $product
     *
     * @return ProductResource
     */
    public function show(Product $product)
    {
        $this->authorize('view', $product);

        $product->load(['ratings', 'category', 'category.field', 'ratings.user']);

        return new ProductResource($product);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProductRequest $request
     *
     * @return JsonResponse
     */
    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->store($request->validated(), auth()->user());

        return $this->createdResponse(new ProductResource($product));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateProductRequest $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product = $this->productService->update($product, $request->validated());

        return $this->successResponse(new ProductResource($product), 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return JsonResponse
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
     */
    public function expiredCount()
    {
        return $this->successResponse(['expired' => $this->productService->countExpired(auth()->user())]);
    }

    /**
     * Display the number of near expiry products.
     *
     * @return JsonResponse
     */
    public function nearExpiryCount()
    {
        return $this->successResponse(['near_expiry' => $this->productService->countNearExpiry(auth()->user())]);
    }


    /**
     * Display the related products.
     *
     * @param Product $product
     *
     * @return ProductResource
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
     */
    public function getOutOfStockProducts()
    {
        $products = $this->productService->getOutOfStockProducts(auth()->user()->id);

        return $this->paginatedResponse($products, ProductResource::collection($products));
    }
}
