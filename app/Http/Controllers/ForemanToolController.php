<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class ForemanToolController extends Controller
{
    public function index(Request $request): View
    {
        $query = Tool::query();

        // Filtros
        $search = $request->get('search');
        $category = $request->get('category');
        $status = $request->get('status');

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $tools = $query->orderBy('name')->paginate(15);

        // Categorías y estados disponibles
        $categories = Tool::distinct()->pluck('category')->filter()->values()->toArray();
        $statuses = [
            'operational' => 'Operacional',
            'damaged' => 'Dañado',
            'lost' => 'Perdido',
            'retired' => 'Retirado'
        ];

        return view('foreman.tools.index', compact('tools', 'categories', 'statuses', 'search', 'category', 'status'));
    }

    public function create(): View
    {
        $categories = Tool::distinct()->pluck('category')->filter()->sort()->values();
        $statuses = ['operational', 'lost', 'damaged', 'retired'];

        return view('foreman.tools.create', compact('categories', 'statuses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'status' => 'required|string|in:operational,lost,damaged,retired',
            'total_qty' => 'required|integer|min:0',
            'available_qty' => 'required|integer|min:0|lte:total_qty',
        ]);

        Tool::create($request->all());

        return redirect()->route('foreman.tools.index')
            ->with('status', 'Herramienta registrada correctamente');
    }

    public function show(Tool $tool): View
    {
        return view('foreman.tools.show', compact('tool'));
    }

    public function edit(Tool $tool): View
    {
        $categories = Tool::distinct()->pluck('category')->filter()->sort()->values();
        $statuses = ['operational', 'lost', 'damaged', 'retired'];

        return view('foreman.tools.edit', compact('tool', 'categories', 'statuses'));
    }

    public function update(Request $request, Tool $tool): RedirectResponse|JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'status' => 'required|string|in:operational,lost,damaged,retired',
            'total_qty' => 'required|integer|min:0',
            'available_qty' => 'required|integer|min:0|lte:total_qty',
        ]);

        try {
            $tool->update($request->all());

            // Si es una petición AJAX, devolver JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Herramienta actualizada correctamente',
                    'tool' => [
                        'id' => $tool->id,
                        'name' => $tool->name,
                        'category' => $tool->category,
                        'status' => $tool->status,
                        'total_qty' => $tool->total_qty,
                        'available_qty' => $tool->available_qty
                    ]
                ]);
            }

            return redirect()->route('foreman.tools.index')
                ->with('status', 'Herramienta actualizada correctamente');
        } catch (\Exception $e) {
            // Si es una petición AJAX, devolver error JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar la herramienta: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('foreman.tools.index')
                ->with('error', 'Error al actualizar la herramienta');
        }
    }

    public function destroy(Tool $tool): RedirectResponse
    {
        $tool->delete();

        return redirect()->route('foreman.tools.index')
            ->with('status', 'Herramienta eliminada correctamente');
    }
}
