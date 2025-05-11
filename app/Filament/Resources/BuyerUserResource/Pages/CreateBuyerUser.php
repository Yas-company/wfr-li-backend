<?php

namespace App\Filament\Resources\BuyerUserResource\Pages;

use App\Filament\Resources\BuyerUserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBuyerUser extends CreateRecord
{
    protected static string $resource = BuyerUserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['role'] = 'buyer';
        return $data;
    }
} 