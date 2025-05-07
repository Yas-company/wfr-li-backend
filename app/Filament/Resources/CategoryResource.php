<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers\ProductsRelationManager;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'التصنيفات';

    protected static ?string $pluralNavigationLabel = 'التصنيفات';

    protected static ?string $navigationGroup = 'التصنيفات والمنتجات';

    protected static ?int $navigationSort = 1;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name.en')
                    ->label('الاسم بالانجليزية')
                    ->required(),
                Forms\Components\TextInput::make('name.ar')
                    ->label('الاسم بالعربية')
                    ->required(),
                Forms\Components\FileUpload::make('image')
                    ->label('الصورة')
                    ->image()
                    ->disk('public') // Store in storage/app/public
                    ->directory('categories') // Subdirectory for organization
                    ->visibility('public')
                    ->imageEditor() // Optional: Enable image cropping/editing
                    ->maxSize(2048) // Optional: Limit file size to 2MB
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->label('الحالة')
                    ->default(false),

            ]);
    }

    public static function table(Table $table): Table
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
                    ->searchable(query: fn($query, $search) => $query->where('name->ar', 'like', "%{$search}%")),
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('الصورة'),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('الحالة'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('الحالة')
                    ->options([
                        true => 'مفعل',
                        false => 'غير مفعل',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
            ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
