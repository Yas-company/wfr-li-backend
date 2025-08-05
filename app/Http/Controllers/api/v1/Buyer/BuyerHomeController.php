<?php

namespace App\Http\Controllers\api\v1\Buyer;

use App\Enums\ProductStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Buyer\HomePageBuyerResource;
use App\Models\Product;
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
        $suppliers = User::role(UserRole::SUPPLIER->value)
            ->where('status', UserStatus::APPROVED)
            ->select('id', 'name', 'image')
            ->latest()
            ->take(4)
            ->get();

        $allProducts = Product::whereIn('supplier_id', $suppliers->pluck('id'))
            ->where('status', ProductStatus::PUBLISHED)
            ->with([
                'media',
                'currentUserFavorite',
                'ratings',
                'category',
                'category.field',
                'ratings.user'
            ])
            ->latest() 
            ->get()
            ->groupBy('supplier_id');

        // Attach top 10 products per supplier
        foreach ($suppliers as $supplier) {
            $supplier->setRelation('products',
                $allProducts->get($supplier->id, collect())->take(10)
            );
        }

        return $this->successResponse(HomePageBuyerResource::collection($suppliers));
    }
}
