<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Product;
use App\Models\Factory;
use App\Models\Supplier;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Enums\IconPosition;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('إجمالي التصنيفات', Category::count())
                ->description('عدد التصنيفات')
                ->descriptionIcon('heroicon-m-tag')
                ->color('warning')
                ->url(route('filament.admin.resources.categories.index'))
                ->icon('heroicon-m-tag')
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-warning-50',
                ]),

            Stat::make('إجمالي التصنيفات الفعالة', Category::active()->count())
                ->description('عدد التصنيفات الفعالة')
                ->descriptionIcon('heroicon-m-tag')
                ->color('success')
                ->url(route('filament.admin.resources.categories.index', ['tableFilters[is_active]' => 'true']))
                ->icon('heroicon-m-check-circle')
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-success-50',
                ]),

            Stat::make('إجمالي المنتجات', Product::count())
                ->description('عدد المنتجات')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary')
                ->url(route('filament.admin.resources.products.index'))
                ->icon('heroicon-m-shopping-bag')
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-primary-50',
                ]),

            Stat::make('إجمالي الموردين', Supplier::count())
                ->description('عدد الموردين')
                ->descriptionIcon('heroicon-m-truck')
                ->color('info')
                ->url(route('filament.admin.resources.suppliers.index'))
                ->icon('heroicon-m-truck')
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-info-50',
                ]),
        ];
    }
} 