<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use App\Models\ToolEntry;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ToolDamageController extends Controller
{
    public function index(Request $request): View
    {
        $query = Tool::with('entries');

        // Filtro por tipo de problema
        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'damaged') {
                $query->whereHas('entries', function($q) {
                    $q->where('damaged_qty', '>', 0);
                });
            } elseif ($request->status === 'lost') {
                $query->whereHas('entries', function($q) {
                    $q->where('lost_qty', '>', 0);
                });
            }
        }

        // Filtro por herramienta
        if ($request->filled('tool_id') && $request->tool_id !== 'all') {
            $query->where('id', $request->tool_id);
        }

        $tools = $query->orderBy('name')->paginate(20);

        // Obtener herramientas para el filtro
        $allTools = Tool::orderBy('name')->get();

        // Calcular totales
        $totalTools = Tool::count();
        $totalEntries = ToolEntry::sum('quantity') ?: 0;
        $totalAvailable = ToolEntry::sum('available_qty') ?: 0;
        $totalDamaged = ToolEntry::sum('damaged_qty') ?: 0;
        $totalLost = ToolEntry::sum('lost_qty') ?: 0;

        $statuses = [
            'all' => 'Todas las herramientas',
            'damaged' => 'Solo dañadas',
            'lost' => 'Solo perdidas',
        ];

        return view('admin.tools.damage.index', compact('tools', 'allTools', 'statuses', 'totalTools', 'totalEntries', 'totalAvailable', 'totalDamaged', 'totalLost'));
    }

    public function create(Request $request): View
    {
        $tools = Tool::orderBy('name')->get();
        
        // Si se especifica una herramienta en la URL
        $selectedTool = null;
        if ($request->filled('tool_id')) {
            $selectedTool = Tool::find($request->tool_id);
        }

        $damageTypes = [
            'damage' => 'Registrar Daño',
            'loss' => 'Registrar Pérdida',
        ];

        return view('admin.tools.damage.create', compact('tools', 'selectedTool', 'damageTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tool_id' => 'required|exists:tools,id',
            'damage_type' => 'required|in:damage,loss',
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $tool = Tool::findOrFail($request->tool_id);
        
        // Verificar que hay suficientes herramientas disponibles
        if ($request->damage_type === 'damage' && $tool->available_qty < $request->quantity) {
            return back()->withErrors(['quantity' => 'No hay suficientes herramientas disponibles para marcar como dañadas. Disponible: ' . $tool->available_qty]);
        }

        if ($request->damage_type === 'loss' && $tool->available_qty < $request->quantity) {
            return back()->withErrors(['quantity' => 'No hay suficientes herramientas disponibles para marcar como perdidas. Disponible: ' . $tool->available_qty]);
        }

        DB::beginTransaction();
        try {
            // Crear nueva entrada de daño/pérdida
            $entry = ToolEntry::create([
                'tool_id' => $request->tool_id,
                'quantity' => 0, // No es una entrada nueva
                'type' => $request->damage_type === 'damage' ? 'damage' : 'loss',
                'entry_date' => $request->date,
                'damage_notes' => $request->damage_type === 'damage' ? $request->notes : null,
                'loss_notes' => $request->damage_type === 'loss' ? $request->notes : null,
                'damage_date' => $request->damage_type === 'damage' ? $request->date : null,
                'loss_date' => $request->damage_type === 'loss' ? $request->date : null,
                'created_by' => auth()->id(),
            ]);

            // Actualizar cantidades en la entrada
            if ($request->damage_type === 'damage') {
                $entry->update([
                    'damaged_qty' => $request->quantity,
                    'available_qty' => 0, // No disponible
                ]);
            } else {
                $entry->update([
                    'lost_qty' => $request->quantity,
                    'available_qty' => 0, // No disponible
                ]);
            }

            DB::commit();

            $message = $request->damage_type === 'damage' 
                ? 'Daño registrado correctamente' 
                : 'Pérdida registrada correctamente';

            return redirect()->route('admin.tools.damage.index')
                ->with('status', $message);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al registrar: ' . $e->getMessage()]);
        }
    }

    public function show(Tool $tool_damage): View
    {
        $tool = $tool_damage;
        $tool->load(['entries' => function($query) {
            $query->where(function($q) {
                $q->where('damaged_qty', '>', 0)
                  ->orWhere('lost_qty', '>', 0);
            })->orderBy('entry_date', 'desc');
        }]);

        return view('admin.tools.damage.show', compact('tool'));
    }
}