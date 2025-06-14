<?php

namespace App\Filament\Resources\BuyerUserResource\Pages;

use App\Filament\Resources\BuyerUserResource;
use Filament\Resources\Pages\ListRecords;

class ListBuyerUsers extends ListRecords
{
    protected static string $resource = BuyerUserResource::class;

    public function getTitle(): string
    {
        return 'المشترين';
    }
} 