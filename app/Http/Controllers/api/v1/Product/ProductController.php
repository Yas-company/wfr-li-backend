<?php

namespace App\Http\Controllers\api\v1\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\StoreProductRequest;
use App\Http\Requests\Products\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\Contracts\ProductServiceInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

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
     */
    public function index(Request $request)
    {
        $products = $this->productService->list($request->all());

        return $this->paginatedResponse($products, ProductResource::collection($products), message: __('messages.products.retrieved_successfully'));
    }

    /**
     * Store a newly created resource in storage.
     *
     *
     * @return JsonResponse
     */
    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->create($request->validated(), auth()->user());

        return $this->createdResponse(new ProductResource($product), __('messages.products.created_successfully'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(UpdateProductRequest $request, $id)
    {
        $product = $this->productService->update($id, $request->all());

        return $this->successResponse(new ProductResource($product), __('messages.products.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $this->productService->delete($id);

        return $this->successResponse(null, __('messages.products.deleted_successfully'));
    }

    /**
     * Display the number of expired products.
     *
     * @return JsonResponse
     */
    public function expiredCount()
    {
        return $this->successResponse(['expired' => $this->productService->countExpired()], __('messages.success'));
    }

    /**
     * Display the number of near expiry products.
     *
     * @return JsonResponse
     */
    public function nearExpiryCount()
    {
        return $this->successResponse(['near_expiry' => $this->productService->countNearExpiry()], __('messages.success'));
    }

    /**
     * Display the stock status counts.
     *
     * @return JsonResponse
     */
    public function stockStatusCounts()
    {
        $data = $this->productService->countStockStatuses();

        return $this->successResponse($data, __('messages.success'));
    }

    /**
     * Display the specified resource.
     *
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
     *
     * @return ProductResource
     */
    public function filter(Request $request)
    {
        $products = Product::filterAndSearch($request->all())
            ->with('category')
            ->paginate(10);

        return $this->paginatedResponse($products, ProductResource::collection($products), message: __('messages.success'));
    }

    /**
     * Display the related products.
     *
     *
     * @return ProductResource
     */
    public function related(Product $product)
    {
        $related = $product->related();

        return $this->successResponse(ProductResource::collection($related), __('messages.success'));
    }
}
