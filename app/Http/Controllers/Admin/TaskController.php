<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\User;
use App\Models\Plot;
use App\Models\Crop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $search = (string) $request->string('q');
        $status = (string) $request->string('status');
        
        $tasks = Task::query()
            ->with(['assignee', 'plot', 'crop', 'approver'])
            ->when($search !== '', fn ($q) => $q->where('description', 'like', "%{$search}%")
                ->orWhereHas('assignee', fn ($q) => $q->where('name', 'like', "%{$search}%")))
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->orderBy('scheduled_for', 'desc')
            ->paginate(15)
            ->withQueryString();

        $statuses = [
            'pending' => 'Pendiente',
            'in_progress' => 'En Progreso',
            'completed' => 'Completada',
            'approved' => 'Aprobada',
            'rejected' => 'Rechazada',
            'invalid' => 'Inválida',
        ];

        return view('admin.tasks.index', compact('tasks', 'search', 'status', 'statuses'));
    }

    public function create(): View
    {
        $workers = User::where('role', 'worker')
            ->whereNotNull('email_verified_at')
            ->orderBy('name')
            ->get();
        $plots = Plot::where('status', 'active')->orderBy('name')->get();
        $crops = Crop::orderBy('name')->get();
        
        return view('admin.tasks.create', compact('workers', 'plots', 'crops'));
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        // Determinar el tipo de pago y asignar valores correspondientes
        $paymentType = $data['payment_type'];
        unset($data['payment_type']);
        
        // Calcular el total del pago según el tipo
        $totalPayment = 0;
        
        if ($paymentType === 'hours') {
            $data['hours'] = $data['hours'] ?? 0;
            $data['kilos'] = 0;
            $data['price_per_day'] = null;
            $data['price_per_kg'] = null;
            $totalPayment = ($data['hours'] ?? 0) * ($data['price_per_hour'] ?? 0);
        } elseif ($paymentType === 'days') {
            $data['hours'] = ($data['days'] ?? 1) * 8; // Convertir días a horas (8 horas por día)
            $data['kilos'] = 0;
            $data['price_per_hour'] = null;
            $data['price_per_kg'] = null;
            $totalPayment = ($data['days'] ?? 0) * ($data['price_per_day'] ?? 0);
            unset($data['days']);
        } else { // quantity
            $data['kilos'] = $data['kilos'] ?? 0;
            $data['hours'] = 0;
            $data['price_per_hour'] = null;
            $data['price_per_day'] = null;
            $totalPayment = ($data['kilos'] ?? 0) * ($data['price_per_kg'] ?? 0);
        }
        
        // Asignar el total calculado (o usar el que viene del formulario si existe)
        $data['total_payment'] = $data['total_payment'] ?? $totalPayment;

        Task::create($data);
        
        return redirect()->route('admin.tasks.index')
            ->with('status', 'Tarea asignada correctamente');
    }

    public function edit(Task $task): View
    {
        $workers = User::where('role', 'worker')
            ->whereNotNull('email_verified_at')
            ->orderBy('name')
            ->get();
        $plots = Plot::where('status', 'active')->orderBy('name')->get();
        $crops = Crop::orderBy('name')->get();
        
        return view('admin.tasks.edit', compact('task', 'workers', 'plots', 'crops'));
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $data = $request->validated();
        
        // Determinar el tipo de pago y asignar valores correspondientes
        $paymentType = $data['payment_type'];
        unset($data['payment_type']);
        
        // Calcular el total del pago según el tipo
        $totalPayment = 0;
        
        if ($paymentType === 'hours') {
            $data['hours'] = $data['hours'] ?? 0;
            $data['kilos'] = 0;
            $data['price_per_day'] = null;
            $data['price_per_kg'] = null;
            $totalPayment = ($data['hours'] ?? 0) * ($data['price_per_hour'] ?? 0);
        } elseif ($paymentType === 'days') {
            $data['hours'] = ($data['days'] ?? 1) * 8; // Convertir días a horas
            $data['kilos'] = 0;
            $data['price_per_hour'] = null;
            $data['price_per_kg'] = null;
            $totalPayment = ($data['days'] ?? 0) * ($data['price_per_day'] ?? 0);
            unset($data['days']);
        } else { // quantity
            $data['kilos'] = $data['kilos'] ?? 0;
            $data['hours'] = 0;
            $data['price_per_hour'] = null;
            $data['price_per_day'] = null;
            $totalPayment = ($data['kilos'] ?? 0) * ($data['price_per_kg'] ?? 0);
        }
        
        // Asignar el total calculado (o usar el que viene del formulario si existe)
        $data['total_payment'] = $data['total_payment'] ?? $totalPayment;

        $task->update($data);
        
        return redirect()->route('admin.tasks.index')
            ->with('status', 'Tarea actualizada correctamente');
    }

    public function destroy(Task $task): RedirectResponse
    {
        // Solo permitir eliminar tareas pendientes o en progreso
        if (!in_array($task->status, ['pending', 'in_progress'])) {
            return redirect()->route('admin.tasks.index')
                ->with('error', 'No se puede eliminar una tarea que ya ha sido completada o evaluada.');
        }

        $task->delete();
        
        return redirect()->route('admin.tasks.index')
            ->with('status', 'Tarea eliminada correctamente');
    }

    public function approve(Task $task): RedirectResponse
    {
        // Solo permitir aprobar tareas completadas
        if ($task->status !== 'completed') {
            return redirect()->route('admin.tasks.index')
                ->with('error', 'Solo se pueden aprobar tareas que han sido completadas.');
        }

        $task->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        
        return redirect()->route('admin.tasks.index')
            ->with('status', 'Tarea aprobada correctamente');
    }

    public function invalidate(Task $task): RedirectResponse
    {
        // Solo permitir invalidar tareas completadas
        if ($task->status !== 'completed') {
            return redirect()->route('admin.tasks.index')
                ->with('error', 'Solo se pueden invalidar tareas que han sido completadas.');
        }

        $task->update([
            'status' => 'invalid',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        
        return redirect()->route('admin.tasks.index')
            ->with('status', 'Tarea marcada como inválida');
    }

    public function show(Task $task): View
    {
        $task->load(['assignee', 'plot', 'crop', 'approver']);
        
        return view('admin.tasks.show', compact('task'));
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
        $search = (string) $request->string('q');
        $status = (string) $request->string('status');
        
        $tasks = Task::query()
            ->with(['assignee', 'plot', 'crop', 'approver'])
            ->when($search !== '', fn ($q) => $q->where('description', 'like', "%{$search}%")
                ->orWhereHas('assignee', fn ($q) => $q->where('name', 'like', "%{$search}%")))
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->orderBy('scheduled_for', 'desc')
            ->get();

        $statuses = [
            'pending' => 'Pendiente',
            'in_progress' => 'En Progreso',
            'completed' => 'Completada',
            'approved' => 'Aprobada',
            'rejected' => 'Rechazada',
            'invalid' => 'Inválida',
        ];

        $pdf = Pdf::loadView('admin.tasks.pdf', compact('tasks', 'statuses'));
        return $pdf->download('tareas-' . now()->format('Y-m-d') . '.pdf');
    }
}