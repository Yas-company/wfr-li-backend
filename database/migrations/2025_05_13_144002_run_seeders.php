<?php

use Database\Seeders\FactorySeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\SupplierSeeder;
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
        (new \Database\Seeders\FactorySeeder())->run();
        (new \Database\Seeders\SupplierSeeder())->run();
        (new \Database\Seeders\ProductSeeder())->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
