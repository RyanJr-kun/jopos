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
    if (!Schema::hasTable('stock_adjustment_items')) {
      Schema::create('stock_adjustment_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('stock_adjustment_id')->constrained('stock_adjustments')->cascadeOnDelete();
        $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
        $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
        $table->string('type', 30); // rusak, hilang, koreksi, retur_supplier, pemakaian_internal
        $table->integer('jumlah'); // signed: selisih yang diterapkan (bisa +/-)
        $table->integer('stok_sebelum');
        $table->integer('stok_setelah');
        $table->string('alasan');
        $table->timestamps();

        $table->unique(['stock_adjustment_id', 'product_id', 'product_variant_id'], 'unique_item_per_adjustment');
      });
    }
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('stock_adjustment_items');
  }
};
