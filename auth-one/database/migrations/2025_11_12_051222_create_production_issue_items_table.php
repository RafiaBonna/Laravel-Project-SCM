<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_issue_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_issue_id')->constrained('production_issues')->onDelete('cascade');
            $table->foreignId('raw_material_id')->constrained('raw_materials');
            // stock_id Track করার জন্য
            $table->foreignId('raw_material_stock_id')->nullable()->constrained('raw_material_stocks'); 
            
            $table->string('batch_number')->nullable();
            $table->decimal('quantity_issued', 10, 2);
            $table->decimal('unit_cost', 10, 4); // Average Purchase Price
            $table->decimal('total_cost', 10, 2);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_issue_items');
    }
};
