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
        // Remove factory_id from products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['factory_id']);
            $table->dropColumn('factory_id');
        });

        // Remove factory_id from suppliers table
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropForeign(['factory_id']);
            $table->dropColumn('factory_id');
        });

        // Drop factories table
        Schema::dropIfExists('factories');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate factories table
        Schema::create('factories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('number')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // Add factory_id back to suppliers table
        Schema::table('suppliers', function (Blueprint $table) {
            $table->foreignId('factory_id')->nullable()->constrained()->onDelete('cascade');
        });

        // Add factory_id back to products table
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('factory_id')->nullable()->constrained()->onDelete('cascade');
        });
    }
}; 