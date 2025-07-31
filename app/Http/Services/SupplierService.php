<?php

namespace App\Http\Services;

use App\Http\Services\Contracts\SupplierServiceInterface;
use App\Models\Ads;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierService implements SupplierServiceInterface
{
    public function getAds(int $supplierId)
    {
        return Ads::where('user_id', $supplierId)->where('is_active', true)->get();
    }

    public function getCategories(int $supplierId)
    {
        return Category::where('supplier_id', $supplierId)->get();
    }

    public function getProducts(array $filters): LengthAwarePaginator
    {
        $query = Product::query()
            ->where('supplier_id', $filters['supplier_id'])
            ->isActive()
            ->with(['category', 'currentUserFavorite', 'ratings']);

        // ✅ البحث في الاسم فقط
        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name->ar', 'like', '%'.$filters['search'].'%')
                    ->orWhere('name->en', 'like', '%'.$filters['search'].'%');
            });
        }

        // ✅ فلتر حسب القسم فقط لو متبعت
        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        return $query->latest()->paginate(10);
    }

    public function getProductById(int $id): ?Product
    {
        return Product::where('id', $id)
            ->where('is_active', true)
            ->with(['category', 'currentUserFavorite', 'ratings'])
            ->first();
    }

 

}
