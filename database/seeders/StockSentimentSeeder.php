<?php
namespace Database\Seeders;

use App\Models\StockSentiment;
use Illuminate\Database\Seeder;

class StockSentimentSeeder extends Seeder
{
    public function run()
    {
        StockSentiment::create(['ticker' => 'AAPL', 'sentiment_score' => 0.75]);
        StockSentiment::create(['ticker' => 'TSLA', 'sentiment_score' => 0.10]);
        StockSentiment::create(['ticker' => 'GME', 'sentiment_score' => -0.80]);
    }
}