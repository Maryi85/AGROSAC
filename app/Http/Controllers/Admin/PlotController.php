<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlotRequest;
use App\Http\Requests\UpdatePlotRequest;
use App\Models\Plot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class PlotController extends Controller
{
    public function index(Request $request): View
    {
        $search = (string) $request->string('q');
        $plots = Plot::query()
            ->with('crops')
            ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $farmSettings = \App\Models\FarmSetting::getFarmSettings();
        
        // Obtener todos los lotes existentes con sus boundaries para mostrarlos en el modal de edición
        $existingPlots = Plot::whereNotNull('boundary')
            ->where('status', 'active')
            ->select('id', 'name', 'boundary', 'latitude', 'longitude')
            ->get()
            ->toArray();
        
        return view('admin.plots.index', compact('plots', 'search', 'farmSettings', 'existingPlots'));
    }

    public function create(): View
    {
        $farmSettings = \App\Models\FarmSetting::getFarmSettings();
        // Obtener todos los lotes existentes con sus boundaries para mostrarlos en el mapa
        $existingPlots = Plot::whereNotNull('boundary')
            ->where('status', 'active')
            ->select('id', 'name', 'boundary', 'latitude', 'longitude')
            ->get();
        
        return view('admin.plots.create', compact('farmSettings', 'existingPlots'));
    }

    public function store(StorePlotRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        
        // Si boundary viene como string JSON, decodificarlo
        if (isset($validated['boundary']) && is_string($validated['boundary'])) {
            $validated['boundary'] = json_decode($validated['boundary'], true);
        }
        
        // Si no hay ubicación pero hay coordenadas, generar una ubicación por defecto
        if (empty($validated['location']) && isset($validated['latitude']) && isset($validated['longitude'])) {
            $validated['location'] = "Lat: " . number_format($validated['latitude'], 6) . ", Lng: " . number_format($validated['longitude'], 6);
        }
        
        Plot::create($validated);
        return redirect()->route('admin.plots.index')->with('status', 'Lote creado correctamente');
    }

    public function edit(Plot $plot): View
    {
        $farmSettings = \App\Models\FarmSetting::getFarmSettings();
        // Obtener todos los lotes existentes con sus boundaries para mostrarlos en el mapa (excluyendo el que se está editando)
        $existingPlots = Plot::whereNotNull('boundary')
            ->where('status', 'active')
            ->where('id', '!=', $plot->id)
            ->select('id', 'name', 'boundary', 'latitude', 'longitude')
            ->get()
            ->toArray(); // Convertir Collection a array para evitar problemas con @json()
        
        return view('admin.plots.edit', compact('plot', 'farmSettings', 'existingPlots'));
    }

    public function update(UpdatePlotRequest $request, Plot $plot): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();
        
        // Si boundary viene como string JSON, decodificarlo
        if (isset($validated['boundary']) && is_string($validated['boundary']) && !empty($validated['boundary'])) {
            $decoded = json_decode($validated['boundary'], true);
            if (json_last_error() === JSON_ERROR_NONE && $decoded !== null) {
                $validated['boundary'] = $decoded;
            } else {
                unset($validated['boundary']);
            }
        } elseif (isset($validated['boundary']) && empty($validated['boundary'])) {
            unset($validated['boundary']);
        }
        
        // Si no hay ubicación pero hay coordenadas, generar una ubicación por defecto
        if (empty($validated['location']) && isset($validated['latitude']) && isset($validated['longitude']) && !empty($validated['latitude']) && !empty($validated['longitude'])) {
            $validated['location'] = "Lat: " . number_format((float)$validated['latitude'], 6) . ", Lng: " . number_format((float)$validated['longitude'], 6);
        }
        
        // Verificar si se está intentando inhabilitar un lote que tiene cultivos activos
        if ($plot->status === 'active' && $validated['status'] === 'inactive') {
            $activeCrops = $plot->crops()->where('status', 'active')->count();
            if ($activeCrops > 0) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => "No se puede inhabilitar este lote porque tiene {$activeCrops} cultivo(s) activo(s). Primero debe inhabilitar o reubicar los cultivos."
                    ], 422);
                }
                return redirect()->route('admin.plots.index')
                    ->with('error', "No se puede inhabilitar este lote porque tiene {$activeCrops} cultivo(s) activo(s). Primero debe inhabilitar o reubicar los cultivos.");
            }
        }
        
        $plot->update($validated);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Lote actualizado correctamente'
            ]);
        }
        
        return redirect()->route('admin.plots.index')->with('status', 'Lote actualizado');
    }

    public function destroy(Plot $plot): RedirectResponse
    {
        // Verificar si el lote tiene cultivos activos
        $activeCrops = $plot->crops()->where('status', 'active')->count();
        if ($activeCrops > 0) {
            return redirect()->route('admin.plots.index')
                ->with('error', "No se puede eliminar este lote porque tiene {$activeCrops} cultivo(s) activo(s). Primero debe inhabilitar o reubicar los cultivos.");
        }

        // Verificar si el lote está activo
        if ($plot->status === 'active') {
            return redirect()->route('admin.plots.index')
                ->with('error', 'No se puede eliminar un lote que está activo. Cambie el estado a inactivo primero.');
        }

        $plot->delete();
        return redirect()->route('admin.plots.index')->with('status', 'Lote eliminado');
    }

    public function downloadPdf(Request $request)
    {
        $search = $request->input('q', '');
        $plots = Plot::query()
            ->with('crops')
            ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->get();

        $pdf = Pdf::loadView('admin.plots.pdf', compact('plots'));
        return $pdf->download('lotes-' . now()->format('Y-m-d') . '.pdf');
    }
}


