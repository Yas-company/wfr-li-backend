<?php

namespace App\Filament\Resources\SupplierUserResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'categories';

    protected static ?string $title = 'التصنيفات';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                     // Display English name
                     Tables\Columns\TextColumn::make('name_en')
                     ->label('الاسم بالانجليزية')
                     ->getStateUsing(fn($record) => $record->getTranslation('name', 'en'))
                     ->searchable(query: fn($query, $search) => $query->where('name->en', 'like', "%{$search}%")),
 
                 // Display Arabic name
                 Tables\Columns\TextColumn::make('name_ar')
                     ->label('الاسم بالعربية')
                     ->getStateUsing(fn($record) => $record->getTranslation('name', 'ar'))
                     ->searchable(query: fn($query, $search) => $query->where('name->ar', 'like', "%{$search}%"))
            ])
            ->headerActions([
                // You can add actions here if needed
            ])
            ->actions([
                // You can add row actions here if needed
            ])
            ->bulkActions([
                // You can add bulk actions here if needed
            ]);
    }
} 