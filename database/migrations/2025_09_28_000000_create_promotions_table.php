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
        if (!Schema::hasTable('promotions')) {
            Schema::create('promotions', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique()->nullable();
                $table->enum('type', ['percentage', 'fixed']);
                $table->decimal('nilai_diskon', 10, 0);
                $table->decimal('min_pembelian', 15, 0)->default(0);
                $table->decimal('max_diskon', 15, 0)->nullable();
                $table->dateTime('tanggal_mulai');
                $table->dateTime('tanggal_berakhir');
                $table->boolean('is_all_products')->default(false);
                $table->boolean('status')->default(true);
                $table->text('description')->nullable();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
