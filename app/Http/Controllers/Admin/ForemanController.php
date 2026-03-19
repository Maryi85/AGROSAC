<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreForemanRequest;
use App\Http\Requests\UpdateForemanRequest;
use App\Models\User;
use App\Notifications\ForemanCredentialsNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class ForemanController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', 'foreman');

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

        $foremen = $query->orderBy('name')->paginate(10);

        return view('admin.foremen.index', compact('foremen'));
    }

    public function create(): View
    {
        return view('admin.foremen.create');
    }

    public function store(StoreForemanRequest $request): RedirectResponse
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
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            $path = $photo->storeAs('photos/users', $photoName, 'public');
            if ($path) {
                $data['photo'] = $path;
            }
        }

        $foreman = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'photo' => $data['photo'] ?? null,
            'password' => Hash::make($tempPassword),
            'role' => 'foreman',
            'email_verified_at' => now(), // Activar inmediatamente
        ]);

        // Enviar correo con las credenciales
        try {
            $foreman->notify(new ForemanCredentialsNotification($tempPassword));
        }
        catch (\Exception $e) {
            Log::error('Error al enviar correo de credenciales al mayordomo: ' . $e->getMessage());
        // Continuar aunque falle el envío del correo
        }

        return redirect()->route('admin.foremen.index')
            ->with('status', "Mayordomo creado correctamente. Las credenciales han sido enviadas por correo electrónico.")
            ->with('temp_password', $tempPassword);
    }

    public function show(User $foreman): View
    {
        // Verificar que sea un mayordomo
        if ($foreman->role !== 'foreman') {
            abort(404);
        }

        return view('admin.foremen.show', compact('foreman'));
    }

    public function edit(User $foreman): RedirectResponse
    {
        // La edición se realiza mediante el modal en el listado de mayordomos
        return redirect()->route('admin.foremen.index');
    }

    public function update(UpdateForemanRequest $request, User $foreman): RedirectResponse|JsonResponse
    {
        // Verificar que sea un mayordomo
        if ($foreman->role !== 'foreman') {
            abort(404);
        }

        try {
            $data = $request->validated();


            // Debug temporal
            Log::info('Status field received:', [
                'has_status' => $request->has('status'),
                'status_value' => $request->get('status')
            ]);

            // Manejar el cambio de estado si se proporciona
            if ($request->has('status')) {
                if ($request->status === 'active') {
                    $data['email_verified_at'] = now();
                }
                else {
                    // Verificar que no sea el último mayordomo activo
                    $activeForemanCount = User::where('role', 'foreman')
                        ->whereNotNull('email_verified_at')
                        ->where('id', '!=', $foreman->id)
                        ->count();

                    if ($activeForemanCount < 1) {
                        if ($request->ajax()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'No se puede desactivar el último mayordomo activo del sistema.'
                            ]);
                        }

                        return redirect()->route('admin.foremen.index')
                            ->with('error', 'No se puede desactivar el último mayordomo activo del sistema.');
                    }

                    $data['email_verified_at'] = null;
                }
                unset($data['status']); // Remover el campo status ya que no existe en la tabla
            }

            // Manejo de foto
            if ($request->hasFile('photo')) {
                // eliminar anterior
                if ($foreman->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($foreman->photo)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($foreman->photo);
                }

                $photo = $request->file('photo');
                $originalName = $photo->getClientOriginalName();
                $extension = $photo->getClientOriginalExtension();
                $safeName = preg_replace('/[^A-Za-z0-9\-_]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
                $photoName = time() . '_' . $safeName . '.' . $extension;

                $directory = storage_path('app/public/photos/users');
                if (!\Illuminate\Support\Facades\File::exists($directory)) {
                    \Illuminate\Support\Facades\File::makeDirectory($directory, 0755, true);
                }

                $path = $photo->storeAs('photos/users', $photoName, 'public');
                if ($path) {
                    $data['photo'] = $path;
                }
            }

            $foreman->update($data);


            // Si es una petición AJAX, devolver JSON
            if ($request->ajax()) {
                // Refrescar el modelo para obtener los datos actualizados
                $foreman->refresh();

                $response = [
                    'success' => true,
                    'message' => 'Mayordomo actualizado correctamente',
                    'foreman' => [
                        'id' => $foreman->id,
                        'name' => $foreman->name,
                        'email' => $foreman->email,
                        'phone' => $foreman->phone,
                        'photo' => $foreman->photo ? asset('storage/' . $foreman->photo) : null,
                        'status' => $foreman->email_verified_at ? 'active' : 'inactive',
                        'destroy_route' => route('admin.foremen.destroy', $foreman)
                    ]
                ];


                return response()->json($response);
            }

            return redirect()->route('admin.foremen.index')
                ->with('status', 'Mayordomo actualizado correctamente');
        }
        catch (\Exception $e) {
            // Si es una petición AJAX, devolver error JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el mayordomo: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('admin.foremen.index')
                ->with('error', 'Error al actualizar el mayordomo');
        }
    }

    public function destroy(User $foreman, Request $request): RedirectResponse|JsonResponse
    {
        // Verificar que sea un mayordomo
        if ($foreman->role !== 'foreman') {
            abort(404);
        }

        // Verificar que el mayordomo esté inactivo
        if ($foreman->email_verified_at) {
            $message = 'No se puede eliminar un mayordomo activo. Debe desactivarlo primero.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->route('admin.foremen.index')->with('error', $message);
        }

        // Verificar que no sea el último mayordomo
        $foremanCount = User::where('role', 'foreman')->count();
        if ($foremanCount <= 1) {
            $message = 'No se puede eliminar el último mayordomo del sistema.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->route('admin.foremen.index')->with('error', $message);
        }

        try {
            $foreman->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1] ?? 0;
            if ($errorCode == 1451) {
                $message = 'No se puede eliminar el mayordomo porque tiene registros asociados en el sistema (préstamos, tareas, etc.). Es recomendable desactivarlo en lugar de eliminarlo.';
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return redirect()->route('admin.foremen.index')->with('error', $message);
            }
            throw $e;
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Mayordomo eliminado correctamente',
                'id' => $foreman->id
            ]);
        }

        return redirect()->route('admin.foremen.index')
            ->with('status', 'Mayordomo eliminado correctamente');
    }


    public function toggleStatus(Request $request, User $foreman): RedirectResponse|JsonResponse
    {
        // Verificar que sea un mayordomo
        if ($foreman->role !== 'foreman') {
            abort(404);
        }

        // Verificar que no sea el último mayordomo activo
        if ($foreman->email_verified_at) {
            $activeForemanCount = User::where('role', 'foreman')
                ->whereNotNull('email_verified_at')
                ->count();

            if ($activeForemanCount <= 1) {
                $errorMessage = 'No se puede desactivar el último mayordomo activo del sistema.';

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ]);
                }

                return redirect()->route('admin.foremen.index')
                    ->with('error', $errorMessage);
            }
        }

        // Cambiar estado
        if ($foreman->email_verified_at) {
            $foreman->update(['email_verified_at' => null]);
            $message = 'Mayordomo desactivado correctamente';
        }
        else {
            $foreman->update(['email_verified_at' => now()]);
            $message = 'Mayordomo activado correctamente';
        }

        // Si es una petición AJAX, devolver JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'foreman' => [
                    'id' => $foreman->id,
                    'name' => $foreman->name,
                    'email' => $foreman->email,
                    'phone' => $foreman->phone,
                    'status' => $foreman->email_verified_at ? 'active' : 'inactive',
                    'destroy_route' => route('admin.foremen.destroy', $foreman)
                ]
            ]);
        }

        return redirect()->route('admin.foremen.index')
            ->with('status', $message);
    }

    public function downloadPdf(Request $request)
    {
        $query = User::where('role', 'foreman');

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

        $foremen = $query->orderBy('name')->get();

        $pdf = Pdf::loadView('admin.foremen.pdf', compact('foremen'));
        return $pdf->download('mayordomos-' . now()->format('Y-m-d') . '.pdf');
    }
}