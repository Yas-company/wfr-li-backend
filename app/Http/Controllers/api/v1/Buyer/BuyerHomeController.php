<?php

namespace App\Http\Controllers\api\v1\Buyer;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\Buyer\HomePageBuyerResource;
use App\Models\User;
use App\Traits\ApiResponse;

class BuyerHomeController extends Controller
{
    use ApiResponse;
    /**
     * Get the suppliers and products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSuppliersAndProducts()
    {
        $data = User::role(UserRole::SUPPLIER->value)
            ->with(['products' => function ($query) {
                $query
                    ->latest()
                    ->take(10)
                    ->with([
                        'media',
                        'currentUserFavorite',
                        'ratings'
                    ])
                    ->withAvg('ratings', 'rating');
            }])
            ->select('id', 'name', 'image')
            ->latest()
            ->take(4)
            ->get();

        return $this->successResponse(HomePageBuyerResource::collection($data));
    }
}
