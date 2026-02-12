<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkerRequest;
use App\Http\Requests\UpdateWorkerRequest;
use App\Models\User;
use App\Models\Task;
use App\Models\Crop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class WorkerController extends Controller
{
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
            } else {
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
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $originalName = $photo->getClientOriginalName();
            $extension = $photo->getClientOriginalExtension();
            $safeName = preg_replace('/[^A-Za-z0-9\-_]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
            $photoName = time() . '_' . $safeName . '.' . $extension;

            $directory = storage_path('app/public/photos/users');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            $path = Storage::disk('public')->putFileAs('photos/users', $photo, $photoName);
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

        return redirect()->route('admin.workers.index')
            ->with('status', "Trabajador creado correctamente. Contraseña temporal: {$tempPassword}")
            ->with('temp_password', $tempPassword);
    }

    public function show(User $worker): View
    {
        // Verificar que sea un trabajador
        if ($worker->role !== 'worker') {
            abort(404);
        }

        // Obtener estadísticas del trabajador
        $stats = $this->getWorkerStats($worker);
        
        // Obtener tareas recientes
        $recentTasks = Task::where('assigned_to', $worker->id)
            ->with(['plot', 'crop'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.workers.show', compact('worker', 'stats', 'recentTasks'));
    }

    public function edit(User $worker): View
    {
        // Verificar que sea un trabajador
        if ($worker->role !== 'worker') {
            abort(404);
        }

        return view('admin.workers.edit', compact('worker'));
    }

    public function update(UpdateWorkerRequest $request, User $worker): RedirectResponse|JsonResponse
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
                } else {
                    $validated['email_verified_at'] = null;
                }
                unset($validated['status']);
            }
            
            // Manejo de foto
            if ($request->hasFile('photo')) {
                // eliminar anterior
                if ($worker->photo && Storage::disk('public')->exists($worker->photo)) {
                    Storage::disk('public')->delete($worker->photo);
                }
                $photo = $request->file('photo');
                $originalName = $photo->getClientOriginalName();
                $extension = $photo->getClientOriginalExtension();
                $safeName = preg_replace('/[^A-Za-z0-9\-_]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
                $photoName = time() . '_' . $safeName . '.' . $extension;

                $directory = storage_path('app/public/photos/users');
                if (!File::exists($directory)) {
                    File::makeDirectory($directory, 0755, true);
                }

                $path = Storage::disk('public')->putFileAs('photos/users', $photo, $photoName);
                if ($path) {
                    $validated['photo'] = $path;
                }
            }

            $worker->update($validated);

            // Si es una petición AJAX, devolver JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Trabajador actualizado correctamente',
                    'worker' => [
                        'id' => $worker->id,
                        'name' => $worker->name,
                        'email' => $worker->email,
                        'status' => $worker->email_verified_at ? 'active' : 'inactive'
                    ]
                ]);
            }

            return redirect()->route('admin.workers.index')
                ->with('status', 'Trabajador actualizado correctamente');
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
                    'message' => 'Error al actualizar el trabajador: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('admin.workers.index')
                ->with('error', 'Error al actualizar el trabajador');
        }
    }

    public function destroy(User $worker): RedirectResponse
    {
        // Verificar que sea un trabajador
        if ($worker->role !== 'worker') {
            abort(404);
        }

        // Verificar que el trabajador esté inactivo
        if ($worker->email_verified_at) {
            return redirect()->route('admin.workers.index')
                ->with('error', 'No se puede eliminar un trabajador activo. Debe desactivarlo primero.');
        }

        // Verificar que no tenga tareas pendientes
        $pendingTasks = Task::where('assigned_to', $worker->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();

        if ($pendingTasks > 0) {
            return redirect()->route('admin.workers.index')
                ->with('error', 'No se puede eliminar un trabajador que tiene tareas pendientes.');
        }

        $worker->delete();

        return redirect()->route('admin.workers.index')
            ->with('status', 'Trabajador eliminado correctamente');
    }

    public function toggleStatus(User $worker): RedirectResponse
    {
        // Verificar que sea un trabajador
        if ($worker->role !== 'worker') {
            abort(404);
        }

        // Cambiar estado
        if ($worker->email_verified_at) {
            $worker->update(['email_verified_at' => null]);
            $message = 'Trabajador desactivado correctamente';
        } else {
            $worker->update(['email_verified_at' => now()]);
            $message = 'Trabajador activado correctamente';
        }

        return redirect()->route('admin.workers.index')
            ->with('status', $message);
    }

    private function getWorkerStats(User $worker): array
    {
        $totalTasks = Task::where('assigned_to', $worker->id)->count();
        $completedTasks = Task::where('assigned_to', $worker->id)->where('status', 'completed')->count();
        $approvedTasks = Task::where('assigned_to', $worker->id)->where('status', 'approved')->count();
        $pendingTasks = Task::where('assigned_to', $worker->id)->whereIn('status', ['pending', 'in_progress'])->count();

        // Calcular total de horas trabajadas
        $totalHours = Task::where('assigned_to', $worker->id)
            ->where('status', 'approved')
            ->sum('hours');

        // Calcular total de kilos cosechados
        $totalKilos = Task::where('assigned_to', $worker->id)
            ->where('status', 'approved')
            ->where('type', 'harvest')
            ->sum('kilos');

        return [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'approved_tasks' => $approvedTasks,
            'pending_tasks' => $pendingTasks,
            'total_hours' => $totalHours,
            'total_kilos' => $totalKilos,
        ];
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
            } else {
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

        // Obtener todas las tareas aprobadas del trabajador con información de cultivo y precios
        // También incluir tareas completadas que puedan tener información de pago
        $tasks = Task::where('assigned_to', $worker->id)
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

        return view('admin.workers.report', compact('worker', 'tasks', 'totalPayment', 'totalHours', 'totalKilos', 'totalTasks', 'cropTotals'));
    }

    public function reportData(User $worker): JsonResponse
    {
        // Verificar que sea un trabajador
        if ($worker->role !== 'worker') {
            abort(404);
        }

        // Obtener todas las tareas aprobadas del trabajador con información de cultivo y precios
        $tasks = Task::where('assigned_to', $worker->id)
            ->whereIn('status', ['approved', 'completed'])
            ->with(['crop', 'plot'])
            ->orderBy('scheduled_for', 'desc')
            ->get();

        // Calcular el total_payment para cada tarea si no está guardado o es 0
        $tasks = $tasks->map(function ($task) {
            $calculatedPayment = 0;
            
            if ($task->price_per_hour && $task->hours > 0) {
                $calculatedPayment = $task->hours * $task->price_per_hour;
            } elseif ($task->price_per_day && $task->hours > 0) {
                $days = $task->hours / 8;
                $calculatedPayment = $days * $task->price_per_day;
            } elseif ($task->price_per_kg && $task->kilos > 0) {
                $calculatedPayment = $task->kilos * $task->price_per_kg;
            }
            
            if ($task->total_payment && $task->total_payment > 0) {
                $task->calculated_payment = $task->total_payment;
            } else {
                $task->calculated_payment = $calculatedPayment;
                $task->total_payment = $calculatedPayment;
            }
            
            return $task;
        });

        // Calcular totales
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
            $cropTotals[] = [
                'crop' => $crop ? $crop->name : 'Sin cultivo',
                'tasks_count' => $cropTasks->count(),
                'total_payment' => $cropPayment,
                'total_hours' => $cropHours,
                'total_kilos' => $cropKilos,
            ];
        }

        return response()->json([
            'worker' => [
                'name' => $worker->name,
                'email' => $worker->email,
                'status' => $worker->email_verified_at ? 'Activo' : 'Inactivo',
                'registered' => $worker->created_at->format('d/m/Y H:i'),
            ],
            'totals' => [
                'tasks' => $totalTasks,
                'hours' => $totalHours,
                'kilos' => $totalKilos,
                'payment' => $totalPayment,
            ],
            'cropTotals' => $cropTotals,
            'tasks' => $tasks->map(function ($task) {
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
            }),
        ]);
    }

    public function reportPdf(User $worker)
    {
        // Verificar que sea un trabajador
        if ($worker->role !== 'worker') {
            abort(404);
        }

        // Obtener todas las tareas aprobadas del trabajador
        $tasks = Task::where('assigned_to', $worker->id)
            ->whereIn('status', ['approved', 'completed'])
            ->with(['crop', 'plot'])
            ->orderBy('scheduled_for', 'desc')
            ->get();

        // Calcular el total_payment para cada tarea
        $tasks = $tasks->map(function ($task) {
            $calculatedPayment = 0;
            
            if ($task->price_per_hour && $task->hours > 0) {
                $calculatedPayment = $task->hours * $task->price_per_hour;
            } elseif ($task->price_per_day && $task->hours > 0) {
                $days = $task->hours / 8;
                $calculatedPayment = $days * $task->price_per_day;
            } elseif ($task->price_per_kg && $task->kilos > 0) {
                $calculatedPayment = $task->kilos * $task->price_per_kg;
            }
            
            if ($task->total_payment && $task->total_payment > 0) {
                $task->calculated_payment = $task->total_payment;
            } else {
                $task->calculated_payment = $calculatedPayment;
                $task->total_payment = $calculatedPayment;
            }
            
            return $task;
        });

        // Calcular totales
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

        // Generar PDF
        $pdf = Pdf::loadView('admin.workers.report-pdf', compact('worker', 'tasks', 'totalPayment', 'totalHours', 'totalKilos', 'totalTasks', 'cropTotals'));
        return $pdf->download('reporte-trabajador-' . $worker->name . '-' . now()->format('Y-m-d') . '.pdf');
    }
}