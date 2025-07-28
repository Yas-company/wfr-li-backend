<?php

namespace App\Http\Controllers\api\v1\Product;

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
        $products = $this->productService->list($request->all());

        return $this->paginatedResponse($products,ProductResource::collection($products));
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
        $product = $this->productService->create($request->validated(), auth()->user());

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
    public function update(UpdateProductRequest $request, $id)
    {
        $product = $this->productService->update($id, $request->all());

        return $this->successResponse(new ProductResource($product), 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $this->productService->delete($id);

        return $this->successResponse(null, 'Delete deleted successfully');
    }

    /**
     * Display the number of expired products.
     *
     * @return JsonResponse
     */
    public function expiredCount()
    {
        return $this->successResponse(['expired' => $this->productService->countExpired()]);
    }

    /**
     * Display the number of near expiry products.
     *
     * @return JsonResponse
     */
    public function nearExpiryCount()
    {
        return $this->successResponse(['near_expiry' => $this->productService->countNearExpiry()]);
    }

    /**
     * Display the stock status counts.
     *
     * @return JsonResponse
     */
    public function stockStatusCounts()
    {
        $data = $this->productService->countStockStatuses();

        return $this->successResponse($data);
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
        $product->load(['ratings']);

        return new ProductResource($product);
    }

    /**
     * Display a filtered listing of the resource.
     *
     * @param Request $request
     *
     * @return ProductResource
     */
    public function filter(Request $request)
    {
        $products = Product::filterAndSearch($request->all())
            ->with('category')
            ->paginate(10);

        return ProductResource::collection($products);
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
}
