<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Organization;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Enums\Organization\OrganizationStatus;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OrganizationResource\Pages;
use App\Filament\Resources\OrganizationResource\RelationManagers;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'المنشآت';

    protected static ?string $navigationLabel = 'المنشآت';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('owner.name')
                    ->label('اسم المالك')
                    ->searchable(),
                TextColumn::make('owner.email')
                    ->label('البريد الإلكتروني')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('اسم المنشآة')
                    ->searchable(),
                TextColumn::make('tax_number')
                    ->label('الرقم الضريبي')
                    ->searchable(),
                TextColumn::make('commercial_register_number')
                    ->label('رقم السجل التجاري')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->date()
                    ->label('تاريخ الإنشاء')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->colors(OrganizationStatus::colors())
                    ->formatStateUsing(fn ($state): string => OrganizationStatus::tryFrom($state)?->label()),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('approve')
                    ->label('approve')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->visible(fn (Organization $record): bool => OrganizationStatus::tryFrom($record->status) === OrganizationStatus::PENDING)
                    ->action(function (Organization $record) {
                        $record->update(['status' => OrganizationStatus::APPROVED->value]);
                    }),

                Action::make('reject')
                    ->label('reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->visible(fn (Organization $record): bool => OrganizationStatus::tryFrom($record->status) == OrganizationStatus::PENDING)
                    ->action(function (Organization $record) {
                        $record->update(['status' => OrganizationStatus::REJECTED->value]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\BulkAction::make('approve')
                        ->label('Approve Selected')
                        ->color('success')
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->update(['status' => OrganizationStatus::APPROVED->value]);
                        }),

                    \Filament\Tables\Actions\BulkAction::make('reject')
                        ->label('Reject Selected')
                        ->color('danger')
                        ->icon('heroicon-o-x-circle')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->update(['status' => OrganizationStatus::REJECTED->value]);
                        }),
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
            'index' => Pages\ListOrganizations::route('/'),
            'create' => Pages\CreateOrganization::route('/create'),
            'edit' => Pages\EditOrganization::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
