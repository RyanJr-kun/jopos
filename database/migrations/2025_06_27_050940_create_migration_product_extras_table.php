<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // -------------------------------------------------------
        // 1. PRODUCT IMAGES (galeri foto produk utama)
        // -------------------------------------------------------
        if (!Schema::hasTable('product_images')) {
            Schema::create('product_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->string('path', 255);
                $table->boolean('is_primary')->default(false); // foto utama / thumbnail
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();

                $table->index('product_id');
            });
        }

        // -------------------------------------------------------
        // 2. PRODUCT VARIANT TYPES  (tipe variasi, misal: Warna, RAM, Storage)
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
        // 3. PRODUCT VARIANT OPTIONS  (nilai tiap tipe, misal: Merah, 8GB, 256GB)
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
        // 4. PRODUCT VARIANTS  (kombinasi akhir, misal: Merah-8GB-256GB)
        //    Setiap baris = 1 SKU yang bisa dijual
        // -------------------------------------------------------
        if (!Schema::hasTable('product_variants')) {
            Schema::create('product_variants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->string('sku', 100)->unique();
                $table->string('barcode', 100)->nullable()->unique();
                $table->decimal('harga_jual', 15, 0);
                $table->decimal('harga_beli', 15, 0);
                $table->integer('qty')->default(0);
                $table->string('img_variant', 255)->nullable(); // foto khusus variasi ini
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('product_id');
            });
        }

        // -------------------------------------------------------
        // 5. PIVOT: variant  <-->  option (many-to-many)
        //    Satu variant bisa punya banyak option dari tipe yg berbeda
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
        // 6. Tambah kolom specification ke products (terpisah dari description)
        // -------------------------------------------------------
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'specification')) {
            Schema::table('products', function (Blueprint $table) {
                $table->text('specification')->nullable()->after('description');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variant_option_pivot');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_variant_options');
        Schema::dropIfExists('product_variant_types');
        Schema::dropIfExists('product_images');

        if (Schema::hasColumn('products', 'specification')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('specification');
            });
        }
    }
};
