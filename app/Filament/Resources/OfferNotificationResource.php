<?php

namespace App\Filament\Resources;

use App\Models\OfferNotification;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

use App\Filament\Resources\OfferNotificationResource\Pages;

class OfferNotificationResource extends Resource
{
    protected static ?string $model = OfferNotification::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $pluralNavigationLabel = 'اشعارات العروض';
    protected static ?string $navigationLabel = 'اشعارات العروض';
    protected static ?string $navigationGroup = 'الإعلانات';

    // Get translated value
    protected static function getTranslatedValue(array|string|null $value, string $lang = 'ar'): string
    {
        if (is_null($value)) {
            return '';
        }
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $value = $decoded;
            } else {
                return $value; // fallback: return as string
            }
        }
        if (is_array($value)) {
            return $value[$lang] ?? '';
        }
        return '';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([


            Forms\Components\Section::make('معلومات العرض الأساسية')
                ->schema([
                    Forms\Components\Tabs::make('multilingual_fields')
                        ->tabs([
                            Forms\Components\Tabs\Tab::make('عربي')
                                ->icon('heroicon-o-language')
                                ->schema([
                                    Forms\Components\TextInput::make('name_ar')
                                        ->label('اسم العرض (عربي)')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('أدخل اسم العرض باللغة العربية...')
                                        ->helperText('اسم واضح ومختصر للعرض باللغة العربية')
                                        ->afterStateHydrated(function ($component, $state, $record) {
                                            if ($record && $record->name) {
                                                $name = is_array($record->name) ? $record->name : json_decode($record->name, true);
                                                $component->state($name['ar'] ?? '');
                                            }
                                        })
                                        ->dehydrated(false),
                                    Forms\Components\RichEditor::make('desc_ar')
                                        ->label('وصف العرض (عربي)')
                                        ->required()
                                        ->maxLength(1000)
                                        ->placeholder('أدخل وصف مفصل للعرض باللغة العربية...')
                                        ->helperText('وصف العرض باللغة العربية')
                                        ->afterStateHydrated(function ($component, $state, $record) {
                                            if ($record && $record->description) {
                                                $desc = is_array($record->description) ? $record->description : json_decode($record->description, true);
                                                $component->state($desc['ar'] ?? '');
                                            }
                                        })
                                        ->dehydrated(false),
                                ]),
                            Forms\Components\Tabs\Tab::make('English')
                                ->icon('heroicon-o-globe-alt')
                                ->schema([
                                    Forms\Components\TextInput::make('name_en')
                                        ->label('Offer Name (English)')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('Enter offer name in English...')
                                        ->helperText('Clear and concise offer name in English')
                                        ->afterStateHydrated(function ($component, $state, $record) {
                                            if ($record && $record->name) {
                                                $name = is_array($record->name) ? $record->name : json_decode($record->name, true);
                                                $component->state($name['en'] ?? '');
                                            }
                                        })
                                        ->dehydrated(false),
                                    Forms\Components\RichEditor::make('desc_en')
                                        ->label('Offer Description (English)')
                                        ->required()
                                        ->maxLength(1000)
                                        ->placeholder('Enter detailed offer description in English...')
                                        ->helperText('Offer description in English')
                                        ->afterStateHydrated(function ($component, $state, $record) {
                                            if ($record && $record->description) {
                                                $desc = is_array($record->description) ? $record->description : json_decode($record->description, true);
                                                $component->state($desc['en'] ?? '');
                                            }
                                        })
                                        ->dehydrated(false),
                                ]),
                        ]),
                    Forms\Components\Section::make('إعدادات العرض')
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\DatePicker::make('offer_date')
                                        ->label('تاريخ العرض')
                                        ->format('Y-m-d')
                                        ->displayFormat('d/m/Y')
                                        ->minDate(now()->subDay())
                                        ->maxDate(now()->addYear())
                                        ->required(),
                                    Forms\Components\Toggle::make('is_active')
                                        ->label('تفعيل العرض')
                                        ->default(true)
                                        ->onColor('success')
                                        ->offColor('danger')
                                        ->inline(false)
                                        ->extraAttributes(['style' => 'display: flex; justify-content: center; align-items: center; width: 100%;']),
                                ]),
                        ])
                        ->columns(1),
                ])
                ->columns(1),

            Forms\Components\Hidden::make('name')
                ->dehydrateStateUsing(fn ($state, $get) => [
                    'ar' => $get('name_ar'),
                    'en' => $get('name_en'),
                ]),
            Forms\Components\Hidden::make('description')
                ->dehydrateStateUsing(fn ($state, $get) => [
                    'ar' => $get('desc_ar'),
                    'en' => $get('desc_en'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name_ar')
                    ->label('اسم العرض (عربي)')
                    ->getStateUsing(fn ($record) => $record->name['ar'] ?? ''),
                TextColumn::make('name_en')
                    ->label('Offer Name (English)')
                    ->getStateUsing(fn ($record) => $record->name['en'] ?? ''),
                TextColumn::make('desc_ar')
                    ->label('وصف العرض (عربي)')
                    ->html()
                    ->limit(100)
                    ->getStateUsing(fn ($record) => $record->description['ar'] ?? ''),
                TextColumn::make('desc_en')
                    ->label('Offer Description (English)')
                    ->html()
                    ->limit(100)
                    ->getStateUsing(fn ($record) => $record->description['en'] ?? ''),
                TextColumn::make('offer_date')
                    ->label('تاريخ العرض')
                    ->date('d/m/Y')
                    ->sortable(),
                ToggleColumn::make('is_active')->label('الحالة')
                    ->onColor('success')->offColor('danger'),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('حالة العرض')
                    ->options([1 => 'مفعل', 0 => 'غير مفعل'])
                    ->placeholder('جميع الحالات'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->paginated([10, 25, 50])
            ->defaultSort('offer_date', 'desc')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOfferNotifications::route('/'),
            'create' => Pages\CreateOfferNotification::route('/create'),
            'edit' => Pages\EditOfferNotification::route('/{record}/edit'),
        ];
    }
}
