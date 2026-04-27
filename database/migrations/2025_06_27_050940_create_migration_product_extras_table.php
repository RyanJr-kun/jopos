<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // -------------------------------------------------------
        // 1. PRODUCT VARIANT TYPES  (tipe variasi, misal: Warna, RAM, Storage)
        // -------------------------------------------------------
        if (!Schema::hasTable('product_variant_types')) {
            Schema::create('product_variant_types', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->string('name', 100);      // "Warna", "RAM", "Storage"
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();

                $table->index('product_id');
            });
        }

        // -------------------------------------------------------
        // 2. PRODUCT VARIANT OPTIONS  (nilai tiap tipe, misal: Merah, 8GB, 256GB)
        // -------------------------------------------------------
        if (!Schema::hasTable('product_variant_options')) {
            Schema::create('product_variant_options', function (Blueprint $table) {
                $table->id();
                $table->foreignId('variant_type_id')
                    ->constrained('product_variant_types')
                    ->onDelete('cascade');
                $table->string('value', 100);     // "Merah", "8GB", "256GB"
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();

                $table->index('variant_type_id');
            });
        }

        // -------------------------------------------------------
        // 3. PRODUCT VARIANTS  (kombinasi akhir, misal: Merah-8GB-256GB)
        // -------------------------------------------------------
        if (!Schema::hasTable('product_variants')) {
            Schema::create('product_variants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->string('sku', 100)->unique();
                $table->string('barcode', 100)->nullable()->unique();
                $table->decimal('harga_jual', 15, 0);
                $table->decimal('harga_beli', 15, 0);
                $table->string('img_variant', 255)->nullable(); // Tetap dipertahankan untuk thumbnail cepat
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('product_id');
            });
        }

        // -------------------------------------------------------
        // 4. PIVOT: variant  <-->  option (many-to-many)
        // -------------------------------------------------------
        if (!Schema::hasTable('product_variant_option_pivot')) {
            Schema::create('product_variant_option_pivot', function (Blueprint $table) {
                $table->foreignId('product_variant_id')
                    ->constrained('product_variants')
                    ->onDelete('cascade');
                $table->foreignId('product_variant_option_id')
                    ->constrained('product_variant_options')
                    ->onDelete('cascade');

                $table->primary(
                    ['product_variant_id', 'product_variant_option_id'],
                    'variant_option_pivot_primary'
                );
            });
        }

        // -------------------------------------------------------
        // 5. PRODUCT IMAGES (Dipindah ke bawah agar bisa relasi ke product_variants)
        // -------------------------------------------------------
        if (!Schema::hasTable('product_images')) {
            Schema::create('product_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                // Sekarang aman karena product_variants sudah diciptakan di atas
                $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('cascade');
                $table->string('path', 255);
                $table->boolean('is_primary')->default(false); 
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();

                $table->index('product_id');
            });
        }

        // -------------------------------------------------------
        // 6. Tambah kolom specification ke products
        // -------------------------------------------------------
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'specification')) {
            Schema::table('products', function (Blueprint $table) {
                $table->text('specification')->nullable()->after('description');
            });
        }
    }

    public function down(): void
    {
        // Urutan drop juga dibalik, jatuhkan tabel yang menumpang (child) terlebih dahulu
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_variant_option_pivot');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_variant_options');
        Schema::dropIfExists('product_variant_types');

        if (Schema::hasColumn('products', 'specification')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('specification');
            });
        }
    }
};