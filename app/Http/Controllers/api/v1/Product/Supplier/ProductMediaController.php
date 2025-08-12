<?php

namespace App\Http\Controllers\api\v1\Product\Supplier;

use App\Models\Product;
use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Requests\Products\AttachMediaRequest;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
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
     * Store product media (images).
     *
     * @param AttachMediaRequest $request
     * @param Product $product
     *
     * @return JsonResponse
     * @OA\Post(
     *     path="/supplier/products/{product}/attach-media",
     *     summary="Attach media to product",
     *     tags={"Supplier Products"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="product", in="path", required=true, description="Product ID", @OA\Schema(type="integer", example=1)),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Upload 1 to 5 images (jpeg, png, jpg, gif, webp; max 2MB each)",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"images[]"},
     *                 @OA\Property(
     *                     property="images[]",
     *                     description="Array of image files",
     *                     type="array",
     *                     minItems=1,
     *                     maxItems=5,
     *                     @OA\Items(
     *                         type="string",
     *                         format="binary",
     *                         description="Image file (jpeg, png, jpg, gif, webp; max 2MB)"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Media attached successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/ProductResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated"),
     *             @OA\Property(property="errors", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={
     *                 "images": {"validation.array"},
     *                 "images.0": {"The images.0 must be an image."}
     *             })
     *         )
     *     )
     * )
     */
    public function store(AttachMediaRequest $request, Product $product)
    {
        $product = $this->productService->attachMedia($product);

        return $this->successResponse(new ProductResource($product), 'Product updated successfully');
    }

    /**
     * Remove a specific media item from product.
     *
     * @param Product $product
     * @param Media $media
     *
     * @return JsonResponse
     * @OA\Delete(
     *     path="/supplier/products/{product}/media/{media}",
     *     summary="Delete product media",
     *     tags={"Supplier Products"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="product", in="path", required=true, description="Product ID", @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="media", in="path", required=true, description="Media ID", @OA\Schema(type="integer", example=10)),
     *     @OA\Response(
     *         response=200,
     *         description="Media deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Media deleted successfully"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Product or media not found")
     * )
     */
    public function destroy(Product $product, Media $media)
    {
        $this->productService->removeMedia($product, $media);

        return $this->successResponse(null, 'Media deleted successfully');
    }
}
