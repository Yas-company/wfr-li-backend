<?php

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add new fields
            $table->string('license_attachment')->nullable();
            $table->string('commercial_register_attachment')->nullable();
            $table->string('status')->default(UserStatus::PENDING->value);
            $table->foreignId('field_id')->nullable()->constrained()->nullOnDelete();
            
            $table->dropColumn(['lic_id']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove new fields
            $table->dropColumn(['license_attachment', 'commercial_register_attachment', 'status']);
            $table->dropForeign(['field_id']);
            $table->dropColumn('field_id');
            
            // Restore old field
            $table->string('lic_id')->nullable()->unique();
        });
    }
}; 