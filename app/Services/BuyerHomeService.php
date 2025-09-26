<?php

namespace App\Services;

use App\Dtos\BuyerHomeFilterDto;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;

class BuyerHomeService
{
    public function getSuppliersAndProducts(BuyerHomeFilterDto $filters)
    {
        $suppliers = $this->getFilteredSuppliers($filters);
        $products = $this->getFilteredProducts($suppliers, $filters);

        foreach ($suppliers as $supplier) {
            $supplier->setRelation('products',
                $products->get($supplier->id, collect())->take(10)
            );
        }

        return $suppliers;
    }

    private function getFilteredSuppliers(BuyerHomeFilterDto $filters): Collection
    {
        $query = User::role(UserRole::SUPPLIER->value)
            ->where('status', UserStatus::APPROVED)
            ->select('id', 'name', 'image');

        // Filter by category's field
        if ($filters->hasCategoryFilter()) {
            $category = Category::with('field')->findOrFail($filters->categoryId);
            $query->whereHas('fields', function ($q) use ($category) {
                $q->where('field_id', $category->field_id);
            });
        }

        return $query->oldest()->take(4)->get();
    }

    private function getFilteredProducts(Collection $suppliers, BuyerHomeFilterDto $filters): Collection
    {
        $query = Product::withCartInfo()
            ->whereIn('supplier_id', $suppliers->pluck('id'))
            ->published()
            ->with([
                'media',
                'currentUserFavorite',
                'ratings',
                'category',
                'category.field',
                'ratings.user',
            ]);

        // Category filter
        if ($filters->hasCategoryFilter()) {
            $query->where('category_id', $filters->categoryId);
        }

        return $query->oldest()->get()->groupBy('supplier_id');
    }
}
