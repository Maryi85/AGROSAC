<?php

namespace App\Services;

use App\Models\Loan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

class LoanService
{
    /**
     * Get query builder with filters applied.
     */
    public function getQuery(array $filters = []): Builder
    {
        $query = Loan::with(['tool', 'user', 'approvedBy', 'returnedBy']);

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['tool_id']) && $filters['tool_id'] !== 'all') {
            $query->where('tool_id', $filters['tool_id']);
        }

        if (!empty($filters['user_id']) && $filters['user_id'] !== 'all') {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('tool', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get paginated loans properly filtered.
     */
    public function getPaginatedLoans(array $filters = [], int $perPage = 10)
    {
        return $this->getQuery($filters)
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get all loans properly filtered (for PDF/Export).
     */
    public function getAllLoans(array $filters = []): Collection
    {
        return $this->getQuery($filters)->get();
    }

    /**
     * Approve a loan request.
     *
     * @throws Exception If not enough stock or other error.
     */
    public function approve(Loan $loan, ?string $adminNotes): void
    {
        DB::transaction(function () use ($loan, $adminNotes) {
            // Verificar disponibilidad antes de aprobar
            $tool = $loan->tool;
            if ($tool->available_qty < $loan->quantity) {
                throw new Exception('No hay suficientes herramientas disponibles para aprobar este préstamo.');
            }

            // Actualizar el préstamo
            $loan->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'admin_notes' => $adminNotes,
            ]);

            // Actualizar la cantidad disponible de la herramienta
            $tool->decrementAvailableQty($loan->quantity);

            // Marcar como 'out' y establecer out_at
            $loan->update([
                'status' => 'out',
                'out_at' => now(),
            ]);
        });
    }

    /**
     * Reject a loan request.
     */
    public function reject(Loan $loan, string $adminNotes): void
    {
        $loan->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'admin_notes' => $adminNotes,
        ]);
    }

    /**
     * Confirm return of a tool (Admin action).
     */
    public function confirmReturn(Loan $loan, ?string $adminNotes): void
    {
        DB::transaction(function () use ($loan, $adminNotes) {
            // Actualizar el préstamo con las notas del administrador
            $loan->update([
                'admin_notes' => $adminNotes,
                'status' => 'returned', // Confirmar que está devuelto
            ]);

            // Devolver la cantidad a la herramienta (si no estaba ya devuelta)
            // Nota: En la lógica original, confirmReturn se usaba cuando el trabajador ya había marcado "returned_by_worker".
            // En ese estado, el stock NO se había incrementado aún (según lógica usual, el stock vuelve al confirmar).
            // Revisando controlador original: confirmReturn incrementa stock.
            $loan->tool->incrementAvailableQty($loan->quantity);
        });
    }

    /**
     * Return a loan (Admin force return or immediate return).
     */
    public function return(Loan $loan): void
    {
        DB::transaction(function () use ($loan) {
            // Marcar como devuelto
            $loan->update([
                'status' => 'returned',
                'returned_at' => now(),
            ]);

            // Devolver la cantidad a la herramienta
            $loan->tool->incrementAvailableQty($loan->quantity);
        });
    }

    /**
     * Mark loan as lost.
     */
    public function markAsLost(Loan $loan): void
    {
        DB::transaction(function () use ($loan) {
            // Marcar como perdido
            $loan->update([
                'status' => 'lost',
                'returned_at' => now(),
            ]);

            // Marcar como perdida en el inventario
            $loan->tool->markAsLost($loan->quantity);
        });
    }

    /**
     * Mark loan as damaged.
     */
    public function markAsDamaged(Loan $loan): void
    {
        DB::transaction(function () use ($loan) {
            // Marcar como dañado
            $loan->update([
                'status' => 'damaged',
                'returned_at' => now(),
            ]);

            // Marcar como dañada en el inventario
            $loan->tool->markAsDamaged($loan->quantity);
        });
    }

    /**
     * Process approved loan (mark as out).
     */
    public function processApproved(Loan $loan): void
    {
        $loan->update([
            'status' => 'out',
            'out_at' => now(),
        ]);
    }
}
