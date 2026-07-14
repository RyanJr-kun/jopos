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
    Schema::create('stock_transfer_item_serial_number', function (Blueprint $table) {
      $table->id();
      // foreign ke detail/item transfer stok
      $table->foreignId('stock_transfer_item_id')->constrained('stock_transfer_items')->onDelete('cascade');
      // foreign ke tabel serial_numbers
      $table->foreignId('serial_number_id')->constrained('serial_numbers')->onDelete('cascade');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('stock_transfer_item_serial_number');
  }
};
