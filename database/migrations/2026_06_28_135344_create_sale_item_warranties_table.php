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
        Schema::create('sale_item_warranties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales');
            $table->foreignId('sale_item_id')->constrained('sale_items');
            $table->foreignId('warranty_id')->constrained('warranties');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants');
            $table->string('nomer_seri')->unique();
            $table->string('nomer_garansi')->unique();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('status');
            $table->foreignId('customer_id')->constrained('customers');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_item_warranties');
    }
};
