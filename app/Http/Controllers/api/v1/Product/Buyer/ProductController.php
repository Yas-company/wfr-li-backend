<?php

namespace App\Http\Controllers\api\v1\Product\Buyer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\SimilarProductResource;
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
        $products = $this->productService->getProductsForBuyer();

        return $this->paginatedResponse($products, ProductResource::collection($products));
    }

    /**
     * Display the specified resource.
     *
     *
     * @return ProductResource
     */
    public function show(int $id)
    {
        $product = $this->productService->getProductById($id);

        return new ProductResource($product);
    }

    /**
     * Display the related products.
     *
     *
     * @return ProductResource
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
     * @return SimilarProductResource
     */
    public function getSimilarProducts(Product $product)
    {
        $similarProducts = $this->productService->getSimilarProducts($product);

        return $this->successResponse(ProductResource::collection($similarProducts), __('messages.products.similar_products_retrieved_successfully'));
    }
}
