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
        Schema::create('serial_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('nomor_seri');
            $table->enum('status', ['Tersedia', 'Terjual', 'Rusak', 'Hilang'])->default('Tersedia');
            $table->foreignId('purchase_id')->nullable()->constrained('purchases');
            $table->foreignId('item_sale_id')->nullable()->constrained('sale_items')->onDelete('set null');
            $table->timestamps();

            $table->unique(['product_id', 'nomor_seri']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('serial_numbers');
    }
};
