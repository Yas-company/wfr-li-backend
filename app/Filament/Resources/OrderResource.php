<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Enums\PaymentMethod;
use App\Enums\OrderStatus;
use Filament\Tables\Enums\FiltersLayout;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'الطلبات';

    protected static ?string $navigationGroup = 'الطلبات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('تفاصيل الطلب')
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('حالة الطلب')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('الحالة')
                                    ->options(collect(OrderStatus::cases())->mapWithKeys(fn($case) => [$case->value => $case->label()]))
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Tabs\Tab::make('الدفع')
                            ->icon('heroicon-o-credit-card')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('payment_method')
                                            ->label('طريقة الدفع')
                                            ->options(collect(PaymentMethod::cases())->mapWithKeys(fn($case) => [$case->value => $case->label()]))
                                            ->required(),
                                        Forms\Components\Select::make('payment_status')
                                            ->label('حالة الدفع')
                                            ->options([
                                                'pending' => 'قيد الانتظار',
                                                'paid' => 'مدفوع',
                                                'failed' => 'مرفوض',
                                            ])
                                            ->required(),
                                        Forms\Components\TextInput::make('total_amount')->label('المجموع')->disabled()->columnSpan(2),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('المشتري والمورد')
                            ->icon('heroicon-o-user-group')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('user_id')
                                            ->relationship('user', 'name')
                                            ->label('Buyer / المشتري')
                                            ->disabled(),
                                        Forms\Components\Select::make('supplier_id')
                                            ->relationship('supplier', 'name')
                                            ->label('Supplier / المورد')
                                            ->disabled(),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('الشحن')
                            ->icon('heroicon-o-truck')
                            ->schema([
                                Forms\Components\Textarea::make('shipping_address')->label('عنوان الشحن')->disabled()->columnSpanFull(),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('shipping_latitude')->label('دائرة العرض')->disabled(),
                                        Forms\Components\TextInput::make('shipping_longitude')->label('دائرة الطول')->disabled(),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('ملاحظات')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Textarea::make('notes')->label('ملاحظات')->disabled()->columnSpanFull(),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('رقم')->sortable(),
                TextColumn::make('user.name')->label('المشتري')->searchable(),
                TextColumn::make('supplier.name')->label('المورد')->searchable(),
                BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'primary' => 'pending',
                        'success' => 'accepted',
                        'danger' => 'rejected',
                        'warning' => 'shipped',
                        'info' => 'delivered',
                    ]),
                BadgeColumn::make('payment_method')
                    ->label('طريقة الدفع')
                    ->colors([
                        'success' => 'cash',
                        'primary' => 'visa',
                    ]),
                BadgeColumn::make('payment_status')
                    ->label('حالة الدفع')
                    ->colors([
                        'primary' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                    ]),
                TextColumn::make('total_amount')->label('المجموع')->money('SAR'),
                TextColumn::make('created_at')->label('تاريخ الإنشاء')->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status / الحالة')
                    ->options(collect(OrderStatus::cases())->mapWithKeys(fn($case) => [$case->value => $case->label()])),
                SelectFilter::make('payment_method')
                    ->label('طريقة الدفع')
                    ->options(collect(PaymentMethod::cases())->mapWithKeys(fn($case) => [$case->value => $case->label()])),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\ViewAction::make()->label('عرض'),
                Tables\Actions\EditAction::make()->label('تعديل'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
