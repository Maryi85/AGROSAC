<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplyConsumption extends Model
{
    protected $fillable = [
        'supply_id',
        'crop_id',
        'plot_id',
        'task_id',
        'qty',
        'total_cost',
        'used_at',
    ];

    protected $casts = [
        'qty' => 'decimal:3',
        'total_cost' => 'decimal:2',
        'used_at' => 'date',
    ];

    public function supply(): BelongsTo
    {
        return $this->belongsTo(Supply::class);
    }

    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }

    public function plot(): BelongsTo
    {
        return $this->belongsTo(Plot::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
