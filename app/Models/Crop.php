<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Crop extends Model
{
    protected $fillable = [
        'name',
        'status',
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
}
