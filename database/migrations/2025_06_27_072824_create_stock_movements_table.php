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
        if (!Schema::hasTable('stock_movements')) {
            Schema::create('stock_movements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('asal_store_id')->constrained('stores')->onDelete('cascade');
                $table->foreignId('tujuan_store_id')->constrained('stores')->onDelete('cascade');
                $table->foreignId('product_id')->constrained('products');
                $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
                $table->integer('jumlah'); // positif (masuk) atau negatif (keluar)
                $table->enum('tipe', ['PEMBELIAN', 'PENJUALAN', 'PENYESUAIAN', 'RETUR_JUAL', 'RETUR_BELI']);
                $table->text('keterangan')->nullable();
                $table->foreignId('user_id')->nullable()->constrained('users');
                $table->unsignedBigInteger('referensi_id')->nullable();
                $table->string('referensi_tipe')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
