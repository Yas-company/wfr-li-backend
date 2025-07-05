<?php

namespace App\Filament\Resources\InterestSubmissionResource\Pages;

use App\Filament\Resources\InterestSubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInterestSubmission extends EditRecord
{
    protected static string $resource = InterestSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
