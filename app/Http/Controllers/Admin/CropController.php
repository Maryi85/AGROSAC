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

    public function destroy(Request $request, Crop $crop): RedirectResponse|JsonResponse
    {
        // Verificar si el cultivo está activo
        if ($crop->status === 'active') {
            $message = 'No se puede eliminar un cultivo que está activo. Primero debe inhabilitarlo cambiando su estado a inactivo.';
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }
            
            return redirect()->route('admin.crops.index')->with('error', $message);
        }

        // Verificar si el cultivo tiene tareas asociadas
        if ($crop->tasks()->count() > 0) {
            $message = 'No se puede eliminar un cultivo que tiene tareas asociadas. Primero debe inhabilitarlo.';
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }
            
            return redirect()->route('admin.crops.index')->with('error', $message);
        }

        // Verificar si el cultivo tiene consumos de insumos asociados
        if ($crop->supplyConsumptions()->count() > 0) {
            $message = 'No se puede eliminar un cultivo que tiene consumos de insumos asociados. Primero debe inhabilitarlo.';
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }
            
            return redirect()->route('admin.crops.index')->with('error', $message);
        }

        // Verificar si el cultivo tiene entradas contables asociadas
        if ($crop->ledgerEntries()->count() > 0) {
            $message = 'No se puede eliminar un cultivo que tiene entradas contables asociadas. Primero debe inhabilitarlo.';
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }
            
            return redirect()->route('admin.crops.index')->with('error', $message);
        }

        $crop->delete();
        
        if ($request->ajax()) {
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
}