<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToolEntry extends Model
{
    protected $fillable = [
        'tool_id',
        'quantity',
        'damaged_qty',
        'lost_qty',
        'available_qty',
        'type',
        'unit_cost',
        'total_cost',
        'entry_date',
        'supplier',
        'invoice_number',
        'notes',
        'damage_notes',
        'loss_notes',
        'damage_date',
        'loss_date',
        'created_by',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'entry_date' => 'date',
        'damage_date' => 'date',
        'loss_date' => 'date',
    ];

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Métodos helper para tipos de entrada
    public function isPurchase(): bool
    {
        return $this->type === 'purchase';
    }

    public function isDonation(): bool
    {
        return $this->type === 'donation';
    }

    public function isTransfer(): bool
    {
        return $this->type === 'transfer';
    }

    public function isRepair(): bool
    {
        return $this->type === 'repair';
    }

    // Calcular costo total automáticamente y establecer available_qty
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($toolEntry) {
            // Calcular costo total
            if ($toolEntry->unit_cost && $toolEntry->quantity) {
                $toolEntry->total_cost = $toolEntry->unit_cost * $toolEntry->quantity;
            }
            
            // Inicializar campos de daño y pérdida si no están establecidos
            if (is_null($toolEntry->damaged_qty)) {
                $toolEntry->damaged_qty = 0;
            }
            if (is_null($toolEntry->lost_qty)) {
                $toolEntry->lost_qty = 0;
            }
            
            // Si es una nueva entrada (no existe en BD), inicializar available_qty con quantity
            if (!$toolEntry->exists) {
                if (is_null($toolEntry->available_qty) || $toolEntry->available_qty == 0) {
                    $toolEntry->available_qty = $toolEntry->quantity;
                }
            } else {
                // Si es una actualización, recalcular available_qty basado en quantity, damaged_qty y lost_qty
                $damaged = $toolEntry->damaged_qty ?? 0;
                $lost = $toolEntry->lost_qty ?? 0;
                $available = $toolEntry->quantity - $damaged - $lost;
                $toolEntry->available_qty = max(0, $available);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'id';
    }
}