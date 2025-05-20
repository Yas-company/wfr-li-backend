<?php
namespace App\AiAgents;

use LarAgent\Agent;
use LarAgent\Attributes\Tool;
use App\Models\StockSentiment;
use Illuminate\Support\Facades\Log;

class StockSentimentAgent extends Agent
{
    protected $model = 'mistral'; // Update to 'phi3' or 'tinyllama' if needed
    protected $history = 'in_memory';
    protected $provider = 'ollama';

    public function instructions(): string
    {
        return <<<'INSTRUCTIONS'
You are a stock sentiment analysis AI agent for a trading platform. Your role is to provide trading recommendations (buy, hold, sell) based on sentiment analysis for a given stock ticker. Use the fetchStockSentiment tool to retrieve sentiment data (positive/negative score) for the stock. Follow these rules:
- If sentiment score > 0.5, recommend "buy".
- If sentiment score is between -0.5 and 0.5, recommend "hold".
- If sentiment score < -0.5, recommend "sell".
Always return responses in JSON format:
{
    "status": "success",
    "message": "Recommendation based on sentiment analysis",
    "data": {
        "ticker": "Stock ticker",
        "sentiment_score": 0.0,
        "recommendation": "buy|hold|sell"
    }
}
If the ticker is invalid or sentiment data is unavailable, return an error response:
{
    "status": "error",
    "message": "Invalid ticker or no sentiment data available"
}
Be concise, professional, and data-driven in your responses.
INSTRUCTIONS;
    }

    public function prompt($message): string
    {
        return "User query: {$message}";
    }

    #[Tool('Fetch sentiment score for a stock ticker')]
    public function fetchStockSentiment($ticker): ?array
    {
        $sentiment = StockSentiment::where('ticker', strtoupper($ticker))->first();

        if (!$sentiment) {
            return null;
        }

        return [
            'ticker' => $sentiment->ticker,
            'sentiment_score' => $sentiment->sentiment_score,
        ];
    }

    public function onResponse($response): void
    {
        Log::info('StockSentimentAgent response', [
            'response' => $response,
            'agent' => get_class($this),
        ]);
    }
}