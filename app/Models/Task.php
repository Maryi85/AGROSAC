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
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'scheduled_for' => 'date',
        'approved_at' => 'datetime',
        'hours' => 'decimal:2',
        'kilos' => 'decimal:3',
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
