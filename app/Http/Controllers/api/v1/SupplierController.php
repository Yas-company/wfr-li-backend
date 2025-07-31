<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdsResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Http\Services\Contracts\SupplierServiceInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;


class SupplierController extends Controller
{
    use ApiResponse;

    public function __construct(protected SupplierServiceInterface $supplierService) {}

    public function ads($supplierId)
    {
        $ads = $this->supplierService->getAds($supplierId);

    return $this->successResponse(AdsResource::collection($ads), __('messages.ads.retrieved_successfully'));
    }

    public function categories($supplierId)
    {
        $categories = $this->supplierService->getCategories($supplierId);

        return $this->successResponse(CategoryResource::collection($categories), __('messages.categories.retrieved_successfully'));
    }

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

    public function showProduct($id)
    {
        $product = $this->supplierService->getProductById($id);

        if (! $product) {
            return $this->notFoundResponse(__('messages.product.not_found'));
        }

        return $this->successResponse(new ProductResource($product), __('messages.product.retrieved_successfully'));
    }


}
