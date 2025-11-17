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
        $query = ToolEntry::with(['tool', 'createdBy']);

        // Filtro por herramienta
        if ($request->filled('tool_id') && $request->tool_id !== 'all') {
            $query->where('tool_id', $request->tool_id);
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
        ];

        return view('admin.tools.entries.create', compact('tools', 'selectedTool', 'types'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tool_id' => 'required|exists:tools,id',
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:purchase,donation,transfer,repair',
            'unit_cost' => 'nullable|numeric|min:0',
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

    public function show(ToolEntry $entry): View
    {
        $entry->load(['tool', 'createdBy']);
        return view('admin.tools.entries.show', compact('entry'));
    }

    public function edit(ToolEntry $entry): View
    {
        $tools = Tool::orderBy('name')->get();
        
        $types = [
            'purchase' => 'Compra',
            'donation' => 'Donación',
            'transfer' => 'Transferencia',
            'repair' => 'Reparación',
        ];

        return view('admin.tools.entries.edit', compact('entry', 'tools', 'types'));
    }

    public function update(Request $request, ToolEntry $entry): RedirectResponse
    {
        $request->validate([
            'tool_id' => 'required|exists:tools,id',
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:purchase,donation,transfer,repair',
            'unit_cost' => 'nullable|numeric|min:0',
            'entry_date' => 'required|date',
            'supplier' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $entry->update($request->all());

        return redirect()->route('admin.tool-entries.index')
            ->with('status', 'Entrada de herramienta actualizada correctamente');
    }

    public function destroy(ToolEntry $entry): RedirectResponse
    {
        $entryInfo = $entry->tool->name . ' (Entrada: ' . $entry->quantity . ')';
        $entry->delete();
        return redirect()->route('admin.tool-entries.index')
            ->with('status', 'Entrada de herramienta eliminada correctamente');
    }
}