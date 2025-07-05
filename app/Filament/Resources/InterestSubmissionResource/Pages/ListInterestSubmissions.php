<?php

namespace App\Filament\Resources\InterestSubmissionResource\Pages;

use App\Filament\Resources\InterestSubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInterestSubmissions extends ListRecords
{
    protected static string $resource = InterestSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
