<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\StoreProductRequest;
use App\Http\Requests\Products\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Services\Contracts\ProductServiceInterface;
use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponse;

    public function __construct(protected ProductServiceInterface $productService) {}

    public function index(Request $request)
    {
        $products = $this->productService->list($request->all());

        return $this->paginatedResponse($products, ProductResource::collection($products), message: __('messages.products.retrieved_successfully'));
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->create($request->all());

        return $this->createdResponse(new ProductResource($product), __('messages.products.created_successfully'));
    }

    public function update(UpdateProductRequest $request, $id)
    {
        $product = $this->productService->update($id, $request->all());

        return $this->successResponse(new ProductResource($product), __('messages.products.updated_successfully'));
    }

    public function destroy($id)
    {
        $this->productService->delete($id);

        return $this->successResponse(null, __('messages.products.deleted_successfully'));
    }

    public function expiredCount()
    {
        return $this->successResponse(['expired' => $this->productService->countExpired()], __('messages.success'));
    }

    public function nearExpiryCount()
    {
        return $this->successResponse(['near_expiry' => $this->productService->countNearExpiry()], __('messages.success'));
    }

    public function stockStatusCounts()
    {
        $data = $this->productService->countStockStatuses();

        return $this->successResponse($data, __('messages.success'));
    }

    public function show(Product $product)
    {
        $product->load(['ratings']);

        return new ProductResource($product);
    }

    public function filter(Request $request)
    {
        $products = Product::filterAndSearch($request->all())
            ->with('category')
            ->paginate(10);

        return $this->paginatedResponse($products, ProductResource::collection($products), message: __('messages.success'));
    }

    public function related(Product $product)
    {
        $related = $product->related();

        return $this->successResponse(ProductResource::collection($related), __('messages.success'));
    }
}
