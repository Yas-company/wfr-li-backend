<?php

namespace App\Filament\Resources\SupplierUserResource\Pages;

use App\Filament\Resources\SupplierUserResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use App\Enums\UserStatus;
use Illuminate\Support\Facades\Storage;
use App\Filament\Resources\SupplierUserResource\RelationManagers\CategoriesRelationManager;

class ViewSupplierUser extends ViewRecord
{
    protected static string $resource = SupplierUserResource::class;

    public function getRelationManagers(): array
    {
        return [
            CategoriesRelationManager::class,
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('المعلومات الأساسية')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('الاسم'),
                        Infolists\Components\TextEntry::make('email')
                            ->label('البريد الإلكتروني'),
                        Infolists\Components\TextEntry::make('phone')
                            ->label('رقم الهاتف'),
                        Infolists\Components\TextEntry::make('business_name')
                            ->label('اسم العمل'),
                        Infolists\Components\TextEntry::make('field.name')
                            ->label('المجال'),
                        Infolists\Components\TextEntry::make('address')
                            ->label('العنوان'),
                        Infolists\Components\IconEntry::make('is_verified')
                            ->label('مفعل')
                            ->boolean(),
                        Infolists\Components\TextEntry::make('status')
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
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('المستندات')
                    ->schema([
                        Infolists\Components\TextEntry::make('license_attachment')
                            ->label('رخصة العمل')
                            ->formatStateUsing(function ($state) {
                                if ($state) {
                                    return view('components.document-link', [
                                        'url' => Storage::url($state),
                                        'label' => 'عرض المستند',
                                    ]);
                                }
                                return 'لا يوجد مستند';
                            })
                            ->html(),
                        Infolists\Components\TextEntry::make('commercial_register_attachment')
                            ->label('السجل التجاري')
                            ->formatStateUsing(function ($state) {
                                if ($state) {
                                    return view('components.document-link', [
                                        'url' => Storage::url($state),
                                        'label' => 'عرض المستند',
                                    ]);
                                }
                                return 'لا يوجد مستند';
                            })
                            ->html(),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            // Add any actions you want in the header
        ];
    }
} 