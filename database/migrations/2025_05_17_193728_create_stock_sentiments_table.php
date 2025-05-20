<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockSentimentsTable extends Migration
{
    public function up()
    {
        Schema::create('stock_sentiments', function (Blueprint $table) {
            $table->id();
            $table->string('ticker', 5)->unique();
            $table->decimal('sentiment_score', 3, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_sentiments');
    }
}