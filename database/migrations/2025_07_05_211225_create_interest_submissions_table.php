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
    Schema::create('interest_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->enum('business_type', ['restaurant', 'cafe', 'grocery', 'supermarket', 'catering', 'other']);
            $table->enum('city', ['makkah', 'jeddah', 'riyadh', 'dammam', 'medina', 'other']);
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index('email');
            $table->index('business_type');
            $table->index('city');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interest_submissions');
    }
};
