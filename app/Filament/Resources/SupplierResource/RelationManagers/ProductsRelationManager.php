<?php

namespace App\Filament\Resources\SupplierResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Product;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('products')
                    ->label('المنتجات')
                    ->multiple()
                    ->options(Product::whereDoesntHave('suppliers', function ($query) {
                        $query->where('supplier_id', $this->getOwnerRecord()->id);
                    })->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->afterStateUpdated(function ($state, $set) {
                        if (is_array($state)) {
                            $this->getOwnerRecord()->products()->attach($state);
                        }
                    }),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('السعر')
                    ->money('SAR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_qty')
                    ->label('الكمية المتوفرة')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('الصورة')
                    ->circular(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('إضافة منتجات')
                    ->multiple()
                    ->preloadRecordSelect()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('المنتجات')
                            ->multiple()
                            ->required(),
                    ]),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->label('حذف المنتج'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                        ->label('حذف المنتجات المحددة'),
                ]),
            ]);
    }
}
