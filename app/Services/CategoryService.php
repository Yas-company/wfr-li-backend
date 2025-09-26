<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CategoryService
{
    public function index()
    {
        return QueryBuilder::for(Category::class)
            ->allowedFilters([
                AllowedFilter::partial('name'),
                AllowedFilter::exact('field_id'),
            ])
            ->allowedIncludes([
                'products',
                'field',
            ])
            ->allowedSorts([
                'id',
            ])
            ->paginate(10);
    }

    public function getAll()
    {
        return QueryBuilder::for(Category::class)
            ->allowedFilters([
                AllowedFilter::partial('name'),
            ])
            ->defaultSort('id')
            ->get();
    }

    public function show(Category $category)
    {
        return $category->load('field', 'products')->loadCount('products');
    }

    public function getSupplierCategories(User $user)
    {
        $fields = $user->fields;

        return Category::whereIn('field_id', $fields->pluck('id'))->get();
    }
}
