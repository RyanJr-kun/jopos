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
        Schema::create('claim_warranties', function (Blueprint $table) {
            $table->id();
            $table->string('nomer_claim')->unique();
            $table->foreignId('warranty_id')->constrained('warranties');
            $table->string('channel')->comment('pos / marketplace');
            $table->date('tanggal_claim');
            $table->text('keluhan')->nullable();
            $table->string('bukti');
            $table->string('metode_pengiriman');
            $table->string('nomor_resi');
            $table->string('status');
            $table->string('tipe_resolusi');
            $table->text('catatan_teknisi')->nullable();
            $table->date('tanggal_selesai');
            $table->foreignId('handled_by')->constrained('employees');
            $table->dateTime('customer_notified_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claim_warranties');
    }
};
