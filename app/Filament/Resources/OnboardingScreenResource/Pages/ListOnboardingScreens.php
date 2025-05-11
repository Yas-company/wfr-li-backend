<?php

namespace App\Filament\Resources\OnboardingScreenResource\Pages;

use App\Filament\Resources\OnboardingScreenResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOnboardingScreens extends ListRecords
{
    protected static string $resource = OnboardingScreenResource::class;

    protected static ?string $title = 'الشاشات الاولية';

    protected static ?string $pluralTitle = 'الشاشات الاولية';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('إضافة شاشة جديدة'),
        ];
    }
} 