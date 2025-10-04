<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Plot;
use App\Models\Crop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class ForemanTaskController extends Controller
{
    public function index(Request $request): View
    {
        $query = Task::with(['plot', 'crop', 'assignee']);

        // Filtros
        $search = $request->get('search');
        $status = $request->get('status');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', '%' . $search . '%')
                  ->orWhereHas('assignee', function ($subQ) use ($search) {
                      $subQ->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $tasks = $query->orderBy('created_at', 'desc')->paginate(15);

        // Estados disponibles
        $statuses = ['pending', 'in_progress', 'completed', 'approved', 'rejected', 'invalid'];

        return view('foreman.tasks.index', compact('tasks', 'statuses', 'search', 'status'));
    }

    public function create(): View
    {
        $workers = User::where('role', 'worker')->whereNotNull('email_verified_at')->orderBy('name')->get();
        $plots = Plot::where('status', 'active')->orderBy('name')->get();
        $crops = Crop::where('status', 'active')->orderBy('name')->get();
        $taskTypes = ['daily', 'harvest', 'maintenance', 'planting', 'irrigation', 'fertilization'];

        return view('foreman.tasks.create', compact('workers', 'plots', 'crops', 'taskTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'type' => 'required|string|in:daily,harvest,maintenance,planting,irrigation,fertilization',
            'description' => 'required|string|max:500',
            'plot_id' => 'nullable|exists:plots,id',
            'crop_id' => 'nullable|exists:crops,id',
            'assigned_to' => 'required|exists:users,id',
            'scheduled_for' => 'required|date|after_or_equal:today',
            'hours' => 'nullable|numeric|min:0|max:24',
            'kilos' => 'nullable|numeric|min:0',
        ]);

        Task::create($request->all());

        return redirect()->route('foreman.tasks.index')
            ->with('status', 'Tarea creada correctamente');
    }

    public function show(Task $task): View|JsonResponse
    {
        $task->load(['plot', 'crop', 'assignee', 'approver']);
        
        // Si es una petición AJAX, devolver JSON
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'task' => [
                    'id' => $task->id,
                    'type' => $task->type,
                    'description' => $task->description,
                    'status' => $task->status,
                    'scheduled_for' => $task->scheduled_for ? $task->scheduled_for->format('d/m/Y') : 'Sin fecha programada',
                    'hours' => $task->hours > 0 ? $task->hours . ' horas' : 'No registradas',
                    'kilos' => $task->kilos > 0 ? $task->kilos . ' kg' : 'No registrados',
                    'created_at' => $task->created_at->format('d/m/Y H:i'),
                    'assignee_name' => $task->assignee->name ?? 'Sin asignar',
                    'plot_name' => $task->plot->name ?? 'Sin lote asignado',
                    'crop_name' => $task->crop->name ?? 'Sin cultivo asignado',
                    'approver_name' => $task->approver->name ?? null,
                    'approved_at' => $task->approved_at ? $task->approved_at->format('d/m/Y H:i') : null,
                ]
            ]);
        }
        
        return view('foreman.tasks.show', compact('task'));
    }

    public function edit(Task $task): View|JsonResponse
    {
        $workers = User::where('role', 'worker')->whereNotNull('email_verified_at')->orderBy('name')->get();
        $plots = Plot::where('status', 'active')->orderBy('name')->get();
        $crops = Crop::where('status', 'active')->orderBy('name')->get();
        $taskTypes = ['daily', 'harvest', 'maintenance', 'planting', 'irrigation', 'fertilization'];

        // Si es una petición AJAX, devolver JSON
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'task' => [
                    'id' => $task->id,
                    'type' => $task->type,
                    'description' => $task->description,
                    'status' => $task->status,
                    'scheduled_for' => $task->scheduled_for ? $task->scheduled_for->format('Y-m-d') : '',
                    'hours' => $task->hours,
                    'kilos' => $task->kilos,
                    'assigned_to' => $task->assigned_to,
                    'plot_id' => $task->plot_id,
                    'crop_id' => $task->crop_id,
                ],
                'workers' => $workers->map(function ($worker) {
                    return [
                        'id' => $worker->id,
                        'name' => $worker->name,
                    ];
                }),
                'plots' => $plots->map(function ($plot) {
                    return [
                        'id' => $plot->id,
                        'name' => $plot->name,
                    ];
                }),
                'crops' => $crops->map(function ($crop) {
                    return [
                        'id' => $crop->id,
                        'name' => $crop->name,
                    ];
                }),
                'taskTypes' => $taskTypes
            ]);
        }

        return view('foreman.tasks.edit', compact('task', 'workers', 'plots', 'crops', 'taskTypes'));
    }

    public function update(Request $request, Task $task): RedirectResponse|JsonResponse
    {
        try {
            $validated = $request->validate([
                'type' => 'required|string|in:daily,harvest,maintenance,planting,irrigation,fertilization',
                'description' => 'required|string|max:500',
                'plot_id' => 'nullable|exists:plots,id',
                'crop_id' => 'nullable|exists:crops,id',
                'assigned_to' => 'required|exists:users,id',
                'scheduled_for' => 'required|date',
                'hours' => 'nullable|numeric|min:0|max:24',
                'kilos' => 'nullable|numeric|min:0',
                'status' => 'required|string|in:pending,in_progress,completed,approved,rejected,invalid',
            ]);

            $task->update($validated);
            
            // Recargar las relaciones para la respuesta JSON
            $task->load(['assignee', 'plot', 'crop']);

            // Si es una petición AJAX, devolver JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tarea actualizada correctamente',
                    'task' => [
                        'id' => $task->id,
                        'type' => $task->type,
                        'description' => $task->description,
                        'status' => $task->status,
                        'scheduled_for' => $task->scheduled_for ? $task->scheduled_for->format('d/m/Y') : 'Sin fecha programada',
                        'hours' => $task->hours > 0 ? $task->hours . ' horas' : 'No registradas',
                        'kilos' => $task->kilos > 0 ? $task->kilos . ' kg' : 'No registrados',
                        'assignee_name' => $task->assignee->name ?? 'Sin asignar',
                        'plot_name' => $task->plot->name ?? 'Sin lote asignado',
                        'crop_name' => $task->crop->name ?? 'Sin cultivo asignado',
                    ]
                ]);
            }

            return redirect()->route('foreman.tasks.index')
                ->with('status', 'Tarea actualizada correctamente');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Si es una petición AJAX, devolver errores de validación
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            // Si es una petición AJAX, devolver error JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar la tarea: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('foreman.tasks.index')
                ->with('error', 'Error al actualizar la tarea');
        }
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()->route('foreman.tasks.index')
            ->with('status', 'Tarea eliminada correctamente');
    }

    public function approve(Request $request, Task $task): RedirectResponse|JsonResponse
    {
        if ($task->status !== 'completed') {
            $errorMessage = 'Solo se pueden aprobar tareas que estén marcadas como completadas.';
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }
            
            return redirect()->route('foreman.tasks.index')
                ->with('error', $errorMessage);
        }

        $task->update([
            'status' => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        $message = 'Tarea aprobada correctamente';

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'task' => [
                    'id' => $task->id,
                    'status' => $task->status,
                    'approved_at' => $task->approved_at->format('d/m/Y H:i'),
                    'hours' => $task->hours > 0 ? $task->hours . ' horas' : 'No registradas',
                    'kilos' => $task->kilos > 0 ? $task->kilos . ' kg' : 'No registrados',
                ]
            ]);
        }

        return redirect()->route('foreman.tasks.index')
            ->with('status', $message);
    }

    public function invalidate(Request $request, Task $task): RedirectResponse|JsonResponse
    {
        if ($task->status === 'approved') {
            $errorMessage = 'No se puede invalidar una tarea que ya ha sido aprobada.';
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }
            
            return redirect()->route('foreman.tasks.index')
                ->with('error', $errorMessage);
        }

        $task->update([
            'status' => 'invalid',
        ]);

        $message = 'Tarea marcada como inválida';

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'task' => [
                    'id' => $task->id,
                    'status' => $task->status,
                    'hours' => $task->hours > 0 ? $task->hours . ' horas' : 'No registradas',
                    'kilos' => $task->kilos > 0 ? $task->kilos . ' kg' : 'No registrados',
                ]
            ]);
        }

        return redirect()->route('foreman.tasks.index')
            ->with('status', $message);
    }

    public function complete(Request $request, Task $task): RedirectResponse|JsonResponse
    {
        if ($task->status !== 'in_progress' && $task->status !== 'pending') {
            $errorMessage = 'Solo se pueden completar tareas que estén pendientes o en progreso.';
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }
            
            return redirect()->route('foreman.tasks.index')
                ->with('error', $errorMessage);
        }

        $request->validate([
            'hours' => 'required|numeric|min:0|max:24',
            'kilos' => 'nullable|numeric|min:0',
        ]);

        $task->update([
            'status' => 'completed',
            'hours' => $request->hours,
            'kilos' => $request->kilos ?? 0,
        ]);

        $message = 'Tarea marcada como completada';

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'task' => [
                    'id' => $task->id,
                    'status' => $task->status,
                    'hours' => $task->hours > 0 ? $task->hours . ' horas' : 'No registradas',
                    'kilos' => $task->kilos > 0 ? $task->kilos . ' kg' : 'No registrados',
                ]
            ]);
        }

        return redirect()->route('foreman.tasks.index')
            ->with('status', $message);
    }

    /**
     * Obtener trabajadores disponibles en tiempo real
     */
    public function getWorkers(): JsonResponse
    {
        $workers = User::where('role', 'worker')
            ->whereNotNull('email_verified_at')
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->map(function ($worker) {
                return [
                    'id' => $worker->id,
                    'name' => $worker->name,
                    'email' => $worker->email,
                ];
            });

        return response()->json([
            'success' => true,
            'workers' => $workers
        ]);
    }

    public function getCrops(): JsonResponse
    {
        $crops = Crop::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'variety'])
            ->map(function ($crop) {
                return [
                    'id' => $crop->id,
                    'name' => $crop->name,
                    'variety' => $crop->variety,
                ];
            });

        return response()->json([
            'success' => true,
            'crops' => $crops
        ]);
    }
}
