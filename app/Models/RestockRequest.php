<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestockRequest extends Model
{
    use SoftDeletes;

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

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function activity()
    {
        return $this->belongsTo(ActivityLog::class, 'activity_id');
    }
}