<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('stores')) {
            Schema::create('stores', function (Blueprint $table) {
                $table->id();
                $table->string('name_toko', 100);
                $table->enum('type', ['toko', 'gudang'])->default('toko');

                // Struktur Alamat Baru
                $table->string('provinsi', 100)->nullable();
                $table->string('kabupaten_kota', 100)->nullable();
                $table->string('kecamatan', 100)->nullable();
                $table->string('desa', 100)->nullable();
                $table->text('alamat')->nullable(); // Untuk teks panjang/Quill

                // Kontak & Media
                $table->text('map_url')->nullable();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->string('telepon', 20)->nullable();
                $table->string('email', 100)->nullable();
                $table->string('logo')->nullable();

                // Relasi ke tabel users untuk Kepala Toko / PIC
                $table->foreignId('pic_id')->nullable()->constrained('users')->nullOnDelete();

                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
