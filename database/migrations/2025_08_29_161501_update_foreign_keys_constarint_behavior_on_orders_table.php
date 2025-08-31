<?php

use Illuminate\Support\Facades\DB;
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
        Schema::table('orders', function (Blueprint $table) {

            DB::statement('ALTER TABLE orders DROP FOREIGN KEY `1`');

            $table->dropForeign(['supplier_id']);

            $table->foreignId('user_id')
                ->nullable()
                ->change();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->nullOnDelete();

            $table->foreignId('supplier_id')
                ->nullable()
                ->change();

            $table->foreign('supplier_id')
                ->references('id')->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['supplier_id']);

            $table->foreign('user_id')
                ->references('id')->on('users');

            $table->foreign('supplier_id')
                ->references('id')->on('users');
        });
    }
};
