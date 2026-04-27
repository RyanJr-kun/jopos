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
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('name_product', 255);
                $table->string('slug', 100)->unique();
                $table->string('barcode', 100)->unique()->nullable();
                $table->text('description')->nullable();
                $table->string('sku', 50)->unique();
                $table->decimal('harga_jual', 15, 0);
                $table->decimal('harga_beli', 15, 0);
                $table->boolean('wajib_seri')->default(false);
                $table->unsignedInteger('stok_minimum')->default(0);
                $table->string('img_produk', 255)->nullable();
                $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
                $table->foreignId('brand_id')->constrained('brands')->onDelete('restrict');
                $table->foreignId('unit_id')->constrained('units')->onDelete('restrict');
                $table->foreignId('warrantie_id')->constrained('warranties')->onDelete('restrict');
                $table->foreignId('user_id')->constrained('users');
                $table->foreignId('taxe_id')->nullable()->constrained('taxes');
                $table->timestamps();

                $table->index('category_id');
                $table->index('brand_id');
                $table->index('unit_id');
                $table->index('taxe_id');
                $table->index('warrantie_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
