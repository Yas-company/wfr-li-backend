<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockSentiment extends Model
{
    protected $fillable = ['ticker', 'sentiment_score'];
}