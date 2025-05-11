<?php

namespace App\Filament\Resources\BuyerUserResource\Pages;

use App\Filament\Resources\BuyerUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBuyerUsers extends ListRecords
{
    protected static string $resource = BuyerUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
} 