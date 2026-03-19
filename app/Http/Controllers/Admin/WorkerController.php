<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkerRequest;
use App\Http\Requests\UpdateWorkerRequest;
use App\Models\User;
use App\Models\Task;
use App\Services\WorkerService;
use App\Notifications\WorkerCredentialsNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\UploadsImages;

class WorkerController extends Controller
{
    use UploadsImages;
    protected WorkerService $workerService;

    public function __construct(WorkerService $workerService)
    {
        $this->workerService = $workerService;
    }

    public function index(Request $request): View
    {
        $query = User::where('role', 'worker');

        // Búsqueda por nombre o email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Filtro por estado (activo/inactivo basado en email_verified_at)
        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'active') {
                $query->whereNotNull('email_verified_at');
            }
            else {
                $query->whereNull('email_verified_at');
            }
        }

        $workers = $query->orderBy('name')->paginate(10);

        return view('admin.workers.index', compact('workers'));
    }

    public function create(): View
    {
        return view('admin.workers.create');
    }

    public function store(StoreWorkerRequest $request): RedirectResponse
    {
        // Generar contraseña temporal
        $tempPassword = Str::random(8);
        $data = $request->validated();

        // Procesar foto si se envía
        // Procesar foto si se envía usando Trait
        if ($request->hasFile('photo')) {
            $path = $this->uploadImage($request->file('photo'), 'users');
            if ($path) {
                $data['photo'] = $path;
            }
        }

        $worker = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'photo' => $data['photo'] ?? null,
            'password' => Hash::make($tempPassword),
            'role' => 'worker',
            'email_verified_at' => now(), // Activar inmediatamente
        ]);

        // Enviar correo con las credenciales
        try {
            $worker->notify(new WorkerCredentialsNotification($tempPassword));
        }
        catch (\Exception $e) {
            Log::error('Error al enviar correo de credenciales al trabajador: ' . $e->getMessage());
        // Continuar aunque falle el envío del correo
        }

        return redirect()->route('admin.workers.index')
            ->with('status', "Trabajador creado correctamente. Las credenciales han sido enviadas por correo electrónico.");
    }

    public function show(User $worker): View
    {
        // Verificar que sea un trabajador
        if ($worker->role !== 'worker') {
            abort(404);
        }

        // Obtener estadísticas del trabajador usando el servicio
        $stats = $this->workerService->getStats($worker);

        // Obtener tareas recientes
        $recentTasks = Task::where('assigned_to', $worker->id)
            ->with(['plot', 'crop'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.workers.show', compact('worker', 'stats', 'recentTasks'));
    }

    public function edit(User $worker): RedirectResponse
    {
        // La edición se realiza mediante el modal en el listado de trabajadores
        return redirect()->route('admin.workers.index');
    }

    public function update(UpdateWorkerRequest $request, User $worker)
    {
        // Verificar que sea un trabajador
        if ($worker->role !== 'worker') {
            abort(404);
        }

        try {
            $validated = $request->validated();

            // Manejar el estado
            if (isset($validated['status'])) {
                if ($validated['status'] === 'active') {
                    $validated['email_verified_at'] = now();
                }
                else {
                    $validated['email_verified_at'] = null;
                }
                unset($validated['status']);
            }

            // Manejo de foto
            // Manejo de foto usando Trait
            if ($request->hasFile('photo')) {
                // eliminar anterior
                $this->deleteImage($worker->photo);

                // subir nueva
                $path = $this->uploadImage($request->file('photo'), 'users');
                if ($path) {
                    $validated['photo'] = $path;
                }
            }

            $worker->update($validated);
            $worker->refresh();

            // Si es una petición AJAX, devolver JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Trabajador actualizado correctamente',
                    'worker' => [
                        'id' => $worker->id,
                        'name' => $worker->name,
                        'email' => $worker->email,
                        'phone' => $worker->phone,
                        'photo' => $worker->photo ? asset('storage/' . $worker->photo) : null,
                        'status' => $worker->email_verified_at ? 'active' : 'inactive'
                    ]
                ]);
            }

            return redirect()->route('admin.workers.index')
                ->with('status', 'Trabajador actualizado correctamente');
        }
        catch (\Illuminate\Validation\ValidationException $e) {
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
        }
        catch (\Exception $e) {
            // Si es una petición AJAX, devolver error JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el trabajador: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('admin.workers.index')
                ->with('error', 'Error al actualizar el trabajador');
        }
    }

    public function destroy(User $worker, Request $request): RedirectResponse|JsonResponse
    {
        // Verificar que sea un trabajador
        if ($worker->role !== 'worker') {
            abort(404);
        }

        // 1. Verificar si tiene historial (Tareas, Préstamos o Movimientos)
        $hasHistory = $worker->assignedTasks()->exists() ||
            $worker->loans()->exists() ||
            $worker->createdMovements()->exists();

        if ($hasHistory) {
            // Escenario B: Tiene historial -> Inactivación Lógica
            $worker->update(['email_verified_at' => null]);
            $message = 'El trabajador ha sido marcado como INACTIVO porque posee historial en el sistema. Sus datos permanecen visibles para consulta.';

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'worker' => [
                        'id' => $worker->id,
                        'name' => $worker->name,
                        'status' => 'inactive'
                    ]
                ]);
            }

            return redirect()->route('admin.workers.index')->with('status', $message);
        }

        // Escenario A: No tiene historial -> Eliminación Física
        $this->deleteImage($worker->photo);
        $worker->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Trabajador eliminado correctamente de la base de datos.',
                'id' => $worker->id,
                'deleted' => true
            ]);
        }

        return redirect()->route('admin.workers.index')
            ->with('status', 'Trabajador eliminado correctamente de la base de datos.');
    }

    public function toggleStatus(Request $request, User $worker): RedirectResponse|JsonResponse
    {
        // Verificar que sea un trabajador
        if ($worker->role !== 'worker') {
            abort(404);
        }

        // Cambiar estado
        if ($worker->email_verified_at) {
            $worker->update(['email_verified_at' => null]);
            $message = 'Trabajador desactivado correctamente';
        }
        else {
            $worker->update(['email_verified_at' => now()]);
            $message = 'Trabajador activado correctamente';
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'worker' => [
                    'id' => $worker->id,
                    'name' => $worker->name,
                    'email' => $worker->email,
                    'phone' => $worker->phone,
                    'photo' => $worker->photo ? asset('storage/' . $worker->photo) : null,
                    'status' => $worker->email_verified_at ? 'active' : 'inactive',
                    'destroy_route' => route('admin.workers.destroy', $worker)
                ]
            ]);
        }

        return redirect()->route('admin.workers.index')
            ->with('status', $message);
    }

    public function downloadPdf(Request $request)
    {
        $query = User::where('role', 'worker');

        // Aplicar los mismos filtros que en index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'active') {
                $query->whereNotNull('email_verified_at');
            }
            else {
                $query->whereNull('email_verified_at');
            }
        }

        $workers = $query->orderBy('name')->get();

        $pdf = Pdf::loadView('admin.workers.pdf', compact('workers'));
        return $pdf->download('trabajadores-' . now()->format('Y-m-d') . '.pdf');
    }

    public function report(User $worker): View
    {
        // Verificar que sea un trabajador
        if ($worker->role !== 'worker') {
            abort(404);
        }

        $data = $this->workerService->getReportData($worker);

        // Desempaquetar datos para la vista (manteniendo compatibilidad con vista actual)
        // La vista espera $tasks, $totalPayment, $totalHours, $totalKilos, $totalTasks, $cropTotals
        return view('admin.workers.report', array_merge(['worker' => $worker], $data));
    }

    public function reportData(User $worker): JsonResponse
    {
        // Verificar que sea un trabajador
        if ($worker->role !== 'worker') {
            abort(404);
        }

        $data = $this->workerService->getReportData($worker);

        // Transformar cropTotals a lista para JSON si es necesario y formatear tareas
        $tasksData = $data['tasks']->map(function ($task) {
            return [
            'date' => $task->scheduled_for->format('d/m/Y'),
            'type' => ucfirst(str_replace('_', ' ', $task->type)),
            'description' => $task->description ?: 'Sin descripción',
            'crop' => $task->crop ? $task->crop->name : 'Sin cultivo',
            'plot' => $task->plot ? $task->plot->name : 'Sin lote',
            'hours' => $task->hours ?? 0,
            'kilos' => $task->kilos ?? 0,
            'price_per_hour' => $task->price_per_hour,
            'price_per_day' => $task->price_per_day,
            'price_per_kg' => $task->price_per_kg,
            'total' => $task->calculated_payment ?? $task->total_payment ?? 0,
            ];
        });

        // Asegurar que cropTotals sea un array de objetos para JSONResponse
        $cropTotalsList = [];
        if (is_array($data['cropTotals']) || $data['cropTotals'] instanceof \Illuminate\Support\Collection) {
            foreach ($data['cropTotals'] as $ct) {
                $cropTotalsList[] = $ct; // Reindexar
            }
        }

        return response()->json([
            'worker' => [
                'name' => $worker->name,
                'email' => $worker->email,
                'status' => $worker->email_verified_at ? 'Activo' : 'Inactivo',
                'registered' => $worker->created_at->format('d/m/Y H:i'),
            ],
            'totals' => [
                'tasks' => $data['totalTasks'],
                'hours' => $data['totalHours'],
                'kilos' => $data['totalKilos'],
                'payment' => $data['totalPayment'],
            ],
            'cropTotals' => $cropTotalsList,
            'tasks' => $tasksData,
        ]);
    }

    public function reportPdf(User $worker)
    {
        // Verificar que sea un trabajador
        if ($worker->role !== 'worker') {
            abort(404);
        }

        $data = $this->workerService->getReportData($worker);

        // Generar PDF
        $pdf = Pdf::loadView('admin.workers.report-pdf', array_merge(['worker' => $worker], $data));
        return $pdf->download('reporte-trabajador-' . $worker->name . '-' . now()->format('Y-m-d') . '.pdf');
    }
}