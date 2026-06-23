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
        if (!Schema::hasTable('sales')) {
            Schema::create('sales', function (Blueprint $table) {
                $table->id();
                $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
                $table->foreignId('customer_id')->nullable()->constrained('customers');
                $table->foreignId('user_id')->constrained('users');
                $table->string('referensi', 50)->unique();
                $table->dateTime('tanggal_penjualan');
                $table->date('tanggal_jatuh_tempo')->nullable();
                $table->decimal('subtotal', 15, 0);
                $table->decimal('service', 15, 0)->default(0);
                $table->decimal('ongkir', 15, 0)->default(0);
                $table->decimal('diskon', 15, 0)->default(0);
                $table->decimal('pajak', 15, 0)->default(0);
                $table->decimal('total_akhir', 15, 0);
                $table->decimal('jumlah_dibayar', 15, 0)->default(0);
                $table->decimal('sisa_piutang', 15, 0)->default(0);
                $table->string('status_pembayaran', 30)->default('Lunas');
                $table->string('metode_pembayaran', 30)->default('TUNAI');
                $table->foreignId('bank_id')->nullable()->constrained('banks')->onDelete('set null');
                $table->text('catatan')->nullable();
                $table->timestamps();

                $table->index('tanggal_penjualan');
                $table->index('customer_id');
                $table->index('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
