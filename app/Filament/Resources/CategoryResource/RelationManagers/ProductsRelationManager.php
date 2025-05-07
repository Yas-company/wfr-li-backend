<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = 'المنتجات';

    protected static ?string $pluralTitle = 'المنتجات';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name.en')
                    ->label('الاسم بالانجليزية')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name.ar')
                    ->label('الاسم بالعربية')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('image')
                    ->label('الصورة')
                    ->image()
                    ->disk('public')
                    ->directory('products')
                    ->visibility('public')
                    ->imageEditor()
                    ->maxSize(2048)
                    ->required(),
                Forms\Components\TextInput::make('price')
                    ->label('السعر')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0),
                Forms\Components\TextInput::make('stock_qty')
                    ->label('الكمية المتوفرة')
                    ->required()
                    ->numeric()
                    ->minValue(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_en')
                    ->label('الاسم بالانجليزية')
                    ->getStateUsing(fn($record) => $record->getTranslation('name', 'en'))
                    ->searchable(query: fn($query, $search) => $query->where('name->en', 'like', "%{$search}%")),
                Tables\Columns\TextColumn::make('name_ar')
                    ->label('الاسم بالعربية')
                    ->getStateUsing(fn($record) => $record->getTranslation('name', 'ar'))
                    ->searchable(query: fn($query, $search) => $query->where('name->ar', 'like', "%{$search}%")),
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('الصورة')
                    ->square(),
                Tables\Columns\TextColumn::make('price')
                    ->label('السعر')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_qty')
                    ->label('الكمية المتوفرة')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('تاريخ التحديث')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('إضافة منتج'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('تعديل'),
                Tables\Actions\DeleteAction::make()
                    ->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('حذف المحدد'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
} 