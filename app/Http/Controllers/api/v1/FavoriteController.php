<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Buyer\ToggleFavoriteRequest;
use App\Http\Resources\FavoriteProductResource;
use App\Http\Resources\ProductResource;
use App\Services\FavoriteService;
use App\Traits\ApiResponse;

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

    /*
    * Toggle favorite status for a product.
    *
    * @param ToggleFavoriteRequest $request
    * @return JsonResponse
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
     */
    public function index()
    {
        $result = $this->favoriteService->getFavorites(auth()->user());

        return $this->paginatedResponse($result, ProductResource::collection($result), __('messages.favorites_fetched_successfully'));
    }
}
