<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlotRequest;
use App\Http\Requests\UpdatePlotRequest;
use App\Models\Plot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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

        return view('admin.plots.index', compact('plots', 'search'));
    }

    public function create(): View
    {
        return view('admin.plots.create');
    }

    public function store(StorePlotRequest $request): RedirectResponse
    {
        Plot::create($request->validated());
        return redirect()->route('admin.plots.index')->with('status', 'Lote creado correctamente');
    }

    public function edit(Plot $plot): View
    {
        return view('admin.plots.edit', compact('plot'));
    }

    public function update(UpdatePlotRequest $request, Plot $plot): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();
        
        // Verificar si se está intentando inhabilitar un lote que tiene cultivos activos
        if ($plot->status === 'active' && $validated['status'] === 'inactive') {
            $activeCrops = $plot->crops()->where('status', 'active')->count();
            if ($activeCrops > 0) {
                if ($request->ajax()) {
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
        
        if ($request->ajax()) {
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
}


