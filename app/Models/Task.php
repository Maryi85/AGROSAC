<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        "type",
        "description",
        "plot_id",
        "crop_id",
        "assigned_to",
        "scheduled_for",
        "status",
        "hours",
        "kilos",
        "price_per_hour",
        "price_per_day",
        "price_per_kg",
        "total_payment",
        "estimated_hours",
        "estimated_total_payment",
        "creator_id",
        "approved_by",
        "approved_at",
        "supplies_data",
    ];

    protected $casts = [
        "scheduled_for" => "date",
        "approved_at" => "datetime",
        "hours" => "decimal:2",
        "kilos" => "decimal:3",
        "price_per_hour" => "decimal:2",
        "price_per_day" => "decimal:2",
        "price_per_kg" => "decimal:2",
        "total_payment" => "decimal:2",
        "estimated_hours" => "decimal:2",
        "estimated_total_payment" => "decimal:2",
        "supplies_data" => "array",
    ];

    protected $attributes = [
        "hours" => 0,
        "kilos" => 0,
        "total_payment" => 0,
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
        return $this->belongsTo(User::class, "assigned_to");
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, "approved_by");
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, "creator_id");
    }

    public function getEffectivePaymentAttribute(): float
    {
        if ($this->total_payment && $this->total_payment > 0) {
            return (float) $this->total_payment;
        }

        if ($this->price_per_hour && $this->hours > 0) {
            return (float) ($this->hours * $this->price_per_hour);
        }

        if ($this->price_per_day && $this->hours > 0) {
            $days = $this->hours / 8;
            return (float) ($days * $this->price_per_day);
        }

        if ($this->price_per_kg && $this->kilos > 0) {
            return (float) ($this->kilos * $this->price_per_kg);
        }

        return 0.0;
    }
}
