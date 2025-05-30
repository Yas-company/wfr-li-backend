<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class StockHistoryService
{
    public function fetchMonthlyHistory($ticker)
    {
        $apiKey = config('services.alphavantage.key');
        $url = 'https://www.alphavantage.co/query';
        $params = [
            'function' => 'TIME_SERIES_MONTHLY',
            'symbol' => strtoupper($ticker),
            'apikey' => $apiKey,
        ];

        $response = Http::get($url, $params);

        if (!$response->ok() || !isset($response['Monthly Time Series'])) {
            return null;
        }

        // Parse and return as array of [date => close price]
        $history = [];
        foreach ($response['Monthly Time Series'] as $date => $data) {
            $history[$date] = (float) $data['4. close'];
        }

        return $history;
    }

    public function fetchCompanyOverview($ticker)
    {
        $apiKey = config('services.alphavantage.key');
        $url = 'https://www.alphavantage.co/query';
        $params = [
            'function' => 'OVERVIEW',
            'symbol' => strtoupper($ticker),
            'apikey' => $apiKey,
        ];
        $response = Http::get($url, $params);
        return $response->ok() ? $response->json() : null;
    }
} 