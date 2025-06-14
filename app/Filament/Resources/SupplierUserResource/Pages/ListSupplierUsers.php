<?php

namespace App\Filament\Resources\SupplierUserResource\Pages;

use App\Filament\Resources\SupplierUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSupplierUsers extends ListRecords
{
    protected static string $resource = SupplierUserResource::class;


    public function getTitle(): string
    {
        return 'الموردين';
    }
} 