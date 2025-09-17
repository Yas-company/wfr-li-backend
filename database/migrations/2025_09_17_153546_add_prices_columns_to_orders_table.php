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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('supplier_total', 8, 2)->default(0)->after('total');
            $table->decimal('total_taxes', 8, 2)->default(0)->after('supplier_total');
            $table->decimal('total_platform_taxes', 8, 2)->default(0)->after('supplier_total');
            $table->decimal('total_country_taxes', 8, 2)->default(0)->after('total_platform_taxes');
            $table->decimal('total_other_taxes', 8, 2)->default(0)->after('total_country_taxes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'supplier_total',
                'total_taxes',
                'total_platform_taxes',
                'total_country_taxes',
                'total_other_taxes',
            ]);
        });
    }
};
