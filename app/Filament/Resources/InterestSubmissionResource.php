<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InterestSubmissionResource\Pages;
use App\Filament\Resources\InterestSubmissionResource\RelationManagers;
use App\Models\InterestSubmission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InterestSubmissionResource extends Resource
{
    protected static ?string $model = InterestSubmission::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationGroup = 'الطلبات';

    protected static ?string $navigationLabel = 'طلبات الاهتمام';
    
    protected static ?string $modelLabel = 'طلب اهتمام';
    
    protected static ?string $pluralModelLabel = 'طلبات الاهتمام';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('الاسم')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('البريد الإلكتروني')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('business_type')
                    ->label('نوع العمل')
                    ->options([
                        'restaurant' => 'مطعم',
                        'cafe' => 'كافيه',
                        'grocery' => 'بقالة',
                        'supermarket' => 'سوبر ماركت',
                        'catering' => 'خدمات الطعام',
                        'other' => 'أخرى',
                    ])
                    ->required(),
                Forms\Components\Select::make('city')
                    ->label('المدينة')
                    ->options([
                        'makkah' => 'مكة المكرمة',
                        'jeddah' => 'جدة',
                        'riyadh' => 'الرياض',
                        'dammam' => 'الدمام',
                        'medina' => 'المدينة المنورة',
                        'other' => 'مدينة أخرى',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('business_type_display')
                    ->label('نوع العمل')
                    ->sortable(),
                Tables\Columns\TextColumn::make('city_display')
                    ->label('المدينة')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('business_type')
                    ->label('نوع العمل')
                    ->options([
                        'restaurant' => 'مطعم',
                        'cafe' => 'كافيه',
                        'grocery' => 'بقالة',
                        'supermarket' => 'سوبر ماركت',
                        'catering' => 'خدمات الطعام',
                        'other' => 'أخرى',
                    ]),
                Tables\Filters\SelectFilter::make('city')
                    ->label('المدينة')
                    ->options([
                        'makkah' => 'مكة المكرمة',
                        'jeddah' => 'جدة',
                        'riyadh' => 'الرياض',
                        'dammam' => 'الدمام',
                        'medina' => 'المدينة المنورة',
                        'other' => 'مدينة أخرى',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListInterestSubmissions::route('/'),
            'create' => Pages\CreateInterestSubmission::route('/create'),
            'edit' => Pages\EditInterestSubmission::route('/{record}/edit'),
        ];
    }
}
