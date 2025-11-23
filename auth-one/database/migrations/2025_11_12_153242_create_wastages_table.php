<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wastages', function (Blueprint $table) {
            $table->id();
            
            $table->date('wastage_date'); // কখন নষ্ট হলো
            $table->foreignId('raw_material_id')->constrained('raw_materials'); // কোন কাঁচামাল
            
            // Raw Material Stock থেকে কোন ব্যাচটি নষ্ট হচ্ছে তা ট্র্যাক করার জন্য
            $table->foreignId('raw_material_stock_id')->constrained('raw_material_stocks'); 
            
            $table->string('batch_number'); // ব্যাচ নম্বর
            $table->decimal('quantity_wasted', 10, 3); // নষ্ট হওয়া পরিমাণ
            
            $table->decimal('unit_cost', 10, 4); // নষ্ট হওয়ার সময় প্রতি ইউনিটের কস্ট
            $table->decimal('total_cost', 10, 2); // নষ্ট হওয়া মোট কস্ট
            
            $table->text('reason'); // নষ্ট হওয়ার কারণ
            $table->foreignId('user_id')->constrained('users'); // যিনি এন্ট্রি করেছেন
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wastages');
    }
};