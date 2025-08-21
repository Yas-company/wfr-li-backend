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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('base_price', 10, 2)->nullable()->after('price');
            $table->decimal('discount_rate', 5, 2)->default(0)->after('base_price');
            $table->decimal('total_discount', 10, 2)->nullable()->after('discount_rate');
            $table->decimal('price_after_discount', 10, 2)->nullable()->after('total_discount');
            $table->decimal(('price_after_taxes'), 10, 2)->nullable()->after('price_after_discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('base_price');
            $table->dropColumn('discount_rate');
            $table->dropColumn('total_discount');
            $table->dropColumn('price_after_discount');
            $table->dropColumn('price_after_taxes');
        });
    }
};
