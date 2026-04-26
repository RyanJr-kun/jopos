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
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();

            // Label Alamat (Misal: Rumah, Kantor, Kosan)
            $table->string('label', 50)->nullable();

            // Penerima (Bisa jadi bukan nama customer sendiri, misal kirim ke mertua)
            $table->string('nama_penerima', 255);
            $table->string('kontak_penerima', 20);

            // Struktur Administratif & Ekspedisi (Biteship)
            $table->string('provinsi', 100)->nullable();
            $table->string('kabupaten_kota', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('biteship_area_id')->nullable();

            // Detail Jalan
            $table->text('alamat_detail')->nullable();

            // Koordinat (Untuk validasi jarak servis / GoSend / GrabExpress)
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Penanda apakah ini alamat default yang langsung terpilih saat checkout
            $table->boolean('is_primary')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};
