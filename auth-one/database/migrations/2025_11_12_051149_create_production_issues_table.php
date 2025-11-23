<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_issues', function (Blueprint $table) {
            $table->id();
            $table->string('issue_number')->unique(); // Stock Out Invoice Number
            $table->string('factory_name')->nullable(); // কারখানার নাম
            $table->date('issue_date');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // যিনি ইস্যু করছেন
            $table->decimal('total_quantity_issued', 10, 2)->default(0); 
            $table->decimal('total_issue_cost', 10, 2)->default(0); 
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_issues');
    }
};
