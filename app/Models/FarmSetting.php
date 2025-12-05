<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FarmSetting extends Model
{
    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'boundary',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'boundary' => 'array',
    ];

    /**
     * Obtener la configuración de la finca (singleton)
     */
    public static function getFarmSettings(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Finca AGROSAC',
                'address' => null,
                'latitude' => null,
                'longitude' => null,
                'boundary' => null,
            ]
        );
    }
}
