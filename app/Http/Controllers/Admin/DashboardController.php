<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plot;
use App\Models\Crop;
use App\Models\Task;
use App\Models\Loan;
use App\Models\Supply;
use App\Models\LedgerEntry;
use App\Models\FarmSetting;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Usuarios activos (todos los usuarios registrados)
        $totalUsers = User::count();
        $activeUsers = User::where('role', '!=', 'admin')->count(); // Excluir admin del conteo

        // Lotes registrados
        $totalPlots = Plot::count();
        $activePlots = Plot::where('status', 'active')->count();

        // Cultivos activos
        $totalCrops = Crop::count();
        $activeCrops = Crop::where('status', 'active')->count();

        // Tareas pendientes
        $pendingTasks = Task::where('status', 'pending')->count();
        $completedTasks = Task::where('status', 'completed')->count();

        // Préstamos activos
        $activeLoans = Loan::where('status', 'active')->count();

        // Insumos disponibles (todos los insumos activos)
        $availableSupplies = Supply::where('status', 'active')->count();

        // Estadísticas financieras básicas
        $totalIncome = LedgerEntry::where('type', 'income')->sum('amount');
        $totalExpenses = LedgerEntry::where('type', 'expense')->sum('amount');
        $netProfit = $totalIncome - $totalExpenses;

        // Usuarios por rol
        $usersByRole = User::select('role', DB::raw('count(*) as count'))
            ->where('role', '!=', 'admin')
            ->groupBy('role')
            ->get();

        // Lotes por estado
        $plotsByStatus = Plot::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Cultivos por estado
        $cropsByStatus = Crop::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Tareas recientes
        $recentTasks = Task::with(['plot', 'assignee'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Movimientos recientes del ledger
        $recentLedgerEntries = LedgerEntry::with(['crop', 'plot'])
            ->orderBy('occurred_at', 'desc')
            ->limit(5)
            ->get();

        // Configuración de la finca para el mapa
        $farmSettings = FarmSetting::getFarmSettings();

        // Balance financiero del mes actual
        $currentMonthIncome = LedgerEntry::where('type', 'income')
            ->whereYear('occurred_at', now()->year)
            ->whereMonth('occurred_at', now()->month)
            ->sum('amount');

        $currentMonthExpenses = LedgerEntry::where('type', 'expense')
            ->whereYear('occurred_at', now()->year)
            ->whereMonth('occurred_at', now()->month)
            ->sum('amount');

        $currentMonthBalance = $currentMonthIncome - $currentMonthExpenses;

        // Insumos con stock bajo
        $lowStockSupplies = Supply::where('status', 'active')
            ->whereRaw('current_stock <= min_stock')
            ->orderBy('current_stock', 'asc')
            ->limit(5)
            ->get();

        // Cultivos activos con información de plot
        $activeCropsWithPlot = Crop::with('plot')
            ->where('status', 'active')
            ->limit(6)
            ->get();

        return view('admin.index', compact(
            'totalUsers',
            'activeUsers',
            'totalPlots',
            'activePlots',
            'totalCrops',
            'activeCrops',
            'pendingTasks',
            'completedTasks',
            'activeLoans',
            'availableSupplies',
            'totalIncome',
            'totalExpenses',
            'netProfit',
            'usersByRole',
            'plotsByStatus',
            'cropsByStatus',
            'recentTasks',
            'recentLedgerEntries',
            'farmSettings',
            'currentMonthIncome',
            'currentMonthExpenses',
            'currentMonthBalance',
            'lowStockSupplies',
            'activeCropsWithPlot'
        ));
    }

    /**
     * Get dashboard data in JSON format for real-time updates
     */
    public function data(): \Illuminate\Http\JsonResponse
    {
        $activeUsers = User::where('role', '!=', 'admin')->count();
        $pendingTasks = Task::where('status', 'pending')->count();
        $completedTasks = Task::where('status', 'completed')->count();
        $activeLoans = Loan::where('status', 'active')->count();
        $activeCrops = Crop::where('status', 'active')->count();

        // Financial balance
        $currentMonthIncome = LedgerEntry::where('type', 'income')
            ->whereYear('occurred_at', now()->year)
            ->whereMonth('occurred_at', now()->month)
            ->sum('amount');
        $currentMonthExpenses = LedgerEntry::where('type', 'expense')
            ->whereYear('occurred_at', now()->year)
            ->whereMonth('occurred_at', now()->month)
            ->sum('amount');

        return response()->json([
            'success' => true,
            'stats' => [
                'activeUsers' => $activeUsers,
                'pendingTasks' => $pendingTasks,
                'completedTasks' => $completedTasks,
                'activeLoans' => $activeLoans,
                'activeCrops' => $activeCrops,
                'currentMonthBalance' => $currentMonthIncome - $currentMonthExpenses,
            ]
        ]);
    }
}
