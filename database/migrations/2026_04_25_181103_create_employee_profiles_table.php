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
        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->id();

            // Relasi utama ke tabel users (One-to-One)
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Relasi penempatan karyawan ke tabel stores (Toko/Gudang mana dia bekerja)
            $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();

            // Biodata & Kontak
            $table->string('kontak', 20)->unique()->nullable();
            $table->text('alamat')->nullable();
            $table->string('avatar')->nullable();
            $table->string('jabatan', 50)->nullable();
            $table->string('nik', 50)->unique()->nullable();
            $table->date('tanggal_bergabung')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_profiles');
    }
};
