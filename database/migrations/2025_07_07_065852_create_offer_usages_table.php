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
        Schema::create('offer_usages', function (Blueprint $table) {
            $table->id();
            $table->integer('usage_limit')->nullable(); // الحد الأقصى للاستخدام
            $table->integer('usage_count')->default(0); // عدد مرات الاستخدام حتى الآن
            $table->timestamp('last_used_at')->nullable(); // آخر مرة استخدم فيها المستخدم الكود
            $table->foreignId('offer_id')->constrained('offers')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_usages');
    }
};
