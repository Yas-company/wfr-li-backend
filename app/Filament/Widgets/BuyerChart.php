<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Enums\UserRole;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class BuyerChart extends ChartWidget
{
    protected static ?string $heading = 'Buyers';
    protected static ?string $maxHeight = '300px';


    protected function getData(): array
    {
        $data = Trend::query(
            User::approved()
                ->where('role', UserRole::BUYER)
            )
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Buyers',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
