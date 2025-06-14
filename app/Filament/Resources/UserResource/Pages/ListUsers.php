<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Enums\UserRole;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('الكل')
                ->badge(User::count()),
            'admin' => Tab::make('المدراء')
                ->badge(User::where('role', UserRole::ADMIN)->count())
                ->modifyQueryUsing(fn ($query) => $query->where('role', UserRole::ADMIN)),
            'buyer' => Tab::make('المشترين')
                ->badge(User::where('role', UserRole::BUYER)->count())
                ->modifyQueryUsing(fn ($query) => $query->where('role', UserRole::BUYER)),
            'supplier' => Tab::make('الموردين')
                ->badge(User::where('role', UserRole::SUPPLIER)->count())
                ->modifyQueryUsing(fn ($query) => $query->where('role', UserRole::SUPPLIER)),
        ];
    }
}
