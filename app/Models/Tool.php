<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tool extends Model
{
    protected $fillable = [
        'name',
        'category',
        'status',
        'total_qty',
        'available_qty',
    ];

    protected $casts = [
        'total_qty' => 'integer',
        'available_qty' => 'integer',
    ];

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
