<?php

// database/migrations/xxxxxxxx_create_sales_invoices_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();
            
            // ইনভয়েসের মৌলিক তথ্য
            $table->string('invoice_no')->unique();
            $table->date('invoice_date');
            $table->decimal('total_amount', 10, 2)->default(0);

            // কে তৈরি করল (Super Admin)
            $table->foreignId('user_id')->constrained('users'); 

            // কার কাছে বিক্রি করা হলো (Depo)
            $table->foreignId('depo_id')->constrained('depos'); 

            // ⚠️ ইনভয়েসের স্ট্যাটাস (গুরুত্বপূর্ণ)
            $table->enum('status', ['Pending', 'Approved', 'Canceled'])->default('Pending');
            
            // অনুমোদনের/বাতিলের তথ্য
            $table->foreignId('approved_by')->nullable()->constrained('users'); // Depo-এর User ID
            $table->timestamp('approved_at')->nullable();
            $table->text('cancellation_reason')->nullable(); // Depo বাতিল করলে কারণ লিখবে
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_invoices');
    }
};
