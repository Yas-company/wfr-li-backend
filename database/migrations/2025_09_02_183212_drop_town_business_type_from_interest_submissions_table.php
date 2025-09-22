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
        Schema::table('interest_submissions', function (Blueprint $table) {
            $table->dropColumn('city');
            $table->dropColumn('business_type');
            $table->text('message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interest_submissions', function (Blueprint $table) {
            $table->string('city');
            $table->string('business_type');
        });
    }
};
