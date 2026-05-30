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
        if (!Schema::hasTable('stock_takes')) {
            Schema::create('stock_takes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
                $table->string('kode_opname')->unique();
                $table->dateTime('tanggal_opname');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->text('catatan')->nullable();
                $table->string('status', 50)->default('Selesai');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_takes');
    }
};
