<?php

namespace App\Filament\Resources\AdsResource\Pages;

use App\Filament\Resources\AdsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAds extends ListRecords
{
    protected static string $resource = AdsResource::class;

    public function getTitle(): string
    {
        return 'الإعلانات';
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('إنشاء إعلان'),
        ];
    }
}
