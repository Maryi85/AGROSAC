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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // Estadísticas del trabajador
        $pendingTasks = Task::where('assigned_to', $user->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();

        $completedTasks = Task::where('assigned_to', $user->id)
            ->whereIn('status', ['completed', 'approved'])
            ->count();

        $activeLoans = Loan::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved', 'out'])
            ->count();

        $totalTasks = Task::where('assigned_to', $user->id)->count();

        $myPendingTasks = Task::with(['plot', 'crop'])
            ->where('assigned_to', $user->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('scheduled_for', 'asc')
            ->limit(5)
            ->get();

        // Tareas con estado cambiado recientemente (completadas, aprobadas o rechazadas)
        $recentEvaluations = Task::with(['plot', 'crop'])
            ->where('assigned_to', $user->id)
            ->whereIn('status', ['completed', 'approved', 'rejected'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // Herramientas prestadas al trabajador
        $myLoans = Loan::with(['tool'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved', 'out'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Datos para el gráfico de rendimiento semanal
        $weeklyPerformance = [
            'dates' => [],
            'assigned' => [],
            'completed' => []
        ];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateString = $date->format('Y-m-d');

            $weeklyPerformance['dates'][] = $date->format('d/m');

            // Tareas asignadas en esta fecha (usando scheduled_for)
            $weeklyPerformance['assigned'][] = Task::where('assigned_to', $user->id)
                ->whereDate('scheduled_for', $dateString)
                ->count();

            // Tareas completadas en esta fecha (completadas o ya aprobadas)
            $weeklyPerformance['completed'][] = Task::where('assigned_to', $user->id)
                ->whereIn('status', ['completed', 'approved'])
                ->whereDate('updated_at', $dateString)
                ->count();
        }

        return view('worker.dashboard', compact(
            'pendingTasks', 'completedTasks', 'activeLoans', 'totalTasks',
            'myPendingTasks', 'recentEvaluations', 'myLoans', 'weeklyPerformance'
        ));
    }

    /**
     * Obtener datos del dashboard en formato JSON para actualizaciones en tiempo real
     */
    public function data(): JsonResponse
    {
        $user = Auth::user();

        // Estadísticas del trabajador
        $pendingTasks = Task::where('assigned_to', $user->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();

        $completedTasks = Task::where('assigned_to', $user->id)
            ->whereIn('status', ['completed', 'approved'])
            ->count();

        $activeLoans = Loan::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved', 'out'])
            ->count();

        $totalTasks = Task::where('assigned_to', $user->id)->count();

        $myPendingTasks = Task::with(['plot', 'crop'])
            ->where('assigned_to', $user->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('scheduled_for', 'asc')
            ->limit(5)
            ->get()
            ->map(function ($task) {
            return [
            'id' => $task->id,
            'description' => $task->description,
            'priority' => $task->priority,
            'plot_name' => $task->plot->name ?? 'Lote General',
            'crop_name' => $task->crop->name ?? 'Sin cultivo',
            'scheduled_for' => $task->scheduled_for ? $task->scheduled_for->format('d/m') : 'S/F',
            ];
        });

        // Tareas con estado cambiado recientemente (completadas, aprobadas o rechazadas)
        $recentEvaluations = Task::with(['plot', 'crop'])
            ->where('assigned_to', $user->id)
            ->whereIn('status', ['completed', 'approved', 'rejected'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($task) {
            $statusMap = [
                'completed' => ['label' => 'Completada', 'color' => 'emerald'],
                'approved' => ['label' => 'Aprobada', 'color' => 'blue'],
                'rejected' => ['label' => 'Rechazada', 'color' => 'red'],
            ];
            $s = $statusMap[$task->status] ?? ['label' => $task->status, 'color' => 'gray'];

            return [
            'id' => $task->id,
            'description' => $task->description,
            'status' => $task->status,
            'status_label' => $s['label'],
            'status_color' => $s['color'],
            'updated_at' => $task->updated_at ? $task->updated_at->format('d/m H:i') : '—',
            ];
        });

        // Herramientas prestadas al trabajador
        $myLoans = Loan::with(['tool'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved', 'out'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($loan) {
            return [
            'id' => $loan->id,
            'tool_name' => $loan->tool->name,
            'quantity' => $loan->quantity,
            'status' => $loan->status,
            'created_at' => $loan->created_at->format('d/m H:i'),
            ];
        });

        // Datos para el gráfico de rendimiento semanal
        $weeklyPerformance = [
            'dates' => [],
            'assigned' => [],
            'completed' => []
        ];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateString = $date->format('Y-m-d');

            $weeklyPerformance['dates'][] = $date->format('d/m');
            $weeklyPerformance['assigned'][] = Task::where('assigned_to', $user->id)
                ->whereDate('scheduled_for', $dateString)
                ->count();
            $weeklyPerformance['completed'][] = Task::where('assigned_to', $user->id)
                ->whereIn('status', ['completed', 'approved'])
                ->whereDate('updated_at', $dateString)
                ->count();
        }

        return response()->json([
            'success' => true,
            'stats' => [
                'pendingTasks' => $pendingTasks,
                'completedTasks' => $completedTasks,
                'activeLoans' => $activeLoans,
                'totalTasks' => $totalTasks,
                'compliance' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0,
            ],
            'myPendingTasks' => $myPendingTasks,
            'recentEvaluations' => $recentEvaluations,
            'myLoans' => $myLoans,
            'weeklyPerformance' => $weeklyPerformance
        ]);
    }

    public function tasks(Request $request)
    {
        $user = Auth::user();

        $tasks = Task::with(['plot', 'crop', 'creator'])
            ->where('assigned_to', $user->id)
            ->orderBy('scheduled_for', 'asc')
            ->paginate(15);

        if ($request->wantsJson()) {
            $statusMap = [
                'pending' => ['label' => 'Pendiente', 'class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'clock'],
                'completed' => ['label' => 'Completada', 'class' => 'bg-green-100 text-green-800', 'icon' => 'check-circle-2'],
                'approved' => ['label' => 'Aprobada', 'class' => 'bg-gray-100 text-gray-700', 'icon' => 'circle'],
                'rejected' => ['label' => 'Rechazada', 'class' => 'bg-red-100 text-red-700', 'icon' => 'x-circle'],
                'invalid' => ['label' => 'Inválida', 'class' => 'bg-red-100 text-red-700', 'icon' => 'x-circle'],
                'in_progress' => ['label' => 'En Progreso', 'class' => 'bg-blue-100 text-blue-700', 'icon' => 'loader'],
                'cancelled' => ['label' => 'Cancelada', 'class' => 'bg-gray-100 text-gray-600', 'icon' => 'ban'],
            ];

            $tasks->getCollection()->transform(function ($task) use ($statusMap) {
                $s = $statusMap[$task->status] ?? ['label' => ucfirst($task->status), 'class' => 'bg-gray-100 text-gray-700', 'icon' => 'circle'];
                return [
                'id' => $task->id,
                'description' => $task->description,
                'status' => $task->status,
                'status_label' => $s['label'],
                'status_class' => $s['class'],
                'status_icon' => $s['icon'],
                'plot_name' => $task->plot->name ?? 'Sin lote',
                'crop_name' => $task->crop->name ?? 'Sin cultivo',
                'type' => $task->type ?? '—',
                'scheduled_for' => $task->scheduled_for ? $task->scheduled_for->format('d/m/Y') : null,
                'is_overdue' => $task->scheduled_for < now() && $task->status === 'pending',
                'price_per_hour' => $task->price_per_hour ?? 0,
                'price_per_day' => $task->price_per_day ?? 0,
                'price_per_kg' => $task->price_per_kg ?? 0,
                'hours' => $task->hours ?? 0,
                'kilos' => $task->kilos ?? 0,
                'total_payment' => $task->total_payment ?? 0,
                'estimated_hours' => $task->estimated_hours,
                'estimated_total_payment' => $task->estimated_total_payment,
                'creator_name' => $task->creator->name ?? 'Administración',
                'notes' => $task->notes ?? '',
                ];
            });

            return response()->json($tasks);
        }

        return view('worker.tasks', compact('tasks'));
    }

    public function completeTask(Request $request, Task $task): RedirectResponse|JsonResponse
    {
        $user = Auth::user();

        // Verificar que la tarea pertenece al trabajador
        if ($task->assigned_to !== $user->id) {
            $message = 'No tienes permisos para completar esta tarea.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 403);
            }
            return redirect()->back()->with('error', $message);
        }

        // Determinar el tipo de pago basado en los precios configurados
        $isHourlyTask = $task->price_per_hour > 0 || $task->price_per_day > 0;
        $isQuantityTask = $task->price_per_kg > 0;

        // Validación condicional estricta
        $rules = [
            'completion_notes' => 'nullable|string|max:1000',
        ];

        if ($isHourlyTask) {
            // Para tareas por horas: hours_worked es obligatorio y debe ser > 0
            $rules['hours_worked'] = [
                'required',
                'numeric',
                'min:0.1',
                'max:24',
            ];
            $rules['quantity_harvested'] = 'nullable|numeric|min:0';
        }
        elseif ($isQuantityTask) {
            // Para tareas por cantidad: quantity_harvested es obligatorio y debe ser > 0
            $rules['quantity_harvested'] = [
                'required',
                'numeric',
                'min:0.1',
                'max:100000',
            ];
            $rules['hours_worked'] = 'nullable|numeric|min:0|max:24';
        }
        else {
            // Si no tiene precio configurado, permitir ambos pero al menos uno debe tener valor
            $rules['hours_worked'] = 'nullable|numeric|min:0|max:24';
            $rules['quantity_harvested'] = 'nullable|numeric|min:0|max:100000';
        }

        try {
            $validated = $request->validate($rules, [
                'hours_worked.required' => 'Debes registrar las horas trabajadas para finalizar la tarea.',
                'hours_worked.min' => 'Las horas trabajadas deben ser mayor a 0 para finalizar la tarea.',
                'hours_worked.max' => 'Las horas trabajadas no pueden exceder 24 horas.',
                'quantity_harvested.required' => 'Debes registrar la cantidad recolectada para finalizar la tarea.',
                'quantity_harvested.min' => 'La cantidad recolectada debe ser mayor a 0 para finalizar la tarea.',
                'quantity_harvested.max' => 'La cantidad recolectada excede el límite permitido.',
            ]);
        }
        catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        // Actualizar la tarea usando el servicio para asegurar el consumo de insumos
        app(\App\Services\TaskService::class)->complete($task, $validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tarea completada exitosamente.',
                'task' => $task->fresh()
            ]);
        }

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
            ->filter(function ($tool) {
            return $tool->available_qty > 0;
        })
            ->sortBy('name')
            ->values();

        // Herramientas prestadas al trabajador
        $myLoans = Loan::with(['tool'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Tareas activas del trabajador para asociar a la solicitud de herramientas
        $myTasks = Task::with(['plot'])
            ->where('assigned_to', $user->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('scheduled_for', 'asc')
            ->get();

        if (request()->wantsJson()) {
            $statusConfig = [
                'pending' => ['class' => 'bg-yellow-100 text-yellow-800 border border-yellow-200', 'icon' => 'clock', 'text' => 'Pendiente'],
                'approved' => ['class' => 'bg-blue-100 text-blue-800 border border-blue-200', 'icon' => 'check', 'text' => 'Aprobado'],
                'rejected' => ['class' => 'bg-red-100 text-red-800 border border-red-200', 'icon' => 'x-circle', 'text' => 'Rechazado'],
                'out' => ['class' => 'bg-blue-100 text-blue-800 border border-blue-200', 'icon' => 'arrow-right-circle', 'text' => 'Prestada'],
                'returned_by_worker' => ['class' => 'bg-green-100 text-green-800 border border-green-200', 'icon' => 'check-circle', 'text' => 'Devuelta (Pendiente)'],
                'returned' => ['class' => 'bg-green-100 text-green-800 border border-green-200', 'icon' => 'check-circle', 'text' => 'Devuelta'],
            ];

            return response()->json([
                'availableTools' => $availableTools->map(function ($tool) {
                return [
                        'id' => $tool->id,
                        'name' => $tool->name,
                        'category' => $tool->category,
                        'available_qty' => $tool->available_qty,
                        'total_entries' => $tool->total_entries,
                        'photo' => $tool->photo ? asset('storage/' . $tool->photo) : null,
                        'status' => $tool->status,
                    ];
            }),
                'myLoans' => $myLoans->map(function ($loan) use ($statusConfig) {
                $cfg = $statusConfig[$loan->status] ?? ['class' => 'bg-gray-100 text-gray-700', 'icon' => 'circle', 'text' => ucfirst($loan->status)];
                return [
                        'id' => $loan->id,
                        'tool' => [
                            'name' => $loan->tool->name,
                            'photo' => $loan->tool->photo ? asset('storage/' . $loan->tool->photo) : null,
                        ],
                        'quantity' => $loan->quantity,
                        'status' => $loan->status,
                        'status_text' => $cfg['text'],
                        'status_class' => $cfg['class'],
                        'status_icon' => $cfg['icon'],
                        'created_at' => $loan->created_at->format('d/m/Y H:i'),
                        'out_at' => $loan->out_at ? $loan->out_at->format('d/m/Y H:i') : null,
                        'due_at' => $loan->due_at ? $loan->due_at->format('d/m/Y') : null,
                        'returned_at' => $loan->returned_at ? $loan->returned_at->format('d/m/Y H:i') : null,
                        'request_notes' => $loan->request_notes,
                        'task_name' => $loan->task ? $loan->task->description . ' (' . ($loan->task->plot->name ?? 'Lote General') . ')' : '',
                        'return_url' => route('worker.tools.return', $loan),
                    ];
            }),
                'pagination' => $myLoans->links()->toHtml(),
            ]);
        }

        return view('worker.tools', compact('availableTools', 'myLoans', 'myTasks'));
    }

    public function requestTool(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'tool_id' => 'required|exists:tools,id',
            'task_id' => 'nullable|exists:tasks,id',
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
            'task_id' => $request->task_id,
            'quantity' => $quantity,
            'due_at' => $request->due_at ?\Carbon\Carbon::parse($request->due_at) : null,
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
            }
            elseif ($task->price_per_day && $task->hours > 0) {
                // Convertir horas a días (8 horas = 1 día)
                $days = $task->hours / 8;
                $calculatedPayment = $days * $task->price_per_day;
            }
            elseif ($task->price_per_kg && $task->kilos > 0) {
                $calculatedPayment = $task->kilos * $task->price_per_kg;
            }

            // Usar el total_payment guardado si existe y es mayor que 0, sino usar el calculado
            if ($task->total_payment && $task->total_payment > 0) {
                $task->calculated_payment = $task->total_payment;
            }
            else {
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

        // Obtener tareas finalizadas (completadas o aprobadas) en el rango de fechas
        $tasks = Task::with(['plot', 'crop'])
            ->where('assigned_to', $user->id)
            ->whereIn('status', ['completed', 'approved'])
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
            }
            elseif ($task->price_per_day && $task->hours > 0) {
                // Convertir horas a días (8 horas = 1 día)
                $days = $task->hours / 8;
                $calculatedPayment = $days * $task->price_per_day;
            }
            elseif ($task->price_per_kg && $task->kilos > 0) {
                $calculatedPayment = $task->kilos * $task->price_per_kg;
            }

            // Usar el total_payment guardado si existe y es mayor que 0, sino usar el calculado
            if ($task->total_payment && $task->total_payment > 0) {
                $task->calculated_payment = $task->total_payment;
            }
            else {
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
