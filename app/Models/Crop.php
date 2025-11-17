<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Crop extends Model
{
    protected $fillable = [
        'name',
        'description',
        'variety',
        'yield_per_hectare',
        'status',
        'plot_id',
        'photo',
    ];

    protected $casts = [
        'yield_per_hectare' => 'decimal:2',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function supplyConsumptions(): HasMany
    {
        return $this->hasMany(SupplyConsumption::class);
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(LedgerEntry::class);
    }

    public function plot(): BelongsTo
    {
        return $this->belongsTo(Plot::class);
    }

}
