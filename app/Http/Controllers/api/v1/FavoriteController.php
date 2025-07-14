<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FavoriteProductResource;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Services\FavoriteService;
use App\Http\Requests\ToggleFavoriteRequest;
class FavoriteController extends Controller
{
    use ApiResponse;
    protected $favoriteService;
    public function __construct(FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
    }
    public function toggleFavorite(ToggleFavoriteRequest $request)
    {
        $result = $this->favoriteService->toggleFavorite($request->all());
        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 400);
        }
        return $this->successResponse(new FavoriteProductResource($result), 'Favorite status updated successfully');
    }
    public function index()
    {
        $result = $this->favoriteService->getFavorites();
        return $this->successResponse(ProductResource::collection($result), 'Favorites fetched successfully');
    }
}
