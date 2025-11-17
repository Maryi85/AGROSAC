<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCropRequest;
use App\Http\Requests\UpdateCropRequest;
use App\Models\Crop;
use App\Models\Plot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;

class CropController extends Controller
{
    public function index(Request $request): View
    {
        $search = (string) $request->string('q');
        $status = (string) $request->string('status');
        
        $crops = Crop::query()
            ->with('plot')
            ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->orderBy('status', 'desc')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $statuses = [
            'active' => 'Activo',
            'inactive' => 'Inactivo',
        ];

        $plots = Plot::where('status', 'active')->orderBy('name')->get();

        return view('admin.crops.index', compact('crops', 'search', 'status', 'statuses', 'plots'));
    }

    public function create(): View
    {
        $plots = Plot::where('status', 'active')->orderBy('name')->get();
        return view('admin.crops.create', compact('plots'));
    }

    public function store(StoreCropRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = 'active'; // Los nuevos cultivos siempre se crean como activos
        
        // Manejar la subida de la foto
        if ($request->hasFile('photo')) {
            try {
                $photo = $request->file('photo');
                
                // Generar nombre de archivo seguro (sin espacios ni caracteres especiales)
                $originalName = $photo->getClientOriginalName();
                $extension = $photo->getClientOriginalExtension();
                $safeName = preg_replace('/[^A-Za-z0-9\-_]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
                $photoName = time() . '_' . $safeName . '.' . $extension;
                
                // Asegurar que el directorio existe
                $directory = storage_path('app/public/photos/crops');
                if (!File::exists($directory)) {
                    File::makeDirectory($directory, 0755, true);
                }
                
                // Guardar la foto usando Storage directamente
                $path = Storage::disk('public')->putFileAs('photos/crops', $photo, $photoName);
                
                if ($path) {
                    $data['photo'] = $path;
                    
                    // Verificar que el archivo se guardó físicamente
                    $fullPath = storage_path('app/public/' . $path);
                    if (File::exists($fullPath)) {
                        \Log::info('Foto guardada correctamente', [
                            'path' => $path, 
                            'photo' => $data['photo'], 
                            'original' => $originalName,
                            'full_path' => $fullPath,
                            'file_size' => File::size($fullPath)
                        ]);
                    } else {
                        \Log::error('La foto se guardó en la BD pero no existe físicamente', [
                            'path' => $path,
                            'full_path' => $fullPath
                        ]);
                    }
                } else {
                    \Log::error('Error al guardar la foto - putFileAs retornó false');
                }
            } catch (\Exception $e) {
                \Log::error('Error al procesar la foto: ' . $e->getMessage());
            }
        } else {
            \Log::info('No se recibió archivo de foto');
        }
        
        Crop::create($data);
        
        return redirect()->route('admin.crops.index')
            ->with('status', 'Cultivo creado correctamente');
    }

    public function edit(Crop $crop): View
    {
        $plots = Plot::where('status', 'active')->orderBy('name')->get();
        return view('admin.crops.edit', compact('crop', 'plots'));
    }

    public function update(UpdateCropRequest $request, Crop $crop): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();
        
        // Manejar la subida de la foto
        if ($request->hasFile('photo')) {
            // Eliminar la foto anterior si existe
            if ($crop->photo && Storage::disk('public')->exists($crop->photo)) {
                Storage::disk('public')->delete($crop->photo);
            }
            
            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photo->storeAs('public/photos/crops', $photoName);
            $validated['photo'] = 'photos/crops/' . $photoName;
        }
        
        \Log::info('Updating crop', [
            'crop_id' => $crop->id,
            'validated_data' => $validated,
            'is_ajax' => $request->ajax(),
            'current_status' => $crop->status,
            'new_status' => $validated['status'] ?? 'not_provided'
        ]);
        
        $crop->update($validated);
        
        // Recargar el modelo para obtener los datos actualizados
        $crop->refresh();
        $crop->load('plot');
        
        // Si es una petición AJAX, devolver JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cultivo actualizado correctamente',
                'crop' => [
                    'id' => $crop->id,
                    'name' => $crop->name,
                    'variety' => $crop->variety,
                    'yield_per_hectare' => $crop->yield_per_hectare,
                    'status' => $crop->status,
                    'plot_id' => $crop->plot_id,
                    'plot_name' => $crop->plot ? $crop->plot->name : null
                ]
            ]);
        }
        
        return redirect()->route('admin.crops.index')
            ->with('status', 'Cultivo actualizado correctamente');
    }

    public function destroy(Request $request, $id): RedirectResponse|JsonResponse
    {
        try {
            $crop = Crop::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $message = 'El cultivo no existe o ya fue eliminado.';
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 404);
            }
            
            return redirect()->route('admin.crops.index')->with('error', $message);
        }
        
        // Verificar si el cultivo está activo
        if ($crop->status === 'active') {
            $message = 'No se puede eliminar un cultivo que está activo. Primero debe inhabilitarlo cambiando su estado a inactivo.';
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }
            
            return redirect()->route('admin.crops.index')->with('error', $message);
        }

        // Verificar si el cultivo tiene tareas asociadas
        if ($crop->tasks()->count() > 0) {
            $message = 'No se puede eliminar un cultivo que tiene tareas asociadas. Primero debe inhabilitarlo.';
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }
            
            return redirect()->route('admin.crops.index')->with('error', $message);
        }

        // Verificar si el cultivo tiene consumos de insumos asociados
        if ($crop->supplyConsumptions()->count() > 0) {
            $message = 'No se puede eliminar un cultivo que tiene consumos de insumos asociados. Primero debe inhabilitarlo.';
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }
            
            return redirect()->route('admin.crops.index')->with('error', $message);
        }

        // Verificar si el cultivo tiene entradas contables asociadas
        if ($crop->ledgerEntries()->count() > 0) {
            $message = 'No se puede eliminar un cultivo que tiene entradas contables asociadas. Primero debe inhabilitarlo.';
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }
            
            return redirect()->route('admin.crops.index')->with('error', $message);
        }

        // Eliminar la foto si existe
        if ($crop->photo && Storage::disk('public')->exists($crop->photo)) {
            Storage::disk('public')->delete($crop->photo);
        }

        $crop->delete();
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Cultivo eliminado correctamente']);
        }
        
        return redirect()->route('admin.crops.index')
            ->with('status', 'Cultivo eliminado correctamente');
    }

    public function disable(Crop $crop): RedirectResponse
    {
        // Solo permitir inhabilitar cultivos activos
        if ($crop->status !== 'active') {
            return redirect()->route('admin.crops.index')
                ->with('error', 'Solo se pueden inhabilitar cultivos que están activos.');
        }

        $crop->update(['status' => 'inactive']);
        
        return redirect()->route('admin.crops.index')
            ->with('status', 'Cultivo inhabilitado correctamente');
    }

    public function enable(Crop $crop): RedirectResponse
    {
        // Solo permitir habilitar cultivos inactivos
        if ($crop->status !== 'inactive') {
            return redirect()->route('admin.crops.index')
                ->with('error', 'Solo se pueden habilitar cultivos que están inactivos.');
        }

        $crop->update(['status' => 'active']);
        
        return redirect()->route('admin.crops.index')
            ->with('status', 'Cultivo habilitado correctamente');
    }

    public function show(Crop $crop): View
    {
        $crop->load(['tasks', 'supplyConsumptions', 'ledgerEntries']);
        
        return view('admin.crops.show', compact('crop'));
    }

    public function downloadPdf(Request $request)
    {
        $search = (string) $request->string('q');
        $status = (string) $request->string('status');
        
        $crops = Crop::query()
            ->with('plot')
            ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->orderBy('status', 'desc')
            ->orderBy('name')
            ->get();

        $pdf = Pdf::loadView('admin.crops.pdf', compact('crops'));
        return $pdf->download('cultivos-' . now()->format('Y-m-d') . '.pdf');
    }
}