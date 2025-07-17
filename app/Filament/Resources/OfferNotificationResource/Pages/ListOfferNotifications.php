<?php

namespace App\Filament\Resources\OfferNotificationResource\Pages;

use App\Filament\Resources\OfferNotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOfferNotifications extends ListRecords
{
    protected static string $resource = OfferNotificationResource::class;
    protected static ?string $title = 'إشعارات العروض';
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

}
