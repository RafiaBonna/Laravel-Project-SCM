<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_receives', function (Blueprint $table) {
            $table->foreignId('receiver_id')->nullable()->constrained('users')->after('receive_date');
        });
    }

    public function down(): void
    {
        Schema::table('product_receives', function (Blueprint $table) {
            $table->dropColumn('receiver_id');
        });
    }
};
