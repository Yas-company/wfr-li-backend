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

    protected static ?string $navigationGroup = ' المنتجات';

    protected static ?string $navigationLabel = 'المنتجات';

    protected static ?string $pluralNavigationLabel = 'المنتجات';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('تفاصيل المنتج')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('المعلومات الأساسية')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\TextInput::make('name.en')
                                    ->label('الاسم (بالانجليزية)')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('name.ar')
                                    ->label('الاسم (بالعربية)')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description.en')
                                    ->label('الوصف (بالانجليزية)')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description.ar')
                                    ->label('الوصف (بالعربية)')
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
                                Forms\Components\Toggle::make('is_featured')
                                    ->label('مميز')
                                    ->default(false),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('السعر والمخزون')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Forms\Components\TextInput::make('price_before_discount')
                                    ->label('السعر قبل الخصم')
                                    ->numeric()
                                    ->prefix('ر.س')
                                    ->minValue(0),
                                Forms\Components\TextInput::make('price')
                                    ->label('السعر')
                                    ->required()
                                    ->numeric()
                                    ->prefix('ر.س')
                                    ->minValue(0),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('الكمية')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0),
                                Forms\Components\TextInput::make('min_order_quantity')
                                    ->label('الحد الأدنى للطلب')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1),
                                Forms\Components\TextInput::make('stock_qty')
                                    ->label('الكمية المتوفرة')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0),
                                Forms\Components\TextInput::make('nearly_out_of_stock_limit')
                                    ->label('حد تنبيه نفاد المخزون')
                                    ->numeric()
                                    ->minValue(0)
                                    ->helperText('سيتم إرسال تنبيه عندما تصل الكمية لهذا الحد'),
                                Forms\Components\Select::make('unit_type')
                                    ->label('نوع الوحدة')
                                    ->options(\App\Enums\UnitType::getOptions())
                                    ->required()
                                    ->default(\App\Enums\UnitType::PIECE->value)
                                    ->searchable(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('التصنيفات والموردين')
                            ->icon('heroicon-o-tag')
                            ->schema([
                                Forms\Components\Select::make('category_id')
                                    ->label('التصنيف')
                                    ->options(function ($get, $record) {
                                        $supplierId = $get('supplier_id');
                                        $options = collect();

                                        if ($supplierId) {
                                            // Get categories for selected supplier
                                            $options = \App\Models\Category::where('supplier_id', $supplierId)
                                                ->get()
                                                ->mapWithKeys(function ($category) {
                                                    return [$category->id => $category->getTranslation('name', app()->getLocale())];
                                                });
                                        }

                                        // Always include current category when editing (if exists)
                                        if ($record && $record->category_id) {
                                            $currentCategory = \App\Models\Category::find($record->category_id);
                                            if ($currentCategory) {
                                                $options->put($currentCategory->id, $currentCategory->getTranslation('name', app()->getLocale()));
                                            }
                                        }

                                        return $options;
                                    })
                                    ->required()
                                    ->searchable()
                                    ->reactive(),
                                Forms\Components\Select::make('supplier_id')
                                    ->label('المورد')
                                    ->relationship('supplier', 'name', function ($query) {
                                        return $query->where('role', \App\Enums\UserRole::SUPPLIER);
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set) {
                                        // Clear category when supplier changes
                                        $set('category_id', null);
                                    }),
                            ])->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                // Display English name
                Tables\Columns\TextColumn::make('name_en')
                    ->label('الاسم بالانجليزية')
                    ->getStateUsing(fn ($record) => $record->getTranslation('name', 'en'))
                    ->searchable(query: fn ($query, $search) => $query->where('name->en', 'like', "%{$search}%")),

                // Display Arabic name
                Tables\Columns\TextColumn::make('name_ar')
                    ->label('الاسم بالعربية')
                    ->getStateUsing(fn ($record) => $record->getTranslation('name', 'ar'))
                    ->searchable(query: fn ($query, $search) => $query->where('name->ar', 'like', "%{$search}%")),

                Tables\Columns\TextColumn::make('description_en')
                    ->label('الوصف بالانجليزية')
                    ->getStateUsing(fn ($record) => $record->getTranslation('description', 'en'))
                    ->searchable(query: fn ($query, $search) => $query->where('description->en', 'like', "%{$search}%")),

                Tables\Columns\TextColumn::make('description_ar')
                    ->label('الوصف بالعربية')
                    ->getStateUsing(fn ($record) => $record->getTranslation('description', 'ar'))
                    ->searchable(query: fn ($query, $search) => $query->where('description->ar', 'like', "%{$search}%")),

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
                Tables\Columns\TextColumn::make('quantity')
                    ->label('الكمية')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_order_quantity')
                    ->label('الحد الأدنى للطلب')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_qty')
                    ->label('الكمية المتوفرة')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nearly_out_of_stock_limit')
                    ->label('حد تنبيه المخزون')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('unit_type')
                    ->label('نوع الوحدة')
                    ->getStateUsing(fn ($record) => $record->unit_type?->label())
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('التصنيف')
                    ->getStateUsing(fn ($record) => $record->category?->getTranslation('name', app()->getLocale()))
                    ->searchable(query: fn ($query, $search) => $query->whereHas('category', function ($q) use ($search) {
                        $q->where('name->'.app()->getLocale(), 'like', "%{$search}%");
                    }))
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('المورد')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('مميز')
                    ->boolean(),
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
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTranslation('name', app()->getLocale()))
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('supplier')
                    ->relationship('supplier', 'name')
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
