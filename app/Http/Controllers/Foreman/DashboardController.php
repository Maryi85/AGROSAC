<?php

namespace App\Http\Controllers\Foreman;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Loan;
use App\Models\Supply;
use App\Models\User;
use App\Models\Tool;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Estadísticas enfocadas en las responsabilidades del mayordomo
        $activeWorkers = User::where('role', 'worker')->whereNotNull('email_verified_at')->count();
        $pendingTasks = Task::where('status', 'pending')->count();
        $completedTasks = Task::where('status', 'completed')->count();
        $tasksToApprove = Task::where('status', 'completed')->count();
        $totalTasks = Task::count();
        
        // Estadísticas de herramientas
        // Usar el accessor available_qty que calcula desde tool_entries
        $tools = Tool::with('entries')->where('status', 'operational')->get();
        $availableTools = $tools->sum('available_qty');
        $totalTools = $tools->sum('total_entries');

        // Actividad reciente - solo información relevante para el mayordomo
        $recentTasks = Task::with(['plot', 'assignee'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Herramientas en uso (con cantidad disponible menor al total)
        // Usar el accessor available_qty que calcula desde tool_entries
        $toolsInUse = Tool::with('entries')
            ->get()
            ->filter(function($tool) {
                return $tool->available_qty < $tool->total_entries || $tool->status != 'operational';
            })
            ->sortBy('name')
            ->take(5)
            ->values();

        return view('foreman.dashboard', compact(
            'activeWorkers', 'pendingTasks', 'completedTasks', 'tasksToApprove', 'totalTasks',
            'availableTools', 'totalTools', 'recentTasks', 'toolsInUse'
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
}

