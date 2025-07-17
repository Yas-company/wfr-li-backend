<?php

namespace App\Filament\Resources\OfferNotificationResource\Pages;

use App\Filament\Resources\OfferNotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOfferNotification extends EditRecord
{
    protected static string $resource = OfferNotificationResource::class;
    protected static ?string $title = 'تعديل إشعار العرض';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
