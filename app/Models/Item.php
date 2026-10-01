<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'name',
        'unit_id',
        'stock',
        'stock_min',
        'activity_id',
    ];

    protected $casts = [
        'stock' => 'decimal:2',
        'stock_min' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(
            ActivityLog::class,
            'activity_id'
        );
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->stock_min;
    }

    public function isInactive(): bool
    {
        return $this->activity?->deleted_at !== null;
    }
}
