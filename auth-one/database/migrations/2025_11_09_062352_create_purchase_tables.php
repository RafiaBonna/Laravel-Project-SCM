// database/migrations/xxxx_xx_xx_xxxxxx_create_purchase_tables.php

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
        // 1. Purchase Invoices (Master Table)
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('supplier_id')->constrained('suppliers'); // FK
            $table->date('invoice_date');
            $table->decimal('sub_total', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2);
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained('users'); // কোন Admin এটি তৈরি করেছে
            $table->timestamps();
        });

        // 2. Raw Material Purchase Items (Detail Table)
        Schema::create('raw_material_purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_invoice_id')->constrained('purchase_invoices')->onDelete('cascade'); // FK
            $table->foreignId('raw_material_id')->constrained('raw_materials'); // FK
            $table->string('batch_number'); 
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
            // এই কম্বিনেশন একই ইনভয়েসের জন্য ইউনিক হতে হবে
            $table->unique(['purchase_invoice_id', 'raw_material_id', 'batch_number'], 'purchase_batch_unique'); 
        });

        // 3. Raw Material Stocks (Stock Tracking Table)
        Schema::create('raw_material_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_material_id')->constrained('raw_materials'); // FK
            $table->string('batch_number');
            $table->decimal('stock_quantity', 10, 2); // হাতে থাকা পরিমাণ
            $table->decimal('average_purchase_price', 10, 2); // মুভিং এভারেজ কস্ট
            $table->date('last_in_date'); 
            $table->timestamps();
            // একই ম্যাটেরিয়াল ও ব্যাচের জন্য একটি মাত্র রো থাকবে
            $table->unique(['raw_material_id', 'batch_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_material_stocks');
        Schema::dropIfExists('raw_material_purchase_items');
        Schema::dropIfExists('purchase_invoices');
    }
};