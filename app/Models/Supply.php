<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supply extends Model
{
    protected $fillable = [
        'name',
        'unit',
        'unit_cost',
        'status',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
    ];

    public function consumptions(): HasMany
    {
        return $this->hasMany(SupplyConsumption::class);
    }
}
