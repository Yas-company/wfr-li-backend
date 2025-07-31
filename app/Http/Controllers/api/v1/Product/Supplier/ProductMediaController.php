<?php

namespace App\Http\Controllers\api\v1\Product\Supplier;

use App\Models\Product;
use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Requests\Products\AttachMediaRequest;
use App\Services\Contracts\ProductServiceInterface;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductMediaController extends Controller
{
    use ApiResponse;

    /**
     * ProductMediaController constructor.
     *
     * @param ProductServiceInterface $productService
     */
    public function __construct(protected ProductServiceInterface $productService) {}

    /**
     * Store a newly created resource in storage.
     *
     * @param AttachMediaRequest $request
     * @param Product $product
     *
     * @return JsonResponse
     */
    public function store(AttachMediaRequest $request, Product $product)
    {
        $product = $this->productService->attachMedia($product);

        return $this->successResponse(new ProductResource($product), 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Product $product
     * @param Media $media
     *
     * @return JsonResponse
     */
    public function destroy(Product $product, Media $media)
    {
        $this->productService->removeMedia($product, $media);

        return $this->successResponse(null, 'Media deleted successfully');
    }

}
