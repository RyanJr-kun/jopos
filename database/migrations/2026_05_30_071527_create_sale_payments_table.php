<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('sale_payments', function (Blueprint $table) {
      $table->id();
      $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
      $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
      $table->dateTime('tanggal_bayar');
      $table->decimal('jumlah_bayar', 15, 0);
      $table->string('metode_pembayaran', 30)->default('TUNAI');
      $table->foreignId('account_id')->nullable()->constrained('accounts')->onDelete('set null');
      $table->string('referensi_pembayaran')->nullable();
      $table->string('payment_status', 30)->default('pending');
      $table->string('snap_token')->nullable();
      $table->text('catatan')->nullable();
      $table->json('midtrans_response')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('sale_payments');
  }
};
