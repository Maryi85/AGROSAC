<?php

namespace App\Http\Controllers\Foreman;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Loan;
use App\Models\Supply;
use App\Models\User;
use App\Models\Tool;
use App\Models\Crop;
use App\Models\FarmSetting;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        // A) KPIs Superiores
        // 1. Personal Presente (Trabajadores activos)
        $presentWorkers = User::where('role', 'worker')
            ->whereNotNull('email_verified_at')
            ->count();

        // 2. Tareas Pendientes Hoy
        $pendingTasksToday = Task::where('status', 'pending')
            ->whereDate('scheduled_for', Carbon::today())
            ->count();

        // Si no usan due_date, usar created_at o simplemente pendientes generales si se prefiere
        if ($pendingTasksToday == 0) {
            $pendingTasksToday = Task::where('status', 'pending')->count();
        }

        // 3. Herramientas en Uso (Prestadas o no disponibles)
        // Usar el accessor available_qty que calcula desde tool_entries
        $tools = Tool::with('entries')->where('status', 'operational')->get();
        $totalTools = $tools->sum('total_entries');
        $availableTools = $tools->sum('available_qty');
        $toolsInUseCount = $totalTools - $availableTools;

        // 4. Alertas de Cultivo (Cultivos activos por ahora)
        $cropAlerts = Crop::where('status', 'scanner') // Asumiendo 'active' o similar
            ->count();

        // Si no hay cultivos en estado 'scanner', mostrar todos los activos
        if ($cropAlerts == 0) {
            $cropAlerts = Crop::where('status', 'active')->count();
        }

        // B) Gráfico Principal: Rendimiento Semanal (Completed vs Assigned last 7 days)
        $weeklyPerformance = [
            'dates' => [],
            'assigned' => [],
            'completed' => []
        ];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateString = $date->format('Y-m-d');
            $displayDate = $date->format('d/m');

            $weeklyPerformance['dates'][] = $displayDate;

            // Asignadas = tareas programadas para ese día (scheduled_for)
            $weeklyPerformance['assigned'][] = Task::whereDate('scheduled_for', $dateString)->count();

            // Completadas = tareas marcadas como completadas ese día (por updated_at)
            $weeklyPerformance['completed'][] = Task::where('status', 'completed')
                ->whereDate('updated_at', $dateString)
                ->count();
        }

        // C) Listas Rápidas
        // 1. Próximas Tareas
        $upcomingTasks = Task::with(['plot', 'assignee'])
            ->where('status', 'pending')
            ->orderBy('scheduled_for', 'asc')
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get();

        // 2. Insumos Críticos
        $criticalSupplies = Supply::whereRaw('current_stock <= min_stock')
            ->orderBy('current_stock', 'asc')
            ->limit(5)
            ->get();

        // D) Mapa
        $farmSettings = FarmSetting::getFarmSettings();

        // Datos legacy que podrían usarse en la vista actual si no se migra todo de golpe, 
        // pero para la nueva vista usaremos las variables de arriba.
        // Mantenemos algunas por compatibilidad si se mezclan vistas.
        $activeWorkers = $presentWorkers;
        $pendingTasks = Task::where('status', 'pending')->count();
        $completedTasks = Task::where('status', 'completed')->count();
        $tasksToApprove = Task::where('status', 'completed')->count(); // Ojo: status completed suele ser para aprobar? O hay un status 'review'? Asumo completed es final.
        // Si hay flujo de aprobación, ajustar query.

        return view('foreman.dashboard', compact(
            'presentWorkers', 'pendingTasksToday', 'toolsInUseCount', 'cropAlerts',
            'weeklyPerformance', 'upcomingTasks', 'criticalSupplies', 'farmSettings',
            // Legacy for fallback
            'activeWorkers', 'pendingTasks', 'completedTasks', 'tasksToApprove', 'availableTools', 'totalTools'
        ));
    }

    public function tasks(): View
    {
        $tasks = Task::with(['plot', 'assignee'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('foreman.tasks', compact('tasks'));
    }

    public function inventory(): View
    {
        $supplies = Supply::orderBy('name')->get();
        $tools = \App\Models\Tool::orderBy('name')->get();

        return view('foreman.inventory', compact('supplies', 'tools'));
    }

    public function loans(): View
    {
        $loans = Loan::with(['tool', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('foreman.loans', compact('loans'));
    }

    /**
     * Get dashboard data in JSON format for real-time updates
     */
    public function data(): \Illuminate\Http\JsonResponse
    {
        $presentWorkers = User::where('role', 'worker')->whereNotNull('email_verified_at')->count();
        $pendingTasksCount = Task::where('status', 'pending')->count();
        $completedTasksCount = Task::where('status', 'completed')->count();

        $tools = Tool::with('entries')->where('status', 'operational')->get();
        $totalTools = $tools->sum('total_entries');
        $availableTools = $tools->sum('available_qty');
        $toolsInUseCount = $totalTools - $availableTools;

        return response()->json([
            'success' => true,
            'stats' => [
                'presentWorkers' => $presentWorkers,
                'pendingTasks' => $pendingTasksCount,
                'completedTasks' => $completedTasksCount,
                'toolsInUse' => $toolsInUseCount,
            ]
        ]);
    }
}
