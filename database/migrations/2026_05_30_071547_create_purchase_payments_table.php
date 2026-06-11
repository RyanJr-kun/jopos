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
        Schema::create('purchase_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict'); 
            $table->date('tanggal_bayar');
            $table->decimal('jumlah_bayar', 15, 0); 
            $table->string('metode_pembayaran'); 
            $table->foreignId('bank_id')->nullable()->constrained('banks')->onDelete('set null');
            $table->string('referensi_pembayaran')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_payments');
    }
};
