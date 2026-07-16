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
    Schema::create('banks', function (Blueprint $table) {
      $table->id();
      $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();
      $table->string('tipe_akun');
      $table->string('nama_bank');
      $table->string('nomor_rekening')->nullable();
      $table->string('nama_pemilik')->nullable();
      $table->decimal('saldo_awal', 15, 0)->default(0);
      $table->string('logo_bank')->nullable();
      $table->boolean('is_active')->default(true);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('banks');
  }
};
