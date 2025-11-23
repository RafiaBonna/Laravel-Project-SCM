<?php

// database/migrations/YYYY_MM_DD_HHMMSS_create_products_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('sku')->unique()->nullable(); // Stock Keeping Unit (ঐচ্ছিক)
            $table->string('unit')->default('pcs'); // পরিমাপের একক (যেমন: Pcs, Box, Kg)
            $table->decimal('current_stock', 15, 2)->default(0); // বর্তমান স্টক
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users'); // যিনি এন্ট্রি দিলেন
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
