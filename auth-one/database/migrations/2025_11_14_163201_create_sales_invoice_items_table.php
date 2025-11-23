<?php
// database/migrations/xxxxxxxx_create_sales_invoice_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_invoice_items', function (Blueprint $table) {
            $table->id();
            
            // কোন ইনভয়েসের অংশ
            $table->foreignId('sales_invoice_id')->constrained('sales_invoices')->onDelete('cascade'); 
            
            // কোন পণ্য
            $table->foreignId('product_id')->constrained('products'); 

            // কোন ব্যাচ থেকে (স্টক কমানোর জন্য এটা গুরুত্বপূর্ণ)
            $table->unsignedBigInteger('product_stock_id')->nullable(); 

            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('sub_total', 10, 2);
            
            $table->timestamps();

            // product_stock_id কে product_stocks টেবিলের সাথে যুক্ত করতে হবে (পরবর্তীতে)
            // $table->foreign('product_stock_id')->references('id')->on('product_stocks');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_invoice_items');
    }
};
