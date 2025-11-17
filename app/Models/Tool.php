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
        'description',
        'brand',
        'model',
        'serial_number',
        'photo',
    ];

    protected $casts = [
        // No hay casts de inventario aquí
    ];

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(ToolEntry::class);
    }

    // Métodos de cálculo de inventario basados en entradas
    public function getTotalEntriesAttribute(): int
    {
        return $this->entries()->sum('quantity');
    }

    public function getAvailableQtyAttribute(): int
    {
        $available = $this->entries()->sum('available_qty');
        return $available > 0 ? $available : 0;
    }

    public function getDamagedQtyAttribute(): int
    {
        return $this->entries()->sum('damaged_qty') ?: 0;
    }

    public function getLostQtyAttribute(): int
    {
        return $this->entries()->sum('lost_qty') ?: 0;
    }

    public function getTotalExistenceAttribute(): int
    {
        return $this->total_entries;
    }

    public function isAvailable(): bool
    {
        return $this->total_entries > 0;
    }

    /**
     * Obtener el estado del inventario basado en las cantidades
     */
    public function getInventoryStatusAttribute(): string
    {
        if ($this->total_entries == 0) {
            return 'empty';
        }
        
        if ($this->lost_qty > 0 && $this->lost_qty >= $this->total_entries) {
            return 'lost';
        }
        
        if ($this->damaged_qty > 0 && $this->damaged_qty >= $this->total_entries) {
            return 'damaged';
        }
        
        return 'available';
    }

    /**
     * Incrementar la cantidad disponible de la herramienta
     * Actualiza las entradas de ToolEntry distribuyendo la cantidad
     */
    public function incrementAvailableQty(int $quantity): void
    {
        if ($quantity <= 0) {
            return;
        }

        // Obtener entradas disponibles ordenadas por fecha
        $entries = $this->entries()
            ->where('available_qty', '>', 0)
            ->orderBy('entry_date', 'desc')
            ->get();

        $remaining = $quantity;

        // Si hay entradas disponibles, actualizar las más recientes primero
        foreach ($entries as $entry) {
            if ($remaining <= 0) {
                break;
            }
            $entry->increment('available_qty', $remaining);
            $remaining = 0;
        }

        // Si aún hay cantidad restante, distribuir en todas las entradas
        if ($remaining > 0) {
            $allEntries = $this->entries()->get();
            if ($allEntries->count() > 0) {
                $perEntry = intval($remaining / $allEntries->count());
                $extra = $remaining % $allEntries->count();

                foreach ($allEntries as $index => $entry) {
                    $addQty = $perEntry + ($index < $extra ? 1 : 0);
                    if ($addQty > 0) {
                        $entry->increment('available_qty', $addQty);
                    }
                }
            }
        }
    }

    /**
     * Decrementar la cantidad disponible de la herramienta
     * Actualiza las entradas de ToolEntry distribuyendo la cantidad
     */
    public function decrementAvailableQty(int $quantity): void
    {
        if ($quantity <= 0) {
            return;
        }

        // Obtener entradas disponibles ordenadas por fecha (más antiguas primero)
        $entries = $this->entries()
            ->where('available_qty', '>', 0)
            ->orderBy('entry_date', 'asc')
            ->get();

        $remaining = $quantity;

        // Reducir de las entradas más antiguas primero
        foreach ($entries as $entry) {
            if ($remaining <= 0) {
                break;
            }

            $available = $entry->available_qty;
            $toRemove = min($available, $remaining);
            
            $entry->decrement('available_qty', $toRemove);
            $remaining -= $toRemove;
        }
    }

    /**
     * Marcar herramientas como perdidas
     */
    public function markAsLost(int $quantity): void
    {
        if ($quantity <= 0) {
            return;
        }

        // Obtener entradas disponibles ordenadas por fecha (más antiguas primero)
        $entries = $this->entries()
            ->where('available_qty', '>', 0)
            ->orderBy('entry_date', 'asc')
            ->get();

        $remaining = $quantity;

        foreach ($entries as $entry) {
            if ($remaining <= 0) {
                break;
            }

            $available = $entry->available_qty;
            $toMark = min($available, $remaining);
            
            $entry->increment('lost_qty', $toMark);
            $entry->decrement('available_qty', $toMark);
            $remaining -= $toMark;
        }
    }

    /**
     * Marcar herramientas como dañadas
     */
    public function markAsDamaged(int $quantity): void
    {
        if ($quantity <= 0) {
            return;
        }

        // Obtener entradas disponibles ordenadas por fecha (más antiguas primero)
        $entries = $this->entries()
            ->where('available_qty', '>', 0)
            ->orderBy('entry_date', 'asc')
            ->get();

        $remaining = $quantity;

        foreach ($entries as $entry) {
            if ($remaining <= 0) {
                break;
            }

            $available = $entry->available_qty;
            $toMark = min($available, $remaining);
            
            $entry->increment('damaged_qty', $toMark);
            $entry->decrement('available_qty', $toMark);
            $remaining -= $toMark;
        }
    }
}
