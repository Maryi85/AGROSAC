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
        'current_stock',
        'min_stock',
        'category',
        'description',
        'status',
        'photo',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'current_stock' => 'decimal:3',
        'min_stock' => 'decimal:3',
    ];

    public function consumptions(): HasMany
    {
        return $this->hasMany(SupplyConsumption::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(SupplyMovement::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(SupplyMovement::class)->where('type', 'entry');
    }

    public function exits(): HasMany
    {
        return $this->hasMany(SupplyMovement::class)->where('type', 'exit');
    }

    public function updateStock(): void
    {
        $totalEntries = $this->entries()->sum('quantity');
        $totalExits = $this->exits()->sum('quantity');
        $this->current_stock = $totalEntries - $totalExits;
        $this->save();
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->min_stock;
    }
}
