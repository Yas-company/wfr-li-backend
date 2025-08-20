<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ToggleFavoriteRequest;
use App\Http\Resources\FavoriteProductResource;
use App\Http\Resources\ProductResource;
use App\Services\FavoriteService;
use App\Traits\ApiResponse;
use OpenApi\Annotations as OA;
class FavoriteController extends Controller
{
    use ApiResponse;

    /*
    * FavoriteController constructor.
    *
    * @param FavoriteService $favoriteService
    */
    public function __construct(private FavoriteService $favoriteService)
    {
        //
    }

    /**
     * Toggle favorite status for a product.
     *
     * @param ToggleFavoriteRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/favorites/toggle",
     *     summary="Toggle favorite status for a product",
     *     description="Add or remove a product from the authenticated user's favorites.",
     *     tags={"Favorites"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id","is_favorite"},
     *             @OA\Property(property="product_id", type="integer", example=1, description="ID of the product"),
     *             @OA\Property(property="is_favorite", type="boolean", example=true, description="true to add, false to remove")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,    
     *         description="Favorite status updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Favorite status updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/FavoriteProductResource")
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
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={
     *                 "product_id": {"The product id field is required."},
     *                 "is_favorite": {"The is favorite field is required."}
     *             })
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Product not found"),
     *             @OA\Property(property="errors", type="null", example=null)
     *         )
     *     )   
     * )
     *  
     */
    public function toggleFavorite(ToggleFavoriteRequest $request)
    {
        $result = $this->favoriteService->toggleFavorite($request->all(), auth()->user());

        return $this->successResponse(new FavoriteProductResource($result), __('messages.favorite_status_updated_successfully'));
    }

    /**
     * Get all favorites.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/favorites",
     *     summary="Get all favorites",
     *     description="Retrieve a paginated list of the authenticated user's favorite products.",
     *     tags={"Favorites"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(        
     *         response=200,
     *         description="Favorites fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Favorites fetched successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/ProductResource")
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string", nullable=true, example="https://api.example.com/favorites?page=1"),
     *                 @OA\Property(property="last", type="string", nullable=true, example="https://api.example.com/favorites?page=3"),
     *                 @OA\Property(property="next", type="string", nullable=true, example="https://api.example.com/favorites?page=2"),
     *                 @OA\Property(property="prev", type="string", nullable=true, example=null)
     *             )
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
     *     )        
     * )    
     */
    public function index()
    {
        $result = $this->favoriteService->getFavorites(auth()->user());

        return $this->paginatedResponse($result, ProductResource::collection($result), __('messages.favorites_fetched_successfully'));
    }
}
