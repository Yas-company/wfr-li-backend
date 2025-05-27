<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'ادارة المصانع';

    protected static ?string $navigationLabel = 'الموردين';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات المورد')
                    ->schema([
                        Forms\Components\Select::make('factory_id')
                            ->label('المصنع')
                            ->relationship('factory', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name.en')
                            ->label('الاسم بالانجليزية')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name.ar')
                            ->label('الاسم بالعربية')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('الهاتف')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\Textarea::make('address')
                            ->label('العنوان')
                            ->required()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),
                Forms\Components\Section::make('الموقع')
                    ->schema([
                        Forms\Components\TextInput::make('location_link')
                            ->label('Location Link')
                            ->placeholder('Paste Google Maps or Apple Maps link here')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Google Maps
                                if (preg_match('/@(-?\\d+\\.\\d+),(-?\\d+\\.\\d+)/', $state, $matches)) {
                                    $set('latitude', $matches[1]);
                                    $set('longitude', $matches[2]);
                                } elseif (preg_match('/\/place\/(-?\\d+\\.\\d+),(-?\\d+\\.\\d+)/', $state, $matches)) {
                                    $set('latitude', $matches[1]);
                                    $set('longitude', $matches[2]);
                                }
                                // Apple Maps
                                elseif (preg_match('/coordinate=([\\d\\.\\-]+),([\\d\\.\\-]+)/', $state, $matches)) {
                                    $set('latitude', $matches[1]);
                                    $set('longitude', $matches[2]);
                                }
                            }),
                        Forms\Components\TextInput::make('latitude')
                            ->label('خط العرض')
                            ->numeric()
                            ->minValue(-90)
                            ->maxValue(90),
                        Forms\Components\TextInput::make('longitude')
                            ->label('خط الطول')
                            ->numeric()
                            ->minValue(-180)
                            ->maxValue(180),
                    ])->columns(3),
                Forms\Components\Section::make('الأمان')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('كلمة المرور')
                            ->password()
                            ->required()
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_verified')
                            ->label('تم التحقق')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('factory.name')
                    ->label('المصنع')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name_en')
                ->label('الاسم بالانجليزية')
                ->getStateUsing(fn($record) => $record->getTranslation('name', 'en'))
                ->searchable(query: fn($query, $search) => $query->where('name->en', 'like', "%{$search}%")),

            // Display Arabic name
            Tables\Columns\TextColumn::make('name_ar')
                ->label('الاسم بالعربية')
                ->getStateUsing(fn($record) => $record->getTranslation('name', 'ar'))
                ->searchable(query: fn($query, $search) => $query->where('name->ar', 'like', "%{$search}%")),
                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف')
                    ->searchable(),
                Tables\Columns\TextColumn::make('latitude')
                    ->label('خط العرض')
                    ->searchable(),
                Tables\Columns\TextColumn::make('longitude')
                    ->label('خط الطول')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('التحقق')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_verified'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (Auth::guard('factory')->check()) {
            $factory = Auth::guard('factory')->user();
            return $query->where('factory_id', $factory->id);
        }

        return $query;
    }
}
