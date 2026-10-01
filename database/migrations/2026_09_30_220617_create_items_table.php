<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            $table->string('name');

            $table->foreignId('unit_id')
                ->constrained('units')
                ->restrictOnDelete();

            $table->decimal('stock', 12, 2)
                ->default(0);

            $table->decimal('stock_min', 12, 2)
                ->default(0);

            $table->foreignId('activity_id')
                ->nullable()
                ->constrained('activity_logs')
                ->nullOnDelete();

            $table->index('category_id');
            $table->index('unit_id');
            $table->index('activity_id');

            $table->unique([
                'category_id',
                'name',
                'unit_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
