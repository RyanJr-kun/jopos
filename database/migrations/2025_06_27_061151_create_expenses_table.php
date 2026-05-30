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
        if (!Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
                $table->dateTime('tanggal');
                $table->decimal('jumlah', 15, 0);
                $table->string('metode_pembayaran', 30)->default('TUNAI');
                $table->foreignId('bank_id')->nullable()->constrained('banks')->onDelete('set null');
                $table->foreignId('user_id')->constrained('users');
                $table->foreignId('transaction_category_id')->constrained('transaction_categories')->onDelete('restrict');
                $table->string('referensi');
                $table->string('keterangan');
                $table->text('description')->nullable();
                $table->timestamps();

                $table->index('tanggal');
                $table->index('user_id');
                $table->index('transaction_category_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
