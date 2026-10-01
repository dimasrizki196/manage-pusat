<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Unit extends Model
{
    protected $table = 'units';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'activity_id',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(ActivityLog::class, 'activity_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
