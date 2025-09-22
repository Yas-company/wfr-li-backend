<?php

namespace App\Filament\Resources\TaxResource\Pages;

use Filament\Actions;
use App\Jobs\UpdateProductsPricingJob;
use App\Filament\Resources\TaxResource;
use Filament\Resources\Pages\EditRecord;

class EditTax extends EditRecord
{
    protected static string $resource = TaxResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        UpdateProductsPricingJob::dispatch();
    }
}
