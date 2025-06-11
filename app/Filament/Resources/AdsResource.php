<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdsResource\Pages;
use App\Models\Ads;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AdsResource extends Resource
{
    protected static ?string $model = Ads::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'الإعلانات';

    protected static ?string $navigationLabel = 'الإعلانات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الإعلان')
                    ->schema([
                        Forms\Components\TextInput::make('title.en')
                            ->label('العنوان بالانجليزية')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('title.ar')
                            ->label('العنوان بالعربية')
                            ->required()
                            ->maxLength(255),
                            Forms\Components\FileUpload::make('image')
                            ->label('الصورة')
                            ->image()
                            ->disk('public') // Store in storage/app/public
                            ->directory('ads') // Subdirectory for organization
                            ->visibility('public')
                            ->imageEditor() // Optional: Enable image cropping/editing
                            ->maxSize(2048) // Optional: Limit file size to 2MB
                            ->required(),
                        Forms\Components\Select::make('supplier_id')
                            ->label('المورد')
                            ->relationship('supplier', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true)
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
                ->columns([
                Tables\Columns\TextColumn::make('title.en')
                    ->label('العنوان بالانجليزية')
                    ->getStateUsing(fn($record) => $record->getTranslation('title', 'en'))
                    ->searchable(query: fn($query, $search) => $query->where('title->en', 'like', "%{$search}%")),

                Tables\Columns\TextColumn::make('title.ar')
                    ->label('العنوان بالعربية')
                    ->getStateUsing(fn($record) => $record->getTranslation('title', 'ar'))
                    ->searchable(query: fn($query, $search) => $query->where('title->ar', 'like', "%{$search}%")),

                Tables\Columns\ImageColumn::make('image_url')
                    ->label('الصورة'),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('المورد')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
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
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('نشط'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAds::route('/'),
            'create' => Pages\CreateAds::route('/create'),
            'edit' => Pages\EditAds::route('/{record}/edit'),
        ];
    }
} 