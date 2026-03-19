<?php

namespace App\Http\Controllers\Foreman;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\Plot;
use App\Models\Crop;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class TaskController extends Controller
{
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(Request $request): View
    {
        $search = (string)$request->string('q');
        $status = (string)$request->string('status');

        $filters = compact('search', 'status');

        $tasks = $this->taskService->getQuery($filters)->paginate(15)->withQueryString();

        $statuses = [
            'pending' => 'Pendiente',
            'in_progress' => 'En Progreso',
            'completed' => 'Completada',
            'approved' => 'Aprobada',
            'rejected' => 'Rechazada',
            'invalid' => 'Inválida',
        ];

        $allSupplies = \App\Models\Supply::all(['id', 'name', 'unit', 'category'])->keyBy('id');

        return view('foreman.tasks.index', compact('tasks', 'search', 'status', 'statuses', 'allSupplies'));
    }

    public function create(): View
    {
        $workers = User::where('role', 'worker')
            ->whereNotNull('email_verified_at')
            ->orderBy('name')
            ->get();
        $plots = Plot::where('status', 'active')->orderBy('name')->get();
        $crops = Crop::orderBy('name')->get();
        $taskTypes = [
            'daily' => 'Diaria',
            'harvest' => 'Cosecha',
            'maintenance' => 'Mantenimiento',
            'planting' => 'Siembra',
            'irrigation' => 'Riego',
            'fertilization' => 'Fertilización'
        ];
        $supplies = \App\Models\Supply::where('status', 'active')->orderBy('name')->get();

        return view('foreman.tasks.create', compact('workers', 'plots', 'crops', 'taskTypes', 'supplies'));
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->taskService->create($data);

        return redirect()->route('foreman.tasks.index')
            ->with('status', 'Tarea asignada correctamente');
    }

    public function show(Task $task): View
    {
        $task->load(['assignee', 'plot', 'crop', 'approver']);

        return view('foreman.tasks.show', compact('task'));
    }

    public function edit(Task $task): View
    {
        $workers = User::where('role', 'worker')
            ->whereNotNull('email_verified_at')
            ->orderBy('name')
            ->get();
        $plots = Plot::where('status', 'active')->orderBy('name')->get();
        $crops = Crop::orderBy('name')->get();
        $taskTypes = [
            'daily' => 'Diaria',
            'harvest' => 'Cosecha',
            'maintenance' => 'Mantenimiento',
            'planting' => 'Siembra',
            'irrigation' => 'Riego',
            'fertilization' => 'Fertilización'
        ];
        $supplies = \App\Models\Supply::where('status', 'active')->orderBy('name')->get();

        return view('foreman.tasks.edit', compact('task', 'workers', 'plots', 'crops', 'taskTypes', 'supplies'));
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $data = $request->validated();

        $this->taskService->update($task, $data);

        return redirect()->route('foreman.tasks.index')
            ->with('status', 'Tarea actualizada correctamente');
    }

    public function destroy(Task $task, Request $request): RedirectResponse|JsonResponse
    {
        // Solo permitir eliminar tareas pendientes o en progreso
        if (!in_array($task->status, ['pending', 'in_progress'])) {
            $message = 'No se puede eliminar una tarea que ya ha sido completada o evaluada.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->route('foreman.tasks.index')
                ->with('error', $message);
        }

        $task->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tarea eliminada correctamente',
                'id' => $task->id
            ]);
        }

        return redirect()->route('foreman.tasks.index')
            ->with('status', 'Tarea eliminada correctamente');
    }

    public function approve(Task $task, Request $request): RedirectResponse|JsonResponse
    {
        // Solo permitir aprobar tareas que no están ya aprobadas o canceladas
        if (in_array($task->status, ['approved', 'cancelled'])) {
            $message = $task->status === 'approved' ? 'La tarea ya está aprobada.' : 'No se puede aprobar una tarea cancelada.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->route('foreman.tasks.index')->with('error', $message);
        }

        $this->taskService->approve($task);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tarea aprobada correctamente',
                'task' => $task->fresh(['assignee', 'plot', 'crop'])
            ]);
        }

        return redirect()->route('foreman.tasks.index')
            ->with('status', 'Tarea aprobada correctamente');
    }

    public function invalidate(Task $task, Request $request): RedirectResponse|JsonResponse
    {
        // Solo permitir invalidar tareas que no están ya inválidas o canceladas
        if (in_array($task->status, ['invalid', 'cancelled'])) {
            $message = $task->status === 'invalid' ? 'La tarea ya es inválida.' : 'No se puede invalidar una tarea cancelada.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->route('foreman.tasks.index')->with('error', $message);
        }

        $this->taskService->invalidate($task);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tarea marcada como inválida',
                'task' => $task->fresh(['assignee', 'plot', 'crop'])
            ]);
        }

        return redirect()->route('foreman.tasks.index')
            ->with('status', 'Tarea marcada como inválida');
    }

    public function complete(Request $request, Task $task): RedirectResponse|JsonResponse
    {
        if (!in_array($task->status, ['pending', 'in_progress'])) {
            $message = 'Solo se pueden completar tareas que están pendientes o en progreso.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->route('foreman.tasks.index')
                ->with('error', $message);
        }

        // Usamos el servicio para asegurar recalculo de pago y consumos
        $this->taskService->complete($task, []);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tarea marcada como completada',
                'task' => $task->fresh(['assignee', 'plot', 'crop'])
            ]);
        }

        return redirect()->route('foreman.tasks.index')
            ->with('status', 'Tarea marcada como completada');
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

    /**
     * Obtener cultivos activos disponibles en tiempo real
     */
    public function getCrops(): JsonResponse
    {
        $crops = Crop::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'variety', 'plot_id'])
            ->map(function ($crop) {
            return [
            'id' => $crop->id,
            'name' => $crop->name,
            'variety' => $crop->variety,
            'plot_id' => $crop->plot_id,
            ];
        });

        return response()->json([
            'success' => true,
            'crops' => $crops
        ]);
    }

    public function downloadPdf(Request $request)
    {
        $search = (string)$request->string('q');
        $status = (string)$request->string('status');

        $filters = compact('search', 'status');

        $tasks = $this->taskService->getQuery($filters)->get();

        $statuses = [
            'pending' => 'Pendiente',
            'in_progress' => 'En Progreso',
            'completed' => 'Completada',
            'approved' => 'Aprobada',
            'rejected' => 'Rechazada',
            'invalid' => 'Inválida',
        ];

        $pdf = Pdf::loadView('foreman.tasks.pdf', compact('tasks', 'statuses'));
        return $pdf->download('tareas-' . now()->format('Y-m-d') . '.pdf');
    }
}
