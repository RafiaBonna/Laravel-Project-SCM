<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            // ✅ CRITICAL FIX: Seeder-এর সাথে মিল রেখে name এবং slug কলাম
            $table->string('name'); 
            $table->string('slug')->unique(); // লগিকে slug ব্যবহার করা হয়েছে
            $table->string('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
