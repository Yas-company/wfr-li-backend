<?php

namespace App\Http\Controllers\api\v1\Product\Buyer;

use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Services\Contracts\ProductServiceInterface;

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
        $products = $this->productService->getProductsForBuyer();

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
        $product->load(['ratings', 'category', 'category.field', 'ratings.user']);

        return new ProductResource($product);
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
        $related = $product->related()->load(['category', 'category.field']);

        return ProductResource::collection($related);
    }
}
