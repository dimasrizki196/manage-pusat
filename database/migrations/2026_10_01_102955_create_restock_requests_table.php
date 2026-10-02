<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restock_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('item_id')
                ->constrained('items')
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->decimal('quantity', 12, 2);

            $table->text('note')->nullable();

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
            ])->default('pending');

            $table->foreignId('processed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('processed_at')
                ->nullable();

            $table->text('rejection_reason')
                ->nullable();

            $table->foreignId('activity_id')
                ->nullable()
                ->constrained('activity_logs', 'id')
                ->nullOnDelete();

            $table->index('status');
            $table->index('item_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restock_requests');
    }
};