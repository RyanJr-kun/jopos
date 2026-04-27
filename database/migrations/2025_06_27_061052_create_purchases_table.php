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
        if (!Schema::hasTable('purchases')) {
            Schema::create('purchases', function (Blueprint $table) {
                $table->id();
                $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users');
                $table->foreignId('supplier_id')->constrained('suppliers');
                $table->string('referensi', 50)->unique();
                $table->dateTime('tanggal_pembelian');
                $table->decimal('subtotal', 15, 0);
                $table->decimal('diskon', 15, 0)->default(0);
                $table->decimal('pajak', 15, 0)->default(0);
                $table->decimal('ongkir', 15, 0)->default(0);
                $table->decimal('total_akhir', 15, 0);
                $table->decimal('jumlah_dibayar', 15, 0)->default(0);
                $table->decimal('sisa_hutang', 15, 0)->default(0);
                $table->enum('status_barang', ['Diterima', 'Belum Diterima', 'Dibatalkan'])->default('Diterima');
                $table->enum('status_pembayaran', ['Lunas', 'Belum Lunas', 'Dibatalkan'])->default('Lunas');
                $table->text('catatan')->nullable();
                $table->timestamps();

                $table->index('tanggal_pembelian');
                $table->index('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
