<?php

namespace App\Filters;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;


class JsonColumnFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $locale = app()->getLocale();
        $jsonPath = "{$property}->{$locale}";

        $query->where($jsonPath, 'LIKE', "{$value}%");
    }
}
