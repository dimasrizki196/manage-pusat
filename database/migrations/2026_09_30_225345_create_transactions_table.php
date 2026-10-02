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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // Relasi Foreign Key
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Kolom Tipe Transaksi
            $table->string('transaction_type');

            // Kolom Desimal (total 10 digit, 2 digit di belakang koma)
            $table->decimal('quantity', 10, 2);
            $table->decimal('stock_before', 10, 2);
            $table->decimal('stock_after', 10, 2);

            // Relasi ke tabel log aktivitas (asumsi nama tabel: activity_logs)
            $table->foreignId('activity_id')->nullable()->constrained('activity_logs')->nullOnDelete();

            // public $timestamps = false; pada model berarti tidak ada $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
