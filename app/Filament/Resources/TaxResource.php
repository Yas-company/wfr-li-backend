<?php

namespace App\Filament\Resources;

use App\Models\Tax;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\Tax\TaxGroup;
use App\Enums\Tax\TaxApplyTo;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TaxResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TaxResource\RelationManagers;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Illuminate\Database\Eloquent\Model;

class TaxResource extends Resource
{
    protected static ?string $model = Tax::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'الضرائب';

    protected static ?string $pluralNavigationLabel = 'الضرائب';

    protected static ?string $navigationGroup = 'الاعدادات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('الاسم')
                    ->required(),
                TextInput::make('code')
                    ->label('الرمز')
                    ->unique(ignoreRecord: true)
                    ->required(),
                TextInput::make('rate')
                    ->label('النسبة')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(0.99)
                    ->required(),
                Select::make('group')
                    ->label('المجموعة')
                    ->options(TaxGroup::getOptions())
                    ->required(),
                Select::make('applies_to')
                    ->label('النطاق')
                    ->options(TaxApplyTo::getOptions())
                    ->required(),
                Checkbox::make('is_active')
                    ->label('فعال')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable(),
                TextColumn::make('code')
                    ->label('الرمز')
                    ->searchable(),
                TextColumn::make('rate')
                    ->label('النسبة')
                    ->searchable(),
                TextColumn::make('applies_to')
                    ->label('النطاق')
                    ->searchable(),
                TextColumn::make('group')
                    ->label('المجموعة')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('فعال؟')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                //
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
            'index' => Pages\ListTaxes::route('/'),
            'create' => Pages\CreateTax::route('/create'),
            'edit' => Pages\EditTax::route('/{record}/edit'),
        ];
    }
}
