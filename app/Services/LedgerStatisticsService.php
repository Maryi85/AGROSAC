<?php

namespace App\Services;

use App\Models\Crop;
use App\Models\LedgerEntry;
use App\Models\SupplyConsumption;
use App\Models\SupplyMovement;
use App\Models\Task;
use App\Models\ToolEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class LedgerStatisticsService
{
    /**
     * Get the overall financial summary.
     */
    public function getFinancialSummary(): array
    {
        $totalIncome = LedgerEntry::where('type', 'income')->sum('amount');
        $totalExpenses = LedgerEntry::where('type', 'expense')->sum('amount');

        // Calculate operational costs
        $totalSupplyCosts = SupplyConsumption::sum('total_cost') + 
                           SupplyMovement::where('type', 'exit')->sum('total_cost');
        
        $totalToolCosts = ToolEntry::where('type', 'purchase')
                           ->whereNotNull('total_cost')
                           ->sum('total_cost');
                           
        $totalTaskCosts = Task::sum('total_payment');

        // Total effective expenses (accounting + operational)
        $totalAllExpenses = $totalExpenses + $totalSupplyCosts + $totalToolCosts + $totalTaskCosts;
        $netProfit = $totalIncome - $totalExpenses; // Accounting profit
        $totalProfit = $totalIncome - $totalAllExpenses; // Real profit

        return compact(
            'totalIncome', 
            'totalExpenses', 
            'netProfit', 
            'totalSupplyCosts', 
            'totalToolCosts', 
            'totalTaskCosts', 
            'totalAllExpenses', 
            'totalProfit'
        );
    }

    /**
     * Get income statistics grouped by category.
     */
    public function getIncomeByCategory()
    {
        return LedgerEntry::where('type', 'income')
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get();
    }

    /**
     * Get expense statistics grouped by category.
     */
    public function getExpensesByCategory()
    {
        return LedgerEntry::where('type', 'expense')
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get();
    }

    /**
     * Get income by crop.
     */
    public function getIncomeByCrop()
    {
        return LedgerEntry::where('type', 'income')
            ->whereNotNull('crop_id')
            ->with('crop')
            ->select('crop_id', DB::raw('SUM(amount) as total'))
            ->groupBy('crop_id')
            ->orderBy('total', 'desc')
            ->get();
    }

    /**
     * Get expenses by crop.
     */
    public function getExpensesByCrop()
    {
        return LedgerEntry::where('type', 'expense')
            ->whereNotNull('crop_id')
            ->with('crop')
            ->select('crop_id', DB::raw('SUM(amount) as total'))
            ->groupBy('crop_id')
            ->orderBy('total', 'desc')
            ->get();
    }

    /**
     * Get detailed crop analysis avoiding N+1 queries.
     */
    public function getCropAnalysis(): array
    {
        $crops = Crop::with('plot')->get();
        
        // 1. Bulk fetch all financial data grouped by crop_id
        $incomes = LedgerEntry::where('type', 'income')
            ->select('crop_id', DB::raw('SUM(amount) as total'))
            ->whereNotNull('crop_id')
            ->groupBy('crop_id')
            ->pluck('total', 'crop_id');

        $expenses = LedgerEntry::where('type', 'expense')
            ->select('crop_id', DB::raw('SUM(amount) as total'))
            ->whereNotNull('crop_id')
            ->groupBy('crop_id')
            ->pluck('total', 'crop_id');

        $consumptions = SupplyConsumption::select('crop_id', DB::raw('SUM(total_cost) as total'))
            ->whereNotNull('crop_id')
            ->groupBy('crop_id')
            ->pluck('total', 'crop_id');

        $movements = SupplyMovement::where('type', 'exit')
            ->select('crop_id', DB::raw('SUM(total_cost) as total'))
            ->whereNotNull('crop_id')
            ->groupBy('crop_id')
            ->pluck('total', 'crop_id');

        $tasks = Task::select('crop_id', DB::raw('SUM(total_payment) as total'))
            ->whereNotNull('crop_id')
            ->groupBy('crop_id')
            ->pluck('total', 'crop_id');

        // Tool cost distribution parameters
        $totalToolPurchases = ToolEntry::where('type', 'purchase')
            ->whereNotNull('total_cost')
            ->sum('total_cost');

        $totalTasksCount = Task::whereNotNull('crop_id')->count();
        $tasksPerCrop = Task::select('crop_id', DB::raw('COUNT(*) as count'))
            ->whereNotNull('crop_id')
            ->groupBy('crop_id')
            ->pluck('count', 'crop_id');

        $analysis = [];

        foreach ($crops as $crop) {
            $id = $crop->id;
            
            $ledgerIncome = $incomes->get($id, 0);
            $ledgerExpenses = $expenses->get($id, 0);
            $supplyConsumptionCosts = $consumptions->get($id, 0);
            $supplyMovementCosts = $movements->get($id, 0);
            $taskCosts = $tasks->get($id, 0);
            
            // Proportional tool calculation
            $cropTasksCount = $tasksPerCrop->get($id, 0);
            $toolCosts = 0;
            if ($totalTasksCount > 0 && $totalToolPurchases > 0) {
                 $toolCosts = ($cropTasksCount / $totalTasksCount) * $totalToolPurchases;
            }

            $totalCropCosts = $supplyConsumptionCosts + $supplyMovementCosts + $taskCosts + $toolCosts;
            $totalCropExpenses = $ledgerExpenses + $totalCropCosts;
            $cropProfit = $ledgerIncome - $totalCropExpenses;

            $analysis[] = [
                'crop' => $crop,
                'income' => $ledgerIncome,
                'expenses' => [
                    'ledger' => $ledgerExpenses,
                    'supply_consumption' => $supplyConsumptionCosts,
                    'supply_movement' => $supplyMovementCosts,
                    'tasks' => $taskCosts,
                    'tools' => $toolCosts,
                    'total_costs' => $totalCropCosts,
                    'total' => $totalCropExpenses,
                ],
                'profit' => $cropProfit,
            ];
        }

        // Sort by profit descending
        usort($analysis, function($a, $b) {
            return $b['profit'] <=> $a['profit'];
        });

        return $analysis;
    }

    /**
     * Get monthly trend data for the last X months.
     */
    public function getMonthlyTrends(int $months = 6)
    {
        // ... (Optimization for monthly trends could also be done here similarly)
        // For now, mirroring existing logic but cleaner
        // Ideally this should also be bulk fetched but requires efficient date grouping in SQL
        
        return LedgerEntry::select(
                DB::raw('DATE_FORMAT(occurred_at, "%Y-%m") as month'),
                DB::raw('SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as income'),
                DB::raw('SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as ledger_expenses')
            )
            ->where('occurred_at', '>=', now()->subMonths($months)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function($item) {
                // Determine expenses for this month window to add to ledger expenses
                $monthStart = $item->month . '-01';
                $monthEnd = date('Y-m-t', strtotime($monthStart));

                // We could optimize this further with a single massive query, 
                // but for 6 items (months), these separate queries are acceptable trade-off for readability 
                // vs the N+1 on 50 crops.
                
                $supplyCosts = SupplyConsumption::whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_cost') +
                              SupplyMovement::where('type', 'exit')->whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_cost');

                $taskCosts = Task::whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_payment');
                
                $toolCosts = ToolEntry::where('type', 'purchase')
                    ->whereNotNull('total_cost')
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->sum('total_cost');

                $totalExpenses = $item->ledger_expenses + $supplyCosts + $taskCosts + $toolCosts;
                
                // Format month label
                $monthDate = \Carbon\Carbon::createFromFormat('Y-m', $item->month);
                $monthLabel = ucfirst($monthDate->translatedFormat('F'));

                return [
                    'month' => $item->month,
                    'month_label' => $monthLabel,
                    'income' => $item->income,
                    'expenses' => $totalExpenses
                ];
            });
    }
}
