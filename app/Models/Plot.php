<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plot extends Model
{
    protected $fillable = [
        'name',
        'location',
        'latitude',
        'longitude',
        'boundary',
        'area',
        'status',
    ];

    protected $casts = [
        'area' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'boundary' => 'array',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function supplyConsumptions(): HasMany
    {
        return $this->hasMany(SupplyConsumption::class);
    }

    public function crops(): HasMany
    {
        return $this->hasMany(Crop::class);
    }

}
