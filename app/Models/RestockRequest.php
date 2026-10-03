<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestockRequest extends Model
{
    protected $table = 'restock_requests';

    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'user_id',
        'quantity',
        'note',
        'status',
        'processed_by',
        'processed_at',
        'rejection_reason',
        'activity_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'processed_by'
        );
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
}
