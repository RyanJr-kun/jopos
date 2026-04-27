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
        if (!Schema::hasTable('incomes')) {
            Schema::create('incomes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
                $table->foreignId('transaction_category_id')->constrained('transaction_categories')->onDelete('restrict');
                $table->dateTime('tanggal');
                $table->decimal('jumlah', 15, 0);
                $table->string('referensi')->nullable()->unique();
                $table->string('keterangan');
                $table->text('description')->nullable();
                $table->foreignId('user_id')->constrained('users');
                $table->timestamps();

                $table->index('transaction_category_id');
                $table->index('tanggal');
                $table->index('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
