<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_receive_items', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('product_receive_id')->constrained('product_receives')->onDelete('cascade'); 
            $table->foreignId('product_id')->constrained('products'); 

            // Product info
            $table->string('batch_no')->nullable();
            $table->date('production_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->decimal('received_quantity', 15, 2);

            // Pricing & cost
            $table->decimal('cost_rate', 15, 2)->default(0);
            $table->decimal('mrp', 15, 2)->default(0);
            $table->decimal('retail', 15, 2)->default(0);
            $table->decimal('distributor', 15, 2)->default(0);
            $table->decimal('depo_selling', 15, 2)->default(0);
            $table->decimal('total_item_cost', 18, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_receive_items');
    }
};
