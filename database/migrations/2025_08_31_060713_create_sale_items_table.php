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
        if (!Schema::hasTable('sale_items')) {
            Schema::create('sale_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
                $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
                $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
                $table->unsignedInteger('jumlah')->comment('pengganti qty');
                $table->decimal('harga_jual', 15, 0);
                $table->decimal('diskon_item', 15, 0)->default(0)->comment('Diskon per item dalam nominal');
                $table->foreignId('taxe_id')->nullable()->constrained('taxes');
                $table->decimal('pajak_item', 15, 0)->default(0);
                $table->decimal('subtotal', 15, 0);
                $table->timestamps();

                $table->index('sale_id');
                $table->index('taxe_id');
                $table->index('product_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
