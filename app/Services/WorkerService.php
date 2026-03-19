<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class WorkerService
{
    /**
     * Get aggregated statistics for a worker efficiently.
     */
    public function getStats(User $worker): array
    {
        // Use a single query to get counts by status
        $counts = Task::where('assigned_to', $worker->id)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status IN ('pending', 'in_progress') THEN 1 ELSE 0 END) as pending
            ")
            ->first();

        // Calculate totals for approved tasks (hours and kilos)
        $totals = Task::where('assigned_to', $worker->id)
            ->where('status', 'approved')
            ->selectRaw("
                SUM(hours) as total_hours,
                SUM(CASE WHEN type = 'harvest' THEN kilos ELSE 0 END) as total_kilos
            ")
            ->first();

        return [
            'total_tasks' => $counts->total ?? 0,
            'completed_tasks' => $counts->completed ?? 0,
            'approved_tasks' => $counts->approved ?? 0,
            'pending_tasks' => $counts->pending ?? 0,
            'total_hours' => $totals->total_hours ?? 0,
            'total_kilos' => $totals->total_kilos ?? 0,
        ];
    }

    /**
     * Get processed report data for a worker.
     */
    public function getReportData(User $worker): array
    {
        // Get all approved/completed tasks with relations
        $tasks = Task::where('assigned_to', $worker->id)
            ->whereIn('status', ['approved', 'completed'])
            ->with(['crop', 'plot'])
            ->orderBy('scheduled_for', 'desc')
            ->get();

        // Calculate totals using collection methods + new accessor
        $totalPayment = $tasks->sum(fn($task) => $task->effective_payment);
        $totalHours = $tasks->sum('hours');
        $totalKilos = $tasks->sum('kilos');
        $totalTasks = $tasks->count();

        // Group by crop
        $cropTotals = $tasks->groupBy('crop_id')->map(function ($tasks) {
            $crop = $tasks->first()->crop;
            return [
                'crop' => $crop ? $crop->name : 'Sin cultivo',
                'tasks_count' => $tasks->count(),
                'total_payment' => $tasks->sum(fn($t) => $t->effective_payment),
                'total_hours' => $tasks->sum('hours'),
                'total_kilos' => $tasks->sum('kilos'),
            ];
        });

        return [
            'tasks' => $tasks,
            'totalPayment' => $totalPayment,
            'totalHours' => $totalHours,
            'totalKilos' => $totalKilos,
            'totalTasks' => $totalTasks,
            'cropTotals' => $cropTotals
        ];
    }
}
