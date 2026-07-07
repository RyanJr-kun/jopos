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
    Schema::create('stock_transfer_items', function (Blueprint $table) {
      $table->id();
      $table->foreignId('stock_transfer_id')->constrained('stock_transfers')->cascadeOnDelete();
      $table->foreignId('product_id')->constrained('products');
      $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();

      $table->integer('qty_kirim');
      $table->integer('qty_diterima')->nullable(); // diisi pas approval, bisa beda dari qty_kirim
      $table->string('keterangan_selisih')->nullable(); // misal: "1 unit pecah di jalan"

      $table->timestamps();

      $table->unique(['stock_transfer_id', 'product_id', 'product_variant_id'], 'unique_item_per_transfer');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('stock_transfer_items');
  }
};
