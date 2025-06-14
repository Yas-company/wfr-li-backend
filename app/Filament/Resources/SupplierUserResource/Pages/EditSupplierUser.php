<?php

namespace App\Filament\Resources\SupplierUserResource\Pages;

use App\Filament\Resources\SupplierUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSupplierUser extends EditRecord
{
    protected static string $resource = SupplierUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
} 