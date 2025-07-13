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

        return $this->paginatedResponse($products,ProductResource::collection($products)
        );
    }


    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->create($request->all());
        return $this->createdResponse(new ProductResource($product));
    }

    public function update(UpdateProductRequest $request, $id)
    {
        $product = $this->productService->update($id, $request->all());
        return $this->successResponse(new ProductResource($product), 'Product updated successfully');
    }

    public function destroy($id)
    {
        $this->productService->delete($id);
        return $this->successResponse(null, 'Delete deleted successfully');
    }

    public function expiredCount()
    {
        return $this->successResponse(['expired' => $this->productService->countExpired()]);
    }

    public function nearExpiryCount()
    {
        return $this->successResponse(['near_expiry' => $this->productService->countNearExpiry()]);
    }

    public function stockStatusCounts()
    {
        $data = $this->productService->countStockStatuses();
        return $this->successResponse($data);
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

        return ProductResource::collection($products);
    }

    public function related(Product $product)
    {
        $related = $product->related();
        return ProductResource::collection($related);
    }
}
