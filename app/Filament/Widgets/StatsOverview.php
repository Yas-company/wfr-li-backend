<?php

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
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
                ->color('warning')
                ->url(route('filament.admin.resources.categories.index'))
                ->icon('heroicon-m-tag')
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-warning-50',
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

            Stat::make('إجمالي الموردين', User::where('role', UserRole::SUPPLIER)->count())
                ->description('عدد الموردين')
                ->descriptionIcon('heroicon-m-truck')
                ->color('info')
                ->icon('heroicon-m-truck')
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-info-50',
                ]),

            Stat::make('إجمالي المشترين', User::where('role', UserRole::BUYER)->count())
                ->description('عدد المشترين')
                ->descriptionIcon('heroicon-m-user')
                ->color('danger')
                ->icon('heroicon-m-user')
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-danger-50',
                ]),
        ];
    }
} 