<?php

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
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn(['shipping_address', 'shipping_latitude', 'shipping_longitude']);
            $table->foreignId('shipping_address_id')->constrained('addresses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->text('shipping_address');
            $table->decimal('shipping_latitude', 10, 8);
            $table->decimal('shipping_longitude', 10, 8);
            $table->dropColumn('shipping_address_id');
        });
    }
};
