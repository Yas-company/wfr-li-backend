<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Order;
use App\Enums\UserRole;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Enums\Order\OrderStatus;
use Filament\Widgets\ChartWidget;

class UserChart extends ChartWidget
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
