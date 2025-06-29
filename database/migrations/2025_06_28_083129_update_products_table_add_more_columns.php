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
            $table->unsignedBigInteger('supplier_id')->after('id');
            $table->decimal('quantity', 10, 2)->after('price');
            $table->tinyInteger('unit_type')->default(0)->after('quantity');
            $table->tinyInteger('status')->default(0)->after('unit_type');
            $table->boolean('is_active')->default(true)->after('status');

            // إضافة العلاقات
            $table->foreign('supplier_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['subcategory_id']);

            $table->dropColumn([
                'supplier_id',
                'subcategory_id',
                'description',
                'quantity',
                'unit_type',
                'custom_unit',
                'status',
                'is_active',
            ]);
        });
    }
};
