<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'التصنيفات والمنتجات';

    protected static ?string $navigationLabel = 'المنتجات';

    protected static ?string $pluralNavigationLabel = 'المنتجات';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Product Information')
                    ->schema([
                        Forms\Components\TextInput::make('name.en')
                            ->label('Name (English)')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name.ar')
                            ->label('Name (Arabic)')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description.en')
                            ->label('Description (English)')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description.ar')
                            ->label('Description (Arabic)')
                            ->required()
                            ->maxLength(255),
                            Forms\Components\FileUpload::make('image')
                            ->label('Image')
                            ->image()
                            ->disk('public') // Store in storage/app/public
                            ->directory('products') // Subdirectory for organization
                            ->visibility('public')
                            ->imageEditor() // Optional: Enable image cropping/editing
                            ->maxSize(2048) // Optional: Limit file size to 2MB
                            ->required(),
                            Forms\Components\TextInput::make('price_before_discount')
                            ->required()
                            ->numeric()
                            ->prefix('ر.س')
                            ->minValue(0),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('ر.س')
                            ->minValue(0),
                        Forms\Components\TextInput::make('stock_qty')
                            ->required()
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('factory_id')
                            ->relationship('factory', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('factory.name')
                    ->label('المصنع')
                    ->sortable(),
                
                // Display English name
                Tables\Columns\TextColumn::make('name_en')
                ->label('الاسم بالانجليزية')
                ->getStateUsing(fn($record) => $record->getTranslation('name', 'en'))
                ->searchable(query: fn($query, $search) => $query->where('name->en', 'like', "%{$search}%")),

            // Display Arabic name
            Tables\Columns\TextColumn::make('name_ar')
                ->label('الاسم بالعربية')
                ->getStateUsing(fn($record) => $record->getTranslation('name', 'ar'))
                ->searchable(query: fn($query, $search) => $query->where('name->ar', 'like', "%{$search}%")),


                Tables\Columns\TextColumn::make('description_en')
                    ->label('الوصف بالانجليزية')
                    ->getStateUsing(fn($record) => $record->getTranslation('description', 'en'))
                    ->searchable(query: fn($query, $search) => $query->where('description->en', 'like', "%{$search}%")),

                Tables\Columns\TextColumn::make('description_ar')
                    ->label('الوصف بالعربية')
                    ->getStateUsing(fn($record) => $record->getTranslation('description', 'ar'))
                    ->searchable(query: fn($query, $search) => $query->where('description->ar', 'like', "%{$search}%")),

                Tables\Columns\ImageColumn::make('image_url')
                    ->label('الصورة')
                    ->square(),
                Tables\Columns\TextColumn::make('price_before_discount')
                    ->label('السعر قبل الخصم')
                    ->money('SAR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('السعر')
                    ->money('SAR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_qty')
                    ->label('الكمية')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('التصنيف')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('التاريخ')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
} 