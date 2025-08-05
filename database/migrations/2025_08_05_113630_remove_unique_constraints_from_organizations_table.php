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
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropUnique('organizations_name_unique');
            $table->dropUnique('organizations_tax_number_unique');
            $table->dropUnique('organizations_commercial_register_number_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->unique('name');
            $table->unique('tax_number');
            $table->unique('commercial_register_number');
        });
    }
};
