<?php
namespace App\Http\Controllers;

use App\AiAgents\StockSentimentAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AiTradingController extends Controller
{
    public function analyzeSentiment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticker' => 'required|string|alpha|min:1|max:5',
            'message' => 'required|string|min:1|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid input',
                'errors' => $validator->errors(),
            ], 422);
        }

        $agent = new StockSentimentAgent('stock_sentiment_analysis'); // Restore argument
        $response = $agent->message($request->input('message'))->respond();

        return response()->json($response);
    }
}