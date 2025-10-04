<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreToolRequest;
use App\Http\Requests\UpdateToolRequest;
use App\Models\Tool;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ToolController extends Controller
{
    public function index(Request $request): View
    {
        $query = Tool::query();

        // Búsqueda por nombre
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filtro por categoría
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Filtro por estado
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $tools = $query->orderBy('name')->paginate(10);

        // Obtener categorías únicas para el filtro
        $categories = Tool::distinct()->pluck('category')->filter()->sort()->values();

        // Estados disponibles
        $statuses = [
            'operational' => 'Operacional',
            'damaged' => 'Dañado',
            'lost' => 'Perdido',
            'retired' => 'Retirado',
        ];

        return view('admin.tools.index', compact('tools', 'categories', 'statuses'));
    }

    public function create(): View
    {
        $categories = [
            'herramientas_manuales' => 'Herramientas Manuales',
            'herramientas_electricas' => 'Herramientas Eléctricas',
            'equipos_agricolas' => 'Equipos Agrícolas',
            'vehiculos' => 'Vehículos',
            'otros' => 'Otros',
        ];

        $statuses = [
            'operational' => 'Operacional',
            'damaged' => 'Dañado',
            'lost' => 'Perdido',
            'retired' => 'Retirado',
        ];

        return view('admin.tools.create', compact('categories', 'statuses'));
    }

    public function store(StoreToolRequest $request): RedirectResponse
    {
        $tool = Tool::create($request->validated());

        return redirect()->route('admin.tools.index')
            ->with('status', 'Herramienta registrada correctamente');
    }

    public function show(Tool $tool): View
    {
        return view('admin.tools.show', compact('tool'));
    }

    public function edit(Tool $tool): View
    {
        $categories = [
            'herramientas_manuales' => 'Herramientas Manuales',
            'herramientas_electricas' => 'Herramientas Eléctricas',
            'equipos_agricolas' => 'Equipos Agrícolas',
            'vehiculos' => 'Vehículos',
            'otros' => 'Otros',
        ];

        $statuses = [
            'operational' => 'Operacional',
            'damaged' => 'Dañado',
            'lost' => 'Perdido',
            'retired' => 'Retirado',
        ];

        return view('admin.tools.edit', compact('tool', 'categories', 'statuses'));
    }

    public function update(UpdateToolRequest $request, Tool $tool): RedirectResponse
    {
        $tool->update($request->validated());

        return redirect()->route('admin.tools.index')
            ->with('status', 'Herramienta actualizada correctamente');
    }

    public function destroy(Tool $tool): RedirectResponse
    {
        // Verificar si la herramienta tiene préstamos activos
        if ($tool->loans()->where('status', 'active')->exists()) {
            return redirect()->route('admin.tools.index')
                ->with('error', 'No se puede eliminar una herramienta que tiene préstamos activos.');
        }

        $tool->delete();

        return redirect()->route('admin.tools.index')
            ->with('status', 'Herramienta eliminada correctamente');
    }
}