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
        Schema::create('imported_files', function (Blueprint $table) {
            $table->id();
            $table->string('file_name')->unique();
            $table->string('file_hash')->nullable();
            $table->timestamp('imported_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imported_files');
    }
};
