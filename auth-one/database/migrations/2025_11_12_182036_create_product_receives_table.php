<?php
// database/migrations/YYYY_MM_DD_HHMMSS_create_product_receives_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_receives', function (Blueprint $table) {
            $table->id();
            $table->string('receive_no')->unique(); // অনন্য রিসিভ নম্বর (PR-2025-0001)
            $table->date('receive_date');
            $table->text('note')->nullable();
            $table->decimal('total_received_qty', 15, 2)->default(0);
            $table->foreignId('received_by_user_id')->constrained('users')->nullable(); // যিনি রিসিভ করলেন
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_receives');
    }
};
