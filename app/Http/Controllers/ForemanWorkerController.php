<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class ForemanWorkerController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', 'worker');

        // Búsqueda por nombre o email
        $search = $request->get('search');
        $status = $request->get('status');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Filtro por estado (activo/inactivo basado en email_verified_at)
        if ($status && $status !== 'all') {
            if ($status === 'active') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        $workers = $query->orderBy('name')->paginate(15);

        return view('foreman.workers.index', compact('workers', 'search', 'status'));
    }

    public function show(User $worker): View|JsonResponse
    {
        // Verificar que sea un trabajador
        if ($worker->role !== 'worker') {
            abort(404);
        }

        // Cargar tareas del trabajador
        $tasks = $worker->assignedTasks()->with(['plot', 'crop'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Si es una petición AJAX, devolver JSON
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'worker' => [
                    'id' => $worker->id,
                    'name' => $worker->name,
                    'email' => $worker->email,
                    'status' => $worker->email_verified_at ? 'active' : 'inactive',
                    'created_at' => $worker->created_at->format('d/m/Y H:i'),
                    'updated_at' => $worker->updated_at->format('d/m/Y H:i'),
                ],
                'tasks' => $tasks->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'description' => $task->description,
                        'status' => $task->status,
                        'scheduled_for' => $task->scheduled_for ? $task->scheduled_for->format('d/m/Y') : 'Sin fecha',
                        'plot_name' => $task->plot->name ?? 'Sin lote',
                    ];
                })
            ]);
        }

        return view('foreman.workers.show', compact('worker', 'tasks'));
    }

    public function edit(User $worker): View
    {
        // Verificar que sea un trabajador
        if ($worker->role !== 'worker') {
            abort(404);
        }

        return view('foreman.workers.edit', compact('worker'));
    }

    public function update(Request $request, User $worker): RedirectResponse|JsonResponse
    {
        // Verificar que sea un trabajador
        if ($worker->role !== 'worker') {
            abort(404);
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $worker->id,
                'status' => 'nullable|string|in:active,inactive',
            ]);

            // Manejar el estado
            if (isset($validated['status'])) {
                if ($validated['status'] === 'active') {
                    $validated['email_verified_at'] = now();
                } else {
                    $validated['email_verified_at'] = null;
                }
                unset($validated['status']);
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

            return redirect()->route('foreman.workers.index')
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

            return redirect()->route('foreman.workers.index')
                ->with('error', 'Error al actualizar el trabajador');
        }
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
        } else {
            $worker->update(['email_verified_at' => now()]);
            $message = 'Trabajador activado correctamente';
        }

        // Si es una petición AJAX, devolver JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'worker' => [
                    'id' => $worker->id,
                    'name' => $worker->name,
                    'email' => $worker->email,
                    'status' => $worker->email_verified_at ? 'active' : 'inactive',
                    'created_at' => $worker->created_at->format('d/m/Y H:i'),
                    'updated_at' => $worker->updated_at->format('d/m/Y H:i'),
                ]
            ]);
        }

        return redirect()->route('foreman.workers.index')
            ->with('status', $message);
    }

    public function destroy(User $worker): RedirectResponse
    {
        // Verificar que sea un trabajador
        if ($worker->role !== 'worker') {
            abort(404);
        }

        // No permitir eliminar si está activo
        if ($worker->email_verified_at) {
            return redirect()->route('foreman.workers.index')
                ->with('error', 'No se puede eliminar un trabajador activo. Debe desactivarlo primero.');
        }

        // Verificar que no tenga tareas pendientes
        $pendingTasks = \App\Models\Task::where('assigned_to', $worker->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();

        if ($pendingTasks > 0) {
            return redirect()->route('foreman.workers.index')
                ->with('error', 'No se puede eliminar un trabajador que tiene tareas pendientes.');
        }

        $worker->delete();

        return redirect()->route('foreman.workers.index')
            ->with('status', 'Trabajador eliminado correctamente');
    }

    public function downloadPdf(Request $request)
    {
        $query = User::where('role', 'worker');

        $search = $request->get('search');
        $status = $request->get('status');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($status && $status !== 'all') {
            if ($status === 'active') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        $workers = $query->orderBy('name')->get();

        $pdf = Pdf::loadView('foreman.workers.pdf', compact('workers'));
        return $pdf->download('trabajadores-' . now()->format('Y-m-d') . '.pdf');
    }
}
