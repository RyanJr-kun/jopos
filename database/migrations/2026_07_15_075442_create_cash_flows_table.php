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
    Schema::create('cash_flows', function (Blueprint $table) {
      $table->id();
      $table->string('type');
      $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
      $table->foreignId('transaction_category_id')->nullable();
      $table->dateTime('tanggal');
      $table->decimal('nominal', 15, 0);
      $table->string('metode_pembayaran', 30)->default('TUNAI');
      $table->foreignId('bank_id')->nullable()->constrained('banks')->onDelete('set null');
      $table->string('metode_pembayaran_tujuan', 30)->nullable();
      $table->foreignId('bank_id_tujuan')->nullable()->constrained('banks')->onDelete('set null');
      $table->string('referensi')->nullable()->unique();
      $table->string('bukti')->nullable()->comment('foto');
      $table->string('keterangan');
      $table->text('description')->nullable();
      $table->foreignId('user_id')->constrained('users');
      $table->timestamps();

      $table->index('transaction_category_id');
      $table->index('tanggal');
      $table->index('user_id');
      $table->index(['store_id', 'type', 'tanggal']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('cash_flows');
  }
};
