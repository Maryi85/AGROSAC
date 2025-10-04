<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplyRequest;
use App\Http\Requests\UpdateSupplyRequest;
use App\Models\Supply;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplyController extends Controller
{
    public function index(Request $request): View
    {
        $query = Supply::query();

        // Búsqueda por nombre
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filtro por estado
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $supplies = $query->orderBy('name')->paginate(10);

        // Estados disponibles
        $statuses = [
            'active' => 'Activo',
            'inactive' => 'Inactivo',
        ];

        return view('admin.supplies.index', compact('supplies', 'statuses'));
    }

    public function create(): View
    {
        $units = [
            'kg' => 'Kilogramos (kg)',
            'lt' => 'Litros (lt)',
            'unit' => 'Unidades',
            'g' => 'Gramos (g)',
            'ml' => 'Mililitros (ml)',
            'lb' => 'Libras (lb)',
            'gal' => 'Galones (gal)',
        ];

        return view('admin.supplies.create', compact('units'));
    }

    public function store(StoreSupplyRequest $request): RedirectResponse
    {
        $supply = Supply::create($request->validated());

        return redirect()->route('admin.supplies.index')
            ->with('status', 'Insumo registrado correctamente');
    }

    public function show(Supply $supply): View
    {
        $supply->load(['consumptions.crop', 'consumptions.plot', 'consumptions.task']);
        return view('admin.supplies.show', compact('supply'));
    }

    public function edit(Supply $supply): View
    {
        $units = [
            'kg' => 'Kilogramos (kg)',
            'lt' => 'Litros (lt)',
            'unit' => 'Unidades',
            'g' => 'Gramos (g)',
            'ml' => 'Mililitros (ml)',
            'lb' => 'Libras (lb)',
            'gal' => 'Galones (gal)',
        ];

        return view('admin.supplies.edit', compact('supply', 'units'));
    }

    public function update(UpdateSupplyRequest $request, Supply $supply): RedirectResponse
    {
        $supply->update($request->validated());

        return redirect()->route('admin.supplies.index')
            ->with('status', 'Insumo actualizado correctamente');
    }

    public function destroy(Supply $supply): RedirectResponse
    {
        // Verificar si el insumo tiene consumos registrados
        if ($supply->consumptions()->exists()) {
            return redirect()->route('admin.supplies.index')
                ->with('error', 'No se puede eliminar un insumo que tiene consumos registrados.');
        }

        $supply->delete();

        return redirect()->route('admin.supplies.index')
            ->with('status', 'Insumo eliminado correctamente');
    }
}