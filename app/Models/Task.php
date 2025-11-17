<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'type',
        'description',
        'plot_id',
        'crop_id',
        'assigned_to',
        'scheduled_for',
        'status',
        'hours',
        'kilos',
        'price_per_hour',
        'price_per_day',
        'price_per_kg',
        'total_payment',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'scheduled_for' => 'date',
        'approved_at' => 'datetime',
        'hours' => 'decimal:2',
        'kilos' => 'decimal:3',
        'price_per_hour' => 'decimal:2',
        'price_per_day' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
        'total_payment' => 'decimal:2',
    ];

    public function plot(): BelongsTo
    {
        return $this->belongsTo(Plot::class);
    }

    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
