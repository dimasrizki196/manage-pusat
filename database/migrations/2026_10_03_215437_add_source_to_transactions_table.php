<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('transaction_source', ['pengajuan', 'urgent'])
                ->nullable()
                ->after('transaction_type');

            $table->foreignId('restock_request_id')
                ->nullable()
                ->after('transaction_source')
                ->constrained('restock_requests')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['restock_request_id']);
            $table->dropColumn([
                'restock_request_id',
                'transaction_source',
            ]);
        });
    }
};
