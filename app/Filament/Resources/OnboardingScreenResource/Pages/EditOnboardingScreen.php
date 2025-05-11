<?php

namespace App\Filament\Resources\OnboardingScreenResource\Pages;

use App\Filament\Resources\OnboardingScreenResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOnboardingScreen extends EditRecord
{
    protected static string $resource = OnboardingScreenResource::class;


    protected static ?string $title = 'تعديل';

    protected static ?string $pluralTitle = 'تعديل';


    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
} 