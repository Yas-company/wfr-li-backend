<?php

use App\Enums\Order\PaymentMethod;
use App\Enums\Order\PaymentStatus;
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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->text('shipping_address');
            $table->decimal('shipping_latitude', 10, 8);
            $table->decimal('shipping_longitude', 10, 8);
            $table->enum('payment_status', array_column(PaymentStatus::cases(), 'value'))->default(PaymentStatus::PENDING);
            $table->enum('payment_method', array_column(PaymentMethod::cases(), 'value'))->default(PaymentMethod::Tap);
            $table->string('tracking_number')->nullable();
            $table->date('estimated_delivery_date')->nullable();
            $table->text('notes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
