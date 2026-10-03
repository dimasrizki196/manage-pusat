<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $table = 'transactions';

    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'user_id',
        'transaction_type',
        'transaction_source',
        'restock_request_id',
        'quantity',
        'stock_before',
        'stock_after',
        'activity_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'stock_before' => 'decimal:2',
        'stock_after' => 'decimal:2',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function restockRequest(): BelongsTo
    {
        return $this->belongsTo(RestockRequest::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(ActivityLog::class, 'activity_id');
    }
}
