<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierUserResource\Pages;
use App\Models\User;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;

class SupplierUserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'إدارة المستخدمين';

    protected static ?string $navigationLabel = 'الموردين';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('role', UserRole::SUPPLIER);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable(),
                Tables\Columns\TextColumn::make('business_name')
                    ->label('اسم العمل')
                    ->searchable(),
                Tables\Columns\TextColumn::make('field.name')
                    ->label('المجال')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('مفعل')
                    ->boolean(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (UserStatus $state): string => match ($state) {
                        UserStatus::APPROVED => 'success',
                        UserStatus::REJECTED => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (UserStatus $state): string => match ($state) {
                        UserStatus::APPROVED => 'تم القبول',
                        UserStatus::REJECTED => 'مرفوض',
                        default => 'قيد الانتظار',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('مفعل'),
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        UserStatus::PENDING->value => 'قيد الانتظار',
                        UserStatus::APPROVED->value => 'تم القبول',
                        UserStatus::REJECTED->value => 'مرفوض',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('عرض بيانات المورد')
                    ->icon('heroicon-o-eye')
                    ->color('primary'),
                Tables\Actions\Action::make('approve')
                    ->label('قبول')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (User $record) {
                        $record->update([
                            'status' => UserStatus::APPROVED->value,
                            'is_verified' => true
                        ]);
                    })
                    ->visible(fn (User $record) => $record->status !== UserStatus::APPROVED),
                Tables\Actions\Action::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (User $record) => $record->update(['status' => UserStatus::REJECTED->value]))
                    ->visible(fn (User $record) => $record->status !== UserStatus::REJECTED),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupplierUsers::route('/'),
            'view' => Pages\ViewSupplierUser::route('/{record}'),
        ];
    }
} 