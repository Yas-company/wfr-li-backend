<?php

namespace App\Filament\Resources\OfferNotificationResource\Pages;

use App\Filament\Resources\OfferNotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOfferNotification extends CreateRecord
{
    protected static string $resource = OfferNotificationResource::class;
    protected static ?string $title = 'إنشاء إشعار عرض';
    /**
     * Redirect to the list page after successful creation
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Customize the success notification message
     */
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'تم إنشاء إشعار العرض بنجاح';
    }
}
