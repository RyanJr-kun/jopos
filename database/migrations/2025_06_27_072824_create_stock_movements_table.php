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
    if (!Schema::hasTable('stock_movements')) {
      Schema::create('stock_movements', function (Blueprint $table) {
        $table->id();
        $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
        $table->foreignId('product_id')->constrained('products');
        $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
        $table->integer('qty'); // signed: positif = masuk, negatif = keluar
        $table->integer('stok_sebelum');
        $table->integer('stok_setelah');
        $table->string('type', 30); // in, out, adjustment, transfer_in, transfer_out, opname, sale, purchase
        $table->text('keterangan')->nullable();
        $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
        $table->nullableMorphs('referensi'); // referensi_id + referensi_type, index otomatis
        $table->timestamps();

        $table->index(['store_id', 'product_id', 'product_variant_id']);
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
