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
        if (!Schema::hasTable('purchase_items')) {
            Schema::create('purchase_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('purchase_id')->constrained('purchases')->onDelete('cascade');
                $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
                $table->unsignedInteger('qty');
                $table->decimal('harga_beli', 15, 0);
                $table->decimal('diskon', 15, 0)->default(0);
                $table->foreignId('taxe_id')->nullable()->constrained('taxes');
                $table->decimal('subtotal', 15, 0);
                $table->timestamps();

                $table->index('purchase_id');
                $table->index('product_id');
                $table->index('taxe_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
