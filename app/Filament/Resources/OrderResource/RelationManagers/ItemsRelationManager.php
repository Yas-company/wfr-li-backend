<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Read-only, so no form fields
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->label('Product / المنتج'),
                Tables\Columns\TextColumn::make('quantity')->label('Quantity / الكمية'),
                Tables\Columns\TextColumn::make('price')->label('Price / السعر')->money('SAR'),
                Tables\Columns\TextColumn::make('total')->label('Total / المجموع')->money('SAR'),
            ])
            ->filters([
                //
            ])
            ->headerActions([]) // No create
            ->actions([]) // No edit/delete
            ->bulkActions([]); // No bulk delete
    }
}
