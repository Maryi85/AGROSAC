<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
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
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
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
        // Usar el accessor available_qty que calcula desde tool_entries
        $availableTools = Tool::with('entries')
            ->where('status', 'operational')
            ->get()
            ->filter(function($tool) {
                return $tool->available_qty > 0;
            })
            ->sortBy('name')
            ->values();

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
            'request_notes' => 'nullable|string|max:500',
        ]);

        $tool = Tool::findOrFail($request->tool_id);
        $quantity = $request->quantity;
        
        // Verificar disponibilidad
        if ($tool->available_qty < $quantity) {
            return redirect()->back()->with('error', "No hay suficientes herramientas disponibles. Solo hay {$tool->available_qty} disponibles.");
        }

        // Crear préstamo con estado 'pending' (pendiente de aprobación)
        // NO decrementar el stock hasta que se apruebe
        Loan::create([
            'tool_id' => $tool->id,
            'user_id' => $user->id,
            'quantity' => $quantity,
            'due_at' => $request->due_at ? \Carbon\Carbon::parse($request->due_at) : null,
            'request_notes' => $request->request_notes,
            'status' => 'pending',
        ]);

        $message = $quantity > 1 
            ? "Se solicitó el préstamo de {$quantity} herramientas de {$tool->name}. Esperando aprobación del administrador o mayordomo."
            : "Se solicitó el préstamo de 1 herramienta de {$tool->name}. Esperando aprobación del administrador o mayordomo.";
            
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
            'status' => 'returned_by_worker',
            'returned_at' => now(),
        ]);

        // NO incrementar el stock aquí, el admin/mayordomo lo hará al confirmar la devolución

        return redirect()->back()->with('status', 'Herramienta devuelta exitosamente.');
    }

    public function reports(): View
    {
        $user = Auth::user();
        
        // Obtener todas las tareas aprobadas y completadas del trabajador con información de cultivo y precios
        $tasks = Task::where('assigned_to', $user->id)
            ->whereIn('status', ['approved', 'completed'])
            ->with(['crop', 'plot'])
            ->orderBy('scheduled_for', 'desc')
            ->get();

        // Calcular el total_payment para cada tarea si no está guardado o es 0
        $tasks = $tasks->map(function ($task) {
            // Si total_payment es null o 0, calcularlo basándose en los precios
            $calculatedPayment = 0;
            
            if ($task->price_per_hour && $task->hours > 0) {
                $calculatedPayment = $task->hours * $task->price_per_hour;
            } elseif ($task->price_per_day && $task->hours > 0) {
                // Convertir horas a días (8 horas = 1 día)
                $days = $task->hours / 8;
                $calculatedPayment = $days * $task->price_per_day;
            } elseif ($task->price_per_kg && $task->kilos > 0) {
                $calculatedPayment = $task->kilos * $task->price_per_kg;
            }
            
            // Usar el total_payment guardado si existe y es mayor que 0, sino usar el calculado
            if ($task->total_payment && $task->total_payment > 0) {
                $task->calculated_payment = $task->total_payment;
            } else {
                $task->calculated_payment = $calculatedPayment;
                // Actualizar también el total_payment para que se guarde en la vista
                $task->total_payment = $calculatedPayment;
            }
            
            return $task;
        });

        // Calcular totales sumando todas las tareas
        $totalPayment = $tasks->sum(function ($task) {
            return $task->calculated_payment ?? ($task->total_payment ?? 0);
        });
        $totalHours = $tasks->sum(function ($task) {
            return $task->hours ?? 0;
        });
        $totalKilos = $tasks->sum(function ($task) {
            return $task->kilos ?? 0;
        });
        $totalTasks = $tasks->count();

        // Agrupar por cultivo
        $tasksByCrop = $tasks->groupBy('crop_id');

        // Calcular totales por cultivo
        $cropTotals = [];
        foreach ($tasksByCrop as $cropId => $cropTasks) {
            $crop = $cropTasks->first()->crop;
            $cropPayment = $cropTasks->sum(function ($task) {
                return $task->calculated_payment ?? ($task->total_payment ?? 0);
            });
            $cropHours = $cropTasks->sum(function ($task) {
                return $task->hours ?? 0;
            });
            $cropKilos = $cropTasks->sum(function ($task) {
                return $task->kilos ?? 0;
            });
            $cropTotals[$cropId] = [
                'crop' => $crop ? $crop->name : 'Sin cultivo',
                'tasks_count' => $cropTasks->count(),
                'total_payment' => $cropPayment,
                'total_hours' => $cropHours,
                'total_kilos' => $cropKilos,
            ];
        }

        return view('worker.reports', compact('user', 'tasks', 'totalPayment', 'totalHours', 'totalKilos', 'totalTasks', 'cropTotals'));
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

    public function downloadTasksPdf(Request $request)
    {
        $user = Auth::user();
        
        $tasks = Task::with(['plot', 'crop'])
            ->where('assigned_to', $user->id)
            ->orderBy('scheduled_for', 'asc')
            ->get();

        $pdf = Pdf::loadView('worker.tasks.pdf', compact('tasks'));
        return $pdf->download('mis-tareas-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadToolsPdf(Request $request)
    {
        $user = Auth::user();
        
        $myLoans = Loan::with(['tool'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('worker.tools.pdf', compact('myLoans'));
        return $pdf->download('mis-herramientas-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadReportsPdf()
    {
        $user = Auth::user();
        
        // Obtener todas las tareas aprobadas y completadas del trabajador con información de cultivo y precios
        $tasks = Task::where('assigned_to', $user->id)
            ->whereIn('status', ['approved', 'completed'])
            ->with(['crop', 'plot'])
            ->orderBy('scheduled_for', 'desc')
            ->get();

        // Calcular el total_payment para cada tarea si no está guardado o es 0
        $tasks = $tasks->map(function ($task) {
            // Si total_payment es null o 0, calcularlo basándose en los precios
            $calculatedPayment = 0;
            
            if ($task->price_per_hour && $task->hours > 0) {
                $calculatedPayment = $task->hours * $task->price_per_hour;
            } elseif ($task->price_per_day && $task->hours > 0) {
                // Convertir horas a días (8 horas = 1 día)
                $days = $task->hours / 8;
                $calculatedPayment = $days * $task->price_per_day;
            } elseif ($task->price_per_kg && $task->kilos > 0) {
                $calculatedPayment = $task->kilos * $task->price_per_kg;
            }
            
            // Usar el total_payment guardado si existe y es mayor que 0, sino usar el calculado
            if ($task->total_payment && $task->total_payment > 0) {
                $task->calculated_payment = $task->total_payment;
            } else {
                $task->calculated_payment = $calculatedPayment;
                $task->total_payment = $calculatedPayment;
            }
            
            return $task;
        });

        // Calcular totales sumando todas las tareas
        $totalPayment = $tasks->sum(function ($task) {
            return $task->calculated_payment ?? ($task->total_payment ?? 0);
        });
        $totalHours = $tasks->sum(function ($task) {
            return $task->hours ?? 0;
        });
        $totalKilos = $tasks->sum(function ($task) {
            return $task->kilos ?? 0;
        });
        $totalTasks = $tasks->count();

        // Agrupar por cultivo
        $tasksByCrop = $tasks->groupBy('crop_id');

        // Calcular totales por cultivo
        $cropTotals = [];
        foreach ($tasksByCrop as $cropId => $cropTasks) {
            $crop = $cropTasks->first()->crop;
            $cropPayment = $cropTasks->sum(function ($task) {
                return $task->calculated_payment ?? ($task->total_payment ?? 0);
            });
            $cropHours = $cropTasks->sum(function ($task) {
                return $task->hours ?? 0;
            });
            $cropKilos = $cropTasks->sum(function ($task) {
                return $task->kilos ?? 0;
            });
            $cropTotals[$cropId] = [
                'crop' => $crop ? $crop->name : 'Sin cultivo',
                'tasks_count' => $cropTasks->count(),
                'total_payment' => $cropPayment,
                'total_hours' => $cropHours,
                'total_kilos' => $cropKilos,
            ];
        }

        $pdf = Pdf::loadView('worker.reports.pdf', compact('user', 'tasks', 'totalPayment', 'totalHours', 'totalKilos', 'totalTasks', 'cropTotals'));
        return $pdf->download('reporte-trabajador-' . $user->name . '-' . now()->format('Y-m-d') . '.pdf');
    }
}





