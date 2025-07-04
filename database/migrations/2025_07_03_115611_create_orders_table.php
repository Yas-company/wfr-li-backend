<?php

use App\Enums\Order\OrderStatus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->decimal('total', 10, 2);
            $table->decimal('total_discount', 10, 2)->default(0);
            $table->enum('status', array_column(OrderStatus::cases(), 'value'))->default(OrderStatus::PENDING);
            $table->dateTime('deleted_at')->nullable();
            $table->foreignId('user_id')->constrained('users')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
