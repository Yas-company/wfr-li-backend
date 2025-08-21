<?php

namespace App\Http\Services;

use App\Models\Category;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;

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

    public function show(Category $category)
    {
        return $category->load('field', 'products')->loadCount('products');
    }
}
