<?php


// app/Http/Services/ProductService.php
namespace App\Http\Services;

use App\Http\Services\Contracts\ProductServiceInterface;
use App\Models\Product;
use Carbon\Carbon;

class ProductService implements ProductServiceInterface
{
    public function list(array $filters)
    {
        return Product::when(isset($filters['category_id']), function ($query) use ($filters) {
            $query->where('category_id', $filters['category_id']);
        })->latest()->paginate(10);
    }

    public function create(array $data)
    {
        $data['supplier_id'] = 1;

        // تخزين صورة واحدة فقط إذا كانت موجودة
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $data['image'] = $data['image']->store('products', 'public'); // path: storage/app/public/products
        }

        return Product::create($data);
    }


    public function update(int $id, array $data)
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        return $product;
    }

    public function delete(int $id)
    {
        $product = Product::findOrFail($id);
        return $product->delete();
    }

    public function countTotal(): int
    {
        return Product::count();
    }

    public function countExpired(): int
    {
        return Product::where('stock_qty', 0)->count();
    }

    public function countNearExpiry(): int
    {
        return Product::where('stock_qty', '<', 5)->count(); // مثال: أقل من 5
    }

    public function countStockStatuses(): array
    {
        return [
            'expired' => Product::where('stock_qty', 0)->count(),
            'near_expiry' => Product::where('stock_qty', '<', 5)->where('stock_qty', '>', 0)->count(),
        ];
    }
}
