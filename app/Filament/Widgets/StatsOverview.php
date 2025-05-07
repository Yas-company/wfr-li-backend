<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('إجمالي التصنيفات', Category::count())
                ->description('عدد التصنيفات')
                ->descriptionIcon('heroicon-m-tag')
                ->color('warning'),


                Stat::make('إجمالي التصنيفات الفعالة', Category::active()->count())
                ->description('عدد التصنيفات الفعالة')
                ->descriptionIcon('heroicon-m-tag')
                ->color('success'),


            Stat::make('إجمالي المنتجات', Product::count())
                ->description('عدد المنتجات')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary'),
        ];
    }
} 