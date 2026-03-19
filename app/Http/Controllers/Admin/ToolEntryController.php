<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use App\Models\ToolEntry;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ToolEntryController extends Controller
{
    public function index(Request $request): View
    {
        $query = ToolEntry::with(['tool', 'createdBy'])
            ->whereNotIn('type', ['damage', 'loss']);

        // Filtro por herramienta
        if ($request->filled('tool_id') && $request->tool_id !== 'all') {
            $query->where('tool_id', $request->tool_id);
        }

        // Búsqueda por nombre de herramienta
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('tool', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Filtro por tipo
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filtro por fecha
        if ($request->filled('date_from')) {
            $query->where('entry_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('entry_date', '<=', $request->date_to);
        }

        $entries = $query->orderBy('entry_date', 'desc')->paginate(20);

        // Obtener herramientas para el filtro
        $tools = Tool::orderBy('name')->get();

        // Tipos de entrada
        $types = [
            'purchase' => 'Compra',
            'donation' => 'Donación',
            'transfer' => 'Transferencia',
            'repair' => 'Reparación',
        ];

        return view('admin.tools.entries.index', compact('entries', 'tools', 'types'));
    }

    public function create(Request $request): View
    {
        $tools = Tool::orderBy('name')->get();
        
        // Si se especifica una herramienta en la URL
        $selectedTool = null;
        if ($request->filled('tool_id')) {
            $selectedTool = Tool::find($request->tool_id);
        }

        $types = [
            'purchase' => 'Compra',
            'donation' => 'Donación',
            'transfer' => 'Transferencia',
            'repair' => 'Reparación',
            'damage' => 'Daño',
        ];

        return view('admin.tools.entries.create', compact('tools', 'selectedTool', 'types'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tool_id' => 'required|exists:tools,id',
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:purchase,donation,transfer,repair,damage',
            'unit_cost' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type === 'purchase' && (is_null($value) || $value <= 0)) {
                        $fail('El costo unitario debe ser mayor a 0 para compras.');
                    }
                },
            ],
            'entry_date' => 'required|date',
            'supplier' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $entry = ToolEntry::create([
            'tool_id' => $request->tool_id,
            'quantity' => $request->quantity,
            'type' => $request->type,
            'unit_cost' => $request->unit_cost,
            'entry_date' => $request->entry_date,
            'supplier' => $request->supplier,
            'invoice_number' => $request->invoice_number,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.tool-entries.index')
            ->with('status', 'Entrada de herramienta registrada correctamente');
    }

    public function show(ToolEntry $toolEntry): View
    {
        $toolEntry->load(['tool', 'createdBy']);
        return view('admin.tools.entries.show', compact('toolEntry'));
    }

    public function edit(ToolEntry $toolEntry): View
    {
        $tools = Tool::orderBy('name')->get();
        
        $types = [
            'purchase' => 'Compra',
            'donation' => 'Donación',
            'transfer' => 'Transferencia',
            'repair' => 'Reparación',
            'damage' => 'Daño',
        ];

        return view('admin.tools.entries.edit', compact('toolEntry', 'tools', 'types'));
    }

    public function update(Request $request, ToolEntry $toolEntry): RedirectResponse
    {
        $request->validate([
            'tool_id' => 'required|exists:tools,id',
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:purchase,donation,transfer,repair,damage',
            'unit_cost' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type === 'purchase' && (is_null($value) || $value <= 0)) {
                        $fail('El costo unitario debe ser mayor a 0 para compras.');
                    }
                },
            ],
            'entry_date' => 'required|date',
            'supplier' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $toolEntry->update($request->all());

        return redirect()->route('admin.tool-entries.index')
            ->with('status', 'Entrada de herramienta actualizada correctamente');
    }

    public function destroy(ToolEntry $toolEntry): RedirectResponse
    {
        // Validar que la entrada esté intacta
        if ($toolEntry->available_qty < $toolEntry->quantity) {
             return redirect()->route('admin.tool-entries.index')
                ->with('error', 'No se puede eliminar esta entrada porque ya ha sido utilizada (préstamos, daños o pérdidas).');
        }

        $entryInfo = $toolEntry->tool->name . ' (Entrada: ' . $toolEntry->quantity . ')';
        $toolEntry->delete();
        return redirect()->route('admin.tool-entries.index')
            ->with('status', 'Entrada de herramienta eliminada correctamente');
    }
}