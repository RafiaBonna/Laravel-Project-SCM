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
        Schema::create('distributors', function (Blueprint $table) {
            $table->id();
            
            // ✅ CRITICAL: Foreign Key to users table (Missing column in your error)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            
            // ✅ Foreign Key to depos table (Distributor belongs to a Depo)
            $table->foreignId('depo_id')->constrained('depos')->onDelete('cascade'); 
            
            $table->string('name');
            $table->string('location')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributors');
    }
};