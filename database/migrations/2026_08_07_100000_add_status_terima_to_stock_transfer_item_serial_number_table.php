<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Tambah kolom status_terima dan alasan_tolak pada tabel pivot
   * stock_transfer_item_serial_number untuk mendukung fitur partial
   * receive per serial number.
   *
   * - status_terima: 'diterima' | 'ditolak' | null (belum diproses)
   * - alasan_tolak: alasan penolakan SN spesifik (misal "layar pecah")
   */
  public function up(): void
  {
    Schema::table('stock_transfer_item_serial_number', function (Blueprint $table) {
      $table->string('status_terima', 30)->nullable()->after('serial_number_id');
      $table->string('alasan_tolak')->nullable()->after('status_terima');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('stock_transfer_item_serial_number', function (Blueprint $table) {
      $table->dropColumn(['status_terima', 'alasan_tolak']);
    });
  }
};
