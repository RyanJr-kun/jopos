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
    Schema::create('stock_transfers', function (Blueprint $table) {
      $table->id();
      $table->string('kode_transfer')->unique();
      $table->foreignId('store_asal_id')->constrained('stores');
      $table->foreignId('store_tujuan_id')->constrained('stores');
      $table->dateTime('tanggal_kirim');
      $table->foreignId('user_kirim_id')->constrained('users'); // yang input & kirim dari toko asal

      $table->dateTime('tanggal_diterima')->nullable();
      $table->foreignId('user_terima_id')->nullable()->constrained('users')->nullOnDelete(); // yang approve terima di toko tujuan

      $table->string('status', 30)->default('draft');
      // draft -> dikirim -> diterima
      //                  -> diterima_sebagian (kalau ada selisih)
      //                  -> ditolak
      //       -> dibatalkan (sebelum dikirim)

      $table->text('catatan_kirim')->nullable();
      $table->text('catatan_terima')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('stock_transfers');
  }
};
