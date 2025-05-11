<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OnboardingScreenResource\Pages;
use App\Models\OnboardingScreen;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OnboardingScreenResource extends Resource
{
    protected static ?string $model = OnboardingScreen::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?string $navigationGroup = 'الاعدادات';

    protected static ?string $navigationLabel = 'الشاشات الاولية';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                 Forms\Components\FileUpload::make('image')
                    ->label('الصورة')
                    ->image()
                    ->disk('public') // Store in storage/app/public
                    ->directory('onboarding') // Subdirectory for organization
                    ->visibility('public')
                    ->imageEditor() // Optional: Enable image cropping/editing
                    ->maxSize(2048) // Optional: Limit file size to 2MB
                    ->required(),
                Forms\Components\Section::make('English')
                    ->schema([
                        Forms\Components\TextInput::make('title.en')
                            ->label('العنوان بالانجليزية')
                            ->required(),
                        Forms\Components\Textarea::make('description.en')
                            ->label('الوصف بالانجليزية')
                            ->required(),
                    ]),
                Forms\Components\Section::make('عربي')
                    ->schema([
                        Forms\Components\TextInput::make('title.ar')
                            ->label('العنوان بالعربية')
                            ->required(),
                        Forms\Components\Textarea::make('description.ar')
                            ->label('الوصف بالعربية')
                            ->required(),
                    ]),
                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->required()
                    ->minValue(1),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('الصورة'),

                Tables\Columns\TextColumn::make('title_ar')
                    ->label('العنوان بالعربية')
                    ->getStateUsing(fn($record) => $record->getTranslation('title', 'ar'))
                    ->searchable(query: fn($query, $search) => $query->where('title->ar', 'like', "%{$search}%")),

                Tables\Columns\TextColumn::make('title_en')
                    ->label('العنوان بالانجليزية')
                    ->getStateUsing(fn($record) => $record->getTranslation('title', 'en'))
                    ->searchable(query: fn($query, $search) => $query->where('title->en', 'like', "%{$search}%")),

                Tables\Columns\TextColumn::make('description_ar')
                    ->label('الوصف بالعربية')
                    ->getStateUsing(fn($record) => $record->getTranslation('description', 'ar'))
                    ->searchable(query: fn($query, $search) => $query->where('description->ar', 'like', "%{$search}%")),

                Tables\Columns\TextColumn::make('description_en')
                    ->label('الوصف بالانجليزية')
                    ->getStateUsing(fn($record) => $record->getTranslation('description', 'en'))
                    ->searchable(query: fn($query, $search) => $query->where('description->en', 'like', "%{$search}%")),

                Tables\Columns\TextColumn::make('order')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order', 'asc');
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
            'index' => Pages\ListOnboardingScreens::route('/'),
            'create' => Pages\CreateOnboardingScreen::route('/create'),
            'edit' => Pages\EditOnboardingScreen::route('/{record}/edit'),
        ];
    }
} 