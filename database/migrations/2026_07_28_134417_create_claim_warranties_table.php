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
            $table->foreignId('sale_item_warranty_id')->constrained('sale_item_warranties');
            $table->string('channel')->comment('pos, marketplace');
            $table->date('tanggal_claim');
            $table->text('keluhan');
            $table->string('bukti')->nullable();
            $table->string('metode_pengiriman')->nullable();
            $table->string('nomor_resi')->nullable();
            $table->string('status')->default('Pending');
            $table->string('tipe_resolusi')->nullable();
            $table->text('catatan_teknisi')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('employee_profiles');
            $table->dateTime('customer_notified_at')->nullable();
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
