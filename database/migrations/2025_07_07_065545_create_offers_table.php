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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // رمز العرض 
            $table->string('title'); // عنوان العرض
            $table->text('description')->nullable(); // وصف العرض
            $table->decimal('discount_value', 8, 2); // قيمة الخصم
            $table->enum('discount_type', ['percentage', 'fixed']); // نوع الخصم
            $table->timestamp('start_date')->nullable(); // تاريخ بداية العرض
            $table->timestamp('end_date')->nullable(); // تاريخ نهاية العرض
            $table->boolean('is_active')->default(true); // هل العرض مفعل؟
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
