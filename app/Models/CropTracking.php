<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CropTracking extends Model
{
    protected $table = 'crop_tracking';
    
    protected $fillable = [
        'tracking_date',
        'plot_id',
        'crop_id',
        'phase',
        'cut_date',
        'activities',
        'status',
        'created_by'
    ];

    protected $casts = [
        'tracking_date' => 'date',
        'cut_date' => 'date',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    public function plot(): BelongsTo
    {
        return $this->belongsTo(Plot::class);
    }

    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
