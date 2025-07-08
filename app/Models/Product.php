<?php

namespace App\Models;

use App\Enums\ProductStatus;
use App\Enums\UnitType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $guarded = [];

    public $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'image',
        'price',
        'quantity',
        'min_order_quantity', // ✅ أضفناه هنا
        'stock_qty',
        'unit_type',
        'status',
        'is_active',
        'category_id',
        'supplier_id',
    ];

    protected $casts = [
        'name' => 'array',
        'price' => 'decimal:2',
        'stock_qty' => 'integer',
        'price_before_discount' => 'decimal:2',
        'description' => 'array',
        'unit_type' => UnitType::class,
        'status' => ProductStatus::class,
    ];

    protected $with = ['category', 'supplier'];

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return asset('images/logo.jpeg');
        }

        return asset('storage/' . $this->image);
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function getIsFavoriteAttribute()
    {
        $user = Auth::user();
        if (!$user) return false;
        return $this->favoritedByUsers()->where('user_id', $user->id)->exists();
    }

    public function scopeFilterAndSearch($query, $params)
    {
        // Search
        if (!empty($params['q'])) {
            $search = $params['q'];
            $query->where(function ($q) use ($search) {
                $q->where('name->en', 'LIKE', "%{$search}%")
                    ->orWhere('name->ar', 'LIKE', "%{$search}%")
                    ->orWhere('description->en', 'LIKE', "%{$search}%")
                    ->orWhere('description->ar', 'LIKE', "%{$search}%");
            });
        }

        // Filter by category
        if (!empty($params['category_id'])) {
            $query->where('category_id', $params['category_id']);
        }

        // Filter by price range
        if (!empty($params['min_price'])) {
            $query->where('price', '>=', $params['min_price']);
        }
        if (!empty($params['max_price'])) {
            $query->where('price', '<=', $params['max_price']);
        }

        return $query;
    }

    public function related($limit = 6)
    {
        return self::where('category_id', $this->category_id)
            ->where('id', '!=', $this->id)
            ->with('category')
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function offers()
{
    return $this->belongsToMany(Offer::class, 'offer_products');
}

}
