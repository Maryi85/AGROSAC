<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Supply;
use App\Models\SupplyConsumption;
use App\Models\SupplyMovement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskService
{
    /**
     * Get query builder with filters applied.
     */
    public function getQuery(array $filters = []): Builder
    {
        $query = Task::query()
            ->with(['assignee', 'plot', 'crop', 'approver']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('assignee', fn ($subQ) => $subQ->where('name', 'like', "%{$search}%"));
            });
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('scheduled_for', 'desc');
    }

    /**
     * Get paginated tasks properly filtered.
     */
    public function getPaginatedTasks(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->getQuery($filters)
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get all tasks properly filtered (for PDF/Export).
     */
    public function getAllTasks(array $filters = []): Collection
    {
        return $this->getQuery($filters)->get();
    }

    /**
     * Create a new task handling payment logic.
     */
    public function create(array $data): Task
    {
        $data = $this->normalizePaymentData($data);
        return Task::create($data);
    }

    /**
     * Update a task handling payment logic.
     */
    public function update(Task $task, array $data): bool
    {
        $data = $this->normalizePaymentData($data);
        return $task->update($data);
    }

    /**
     * Complete a task (Worker action).
     */
    public function complete(Task $task, array $data): bool
    {
        return DB::transaction(function () use ($task, $data) {
            $task->update([
                'status' => 'completed',
                'hours' => $data['hours_worked'] ?? $task->hours,
                'kilos' => $data['quantity_harvested'] ?? $task->kilos,
            ]);

            $this->triggerSupplyConsumption($task);

            return true;
        });
    }

    /**
     * Approve a task (Admin action).
     */
    public function approve(Task $task): bool
    {
        return DB::transaction(function () use ($task) {
            // If it wasn't completed (maybe an admin force-approves), ensure consumption happens
            if ($task->status !== 'completed' && $task->status !== 'approved') {
                $this->triggerSupplyConsumption($task);
            }

            return $task->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        });
    }

    /**
     * Invalidate a task (Admin action).
     */
    public function invalidate(Task $task): bool
    {
        return DB::transaction(function () use ($task) {
            $task->update([
                'status' => 'invalid',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $this->reverseSupplyConsumption($task);

            return true;
        });
    }

    /**
     * Record supply consumption and movement when task is completed/approved.
     */
    protected function triggerSupplyConsumption(Task $task): void
    {
        if (empty($task->supplies_data) || !is_array($task->supplies_data)) {
            return;
        }

        foreach ($task->supplies_data as $item) {
            if (empty($item['supply_id']) || empty($item['quantity'])) {
                continue;
            }

            $supply = Supply::find($item['supply_id']);
            if (!$supply) continue;

            $qty = (float) $item['quantity'];
            $totalCost = $qty * $supply->unit_cost;

            // Create Consumption record for accounting
            SupplyConsumption::create([
                'supply_id' => $supply->id,
                'crop_id' => $task->crop_id,
                'plot_id' => $task->plot_id,
                'task_id' => $task->id,
                'qty' => $qty,
                'total_cost' => $totalCost,
                'used_at' => now(),
            ]);

            // Create Movement record for inventory
            SupplyMovement::create([
                'supply_id' => $supply->id,
                'type' => 'exit',
                'quantity' => $qty,
                'unit_cost' => $supply->unit_cost,
                'total_cost' => $totalCost,
                'reason' => "Consumo por tarea: {$task->type}",
                'notes' => "Generado automáticamente al finalizar tarea #{$task->id}",
                'crop_id' => $task->crop_id,
                'plot_id' => $task->plot_id,
                'task_id' => $task->id,
                'created_by' => auth()->id(),
                'movement_date' => now(),
            ]);

            // Update physical stock
            $supply->updateStock();
        }
    }

    /**
     * Remove consumption records if task is invalidated.
     */
    protected function reverseSupplyConsumption(Task $task): void
    {
        // Delete related consumptions
        SupplyConsumption::where('task_id', $task->id)->delete();

        // Related movements
        $movements = SupplyMovement::where('task_id', $task->id)->get();
        foreach ($movements as $movement) {
            $supply = $movement->supply;
            $movement->delete();
            if ($supply) {
                $supply->updateStock();
            }
        }
    }

    /**
     * Normalize payment data based on payment type.
     */
    protected function normalizePaymentData(array $data): array
    {
        // If payment_type is not present, return data as is (might be partial update, though unlikely in this context)
        if (!isset($data['payment_type'])) {
            return $data;
        }

        $paymentType = $data['payment_type'];
        unset($data['payment_type']);

        $totalPayment = 0;

        // Ensure defaults
        $data['hours'] = $data['hours'] ?? 0;
        $data['kilos'] = $data['kilos'] ?? 0;

        if ($paymentType === 'hours') {
            $data['kilos'] = 0;
            $data['price_per_day'] = null;
            $data['price_per_kg'] = null;
            $totalPayment = ($data['hours']) * ($data['price_per_hour'] ?? 0);
        } elseif ($paymentType === 'days') {
            // Check if 'days' key exists, if not, maybe we are calculating from hours? 
            // The controller logic implied 'days' input exists.
            $days = $data['days'] ?? 0;
            $data['hours'] = $days * 8; // Convert days to hours
            $data['kilos'] = 0;
            $data['price_per_hour'] = null;
            $data['price_per_kg'] = null;
            $totalPayment = $days * ($data['price_per_day'] ?? 0);
            unset($data['days']); // Remove auxiliary field
        } else { // quantity / kilos
            $data['hours'] = 0;
            $data['price_per_hour'] = null;
            $data['price_per_day'] = null;
            $totalPayment = ($data['kilos']) * ($data['price_per_kg'] ?? 0);
        }

        // Use calculated total if not explicitly provided or override logic if needed.
        // The original controller checked: $data['total_payment'] = $data['total_payment'] ?? $totalPayment;
        // This accepts a manual override if 'total_payment' is in the request and not null.
        if (!isset($data['total_payment']) || $data['total_payment'] === null) {
            $data['total_payment'] = $totalPayment;
        }

        return $data;
    }
}
