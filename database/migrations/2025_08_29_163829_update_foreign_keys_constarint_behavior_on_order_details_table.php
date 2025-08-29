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
            $table->dropForeign(['shipping_address_id']);

            $table->foreignId('shipping_address_id')
                ->nullable()
                ->change();

            $table->foreign('shipping_address_id')
                ->references('id')->on('addresses')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropForeign(['shipping_address_id']);

            $table->foreign('shipping_address_id')
                ->references('id')->on('addresses');
        });
    }
};
