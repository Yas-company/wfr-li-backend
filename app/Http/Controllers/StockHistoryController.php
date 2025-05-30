<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StockHistoryService;

class StockHistoryController extends Controller
{
    public function show(Request $request)
    {
        $ticker = $request->input('ticker');
        if (!$ticker) {
            return response()->json(['error' => 'Ticker parameter is required'], 422);
        }
        $service = new StockHistoryService();
        $history = $service->fetchMonthlyHistory($ticker);
        $overview = $service->fetchCompanyOverview($ticker);

        if (!$history) {
            return response()->json(['error' => 'Could not fetch data'], 404);
        }

        // Calculate statistics (e.g., average annual return)
        $years = [];
        foreach ($history as $date => $price) {
            $year = substr($date, 0, 4);
            $years[$year][] = $price;
        }
        $annualReturns = [];
        foreach ($years as $year => $prices) {
            $start = end($prices);
            $end = reset($prices);
            if ($start > 0) {
                $annualReturns[$year] = ($end - $start) / $start * 100;
            }
        }
        $avgReturn = count($annualReturns) ? array_sum($annualReturns) / count($annualReturns) : 0;

        return response()->json([
            'ticker' => $ticker,
            'company' => $overview,
            'history' => $history,
            'average_annual_return' => round($avgReturn, 2),
        ]);
    }
} 