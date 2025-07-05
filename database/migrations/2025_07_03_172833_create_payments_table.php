<?php

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('tap_id')->nullable(); // ID من Tap
            $table->string('reference_id')->nullable(); // رقم الحجز أو الأوردر في السيستم
            $table->tinyInteger('payment_method')->default(PaymentMethod::CASH_ON_DELIVERY);
            $table->tinyInteger('status')->default(PaymentStatus::PENDING);
            $table->decimal('amount', 10, 2);
            $table->string('currency', 5)->default('SAR');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
