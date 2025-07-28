<?php

namespace App\Http\Controllers\api\v1\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\AttachMediaRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\Contracts\ProductServiceInterface;
use App\Traits\ApiResponse;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductMediaController extends Controller
{
    use ApiResponse;

    /**
     * ProductMediaController constructor.
     */
    public function __construct(protected ProductServiceInterface $productService) {}

    /**
     * Store a newly created resource in storage.
     *
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
     *
     * @return JsonResponse
     */
    public function destroy(Product $product, Media $media)
    {
        $this->productService->removeMedia($product, $media);

        return $this->successResponse(null, 'Media deleted successfully');
    }
}
