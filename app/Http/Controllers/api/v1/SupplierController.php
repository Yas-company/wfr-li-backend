<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdsResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Http\Services\Contracts\SupplierServiceInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    use ApiResponse;

    public function __construct(protected SupplierServiceInterface $supplierService) {}

    public function ads($supplierId)
    {
        $ads = $this->supplierService->getAds($supplierId);

        return $this->successResponse(AdsResource::collection($ads), 'تم جلب الإعلانات بنجاح');
    }

    public function categories($supplierId)
    {
        $categories = $this->supplierService->getCategories($supplierId);

        return $this->successResponse(CategoryResource::collection($categories), 'تم جلب الأقسام بنجاح');
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
            'تم جلب المنتجات بنجاح'
        );

    }

    public function showProduct($id)
    {
        $product = $this->supplierService->getProductById($id);

        if (! $product) {
            return $this->notFoundResponse('المنتج غير موجود');
        }

        return $this->successResponse(new ProductResource($product), 'تم جلب بيانات المنتج');
    }



    public function getAvailableProducts()
    {
        $products = $this->supplierService->getAvailableProducts(Auth::user()->id);

        return $this->paginatedResponse($products, ProductResource::collection($products), 'تم جلب المنتجات المتوفرة بنجاح');
    }

    public function getNearlyOutOfStockProducts()
    {
        $products = $this->supplierService->getNearlyOutOfStockProducts(Auth::user()->id);

        return $this->paginatedResponse($products, ProductResource::collection($products), 'تم جلب المنتجات القريبة من النفاذ بنجاح');
    }

    public function getOutOfStockProducts()
    {
        $products = $this->supplierService->getOutOfStockProducts(Auth::user()->id);

        return $this->paginatedResponse($products, ProductResource::collection($products), 'تم جلب المنتجات المنتهية المخزون بنجاح');
    }

}
