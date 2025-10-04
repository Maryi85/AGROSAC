<?php

namespace App\Http\Controllers;

use App\Models\Plot;
use App\Models\Crop;
use App\Models\Task;
use App\Models\Loan;
use App\Models\Supply;
use App\Models\SupplyConsumption;
use App\Models\LedgerEntry;
use App\Models\User;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ForemanController extends Controller
{
    public function index(): View
    {
        // Estadísticas enfocadas en las responsabilidades del mayordomo
        $activeWorkers = User::where('role', 'worker')->whereNotNull('email_verified_at')->count();
        $pendingTasks = Task::where('status', 'pending')->count();
        $completedTasks = Task::where('status', 'completed')->count();
        $tasksToApprove = Task::where('status', 'completed')->count();
        
        // Estadísticas de herramientas
        $availableTools = Tool::where('status', 'operational')->sum('available_qty');
        $totalTools = Tool::sum('total_qty');

        // Actividad reciente - solo información relevante para el mayordomo
        $recentTasks = Task::with(['plot', 'assignee'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Herramientas en uso (con cantidad disponible menor al total)
        $toolsInUse = Tool::where('available_qty', '<', \DB::raw('total_qty'))
            ->orWhere('status', '!=', 'operational')
            ->orderBy('name')
            ->limit(5)
            ->get();

        return view('foreman.dashboard', compact(
            'activeWorkers', 'pendingTasks', 'completedTasks', 'tasksToApprove',
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
        $loans = Loan::with(['tool', 'borrower'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('foreman.loans', compact('loans'));
    }
}
