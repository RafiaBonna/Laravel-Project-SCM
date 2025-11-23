<?php

// database/migrations/YYYY_MM_DD_HHMMSS_create_product_stocks_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->string('batch_no');
            $table->date('expiry_date')->nullable();
            $table->decimal('available_quantity', 15, 2); // এই ব্যাচের বর্তমান পরিমাণ
            $table->timestamps();

            // একটি পণ্যের একটি ব্যাচ শুধুমাত্র একবার থাকবে
            $table->unique(['product_id', 'batch_no']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_stocks');
    }
};
