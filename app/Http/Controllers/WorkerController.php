<?php

namespace App\Http\Controllers;

use App\Models\Plot;
use App\Models\Crop;
use App\Models\Task;
use App\Models\Loan;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class WorkerController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        
        // Estadísticas del trabajador
        $pendingTasks = Task::where('assigned_to', $user->id)
            ->where('status', 'pending')
            ->count();
        
        $completedTasks = Task::where('assigned_to', $user->id)
            ->where('status', 'completed')
            ->count();
        
        $activeLoans = Loan::where('user_id', $user->id)
            ->where('status', 'out')
            ->count();
        
        $totalTasks = Task::where('assigned_to', $user->id)->count();

        // Tareas pendientes del trabajador
        $myPendingTasks = Task::with(['plot', 'crop'])
            ->where('assigned_to', $user->id)
            ->where('status', 'pending')
            ->orderBy('scheduled_for', 'asc')
            ->limit(5)
            ->get();

        // Tareas completadas recientemente
        $recentCompletedTasks = Task::with(['plot', 'crop'])
            ->where('assigned_to', $user->id)
            ->where('status', 'completed')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // Herramientas prestadas al trabajador
        $myLoans = Loan::with(['tool'])
            ->where('user_id', $user->id)
            ->where('status', 'out')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('worker.dashboard', compact(
            'pendingTasks', 'completedTasks', 'activeLoans', 'totalTasks',
            'myPendingTasks', 'recentCompletedTasks', 'myLoans'
        ));
    }

    public function tasks(): View
    {
        $user = Auth::user();
        
        $tasks = Task::with(['plot', 'crop'])
            ->where('assigned_to', $user->id)
            ->orderBy('scheduled_for', 'asc')
            ->paginate(15);

        return view('worker.tasks', compact('tasks'));
    }

    public function completeTask(Request $request, Task $task): RedirectResponse
    {
        $user = Auth::user();
        
        // Verificar que la tarea pertenece al trabajador
        if ($task->assigned_to !== $user->id) {
            return redirect()->back()->with('error', 'No tienes permisos para completar esta tarea.');
        }

        $request->validate([
            'completion_notes' => 'nullable|string|max:1000',
            'hours_worked' => 'nullable|numeric|min:0',
            'quantity_harvested' => 'nullable|numeric|min:0',
        ]);

        $task->update([
            'status' => 'completed',
            'hours' => $request->hours_worked ?? $task->hours,
            'kilos' => $request->quantity_harvested ?? $task->kilos,
        ]);

        return redirect()->back()->with('status', 'Tarea completada exitosamente.');
    }

    public function tools(): View
    {
        $user = Auth::user();
        
        // Herramientas disponibles
        $availableTools = Tool::where('status', 'operational')
            ->where('available_qty', '>', 0)
            ->orderBy('name')
            ->get();

        // Herramientas prestadas al trabajador
        $myLoans = Loan::with(['tool'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('worker.tools', compact('availableTools', 'myLoans'));
    }

    public function requestTool(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        $request->validate([
            'tool_id' => 'required|exists:tools,id',
            'quantity' => 'required|integer|min:1',
            'due_at' => 'nullable|date|after:today',
        ]);

        $tool = Tool::findOrFail($request->tool_id);
        $quantity = $request->quantity;
        
        // Verificar disponibilidad
        if ($tool->available_qty < $quantity) {
            return redirect()->back()->with('error', "No hay suficientes herramientas disponibles. Solo hay {$tool->available_qty} disponibles.");
        }

        // Crear préstamos individuales para cada herramienta
        for ($i = 0; $i < $quantity; $i++) {
            Loan::create([
                'tool_id' => $tool->id,
                'user_id' => $user->id,
                'due_at' => $request->due_at,
                'status' => 'out',
            ]);
        }

        // Actualizar cantidad disponible de la herramienta
        $tool->decrement('available_qty', $quantity);

        $message = $quantity > 1 
            ? "Se solicitaron {$quantity} herramientas de {$tool->name} exitosamente."
            : "Se solicitó 1 herramienta de {$tool->name} exitosamente.";
            
        return redirect()->back()->with('status', $message);
    }

    public function returnTool(Loan $loan): RedirectResponse
    {
        $user = Auth::user();
        
        // Verificar que el préstamo pertenece al trabajador
        if ($loan->user_id !== $user->id) {
            return redirect()->back()->with('error', 'No tienes permisos para devolver esta herramienta.');
        }

        if ($loan->status !== 'out') {
            return redirect()->back()->with('error', 'Esta herramienta no está activa para devolver.');
        }

        $loan->update([
            'status' => 'returned',
            'returned_at' => now(),
        ]);

        // Actualizar cantidad disponible de la herramienta
        $tool = $loan->tool;
        $tool->increment('available_qty', 1);

        return redirect()->back()->with('status', 'Herramienta devuelta exitosamente.');
    }

    public function reports(): View
    {
        $user = Auth::user();
        
        // Tareas completadas para reportes
        $completedTasks = Task::with(['plot', 'crop'])
            ->where('assigned_to', $user->id)
            ->where('status', 'completed')
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        return view('worker.reports', compact('completedTasks'));
    }

    public function generateReport(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'report_notes' => 'nullable|string|max:1000',
        ]);

        // Obtener tareas completadas en el rango de fechas
        $tasks = Task::with(['plot', 'crop'])
            ->where('assigned_to', $user->id)
            ->where('status', 'completed')
            ->whereBetween('updated_at', [$request->start_date, $request->end_date])
            ->get();

        // Aquí podrías generar un PDF o crear un registro de reporte
        // Por ahora, solo mostramos un mensaje de éxito
        
        return redirect()->back()->with('status', 
            "Reporte generado exitosamente para el período {$request->start_date} - {$request->end_date}. " .
            "Se encontraron {$tasks->count()} tareas completadas."
        );
    }
}
