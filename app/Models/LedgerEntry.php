<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LedgerEntry extends Model
{
    protected $fillable = [
        'type',
        'category',
        'amount',
        'occurred_at',
        'crop_id',
        'plot_id',
        'reference',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'occurred_at' => 'date',
    ];

    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }

    public function plot(): BelongsTo
    {
        return $this->belongsTo(Plot::class);
    }
}
