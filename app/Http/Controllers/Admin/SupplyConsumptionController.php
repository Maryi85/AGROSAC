<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplyConsumptionRequest;
use App\Http\Requests\UpdateSupplyConsumptionRequest;
use App\Models\SupplyConsumption;
use App\Models\Supply;
use App\Models\Crop;
use App\Models\Plot;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplyConsumptionController extends Controller
{
    public function index(Request $request): View
    {
        $query = SupplyConsumption::with(['supply', 'crop', 'plot', 'task']);

        // Búsqueda general
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('supply', function ($subQ) use ($searchTerm) {
                        $subQ->where('name', 'like', '%' . $searchTerm . '%');
                    }
                    )
                        ->orWhereHas('crop', function ($subQ) use ($searchTerm) {
                    $subQ->where('name', 'like', '%' . $searchTerm . '%');
                }
                )
                    ->orWhereHas('plot', function ($subQ) use ($searchTerm) {
                    $subQ->where('name', 'like', '%' . $searchTerm . '%');
                }
                );
            });
        }

        $consumptions = $query->orderBy('used_at', 'desc')->paginate(10);
        $supplies = Supply::where('status', 'active')->orderBy('name')->get();
        $crops = Crop::where('status', 'active')->orderBy('name')->get();
        $plots = Plot::orderBy('name')->get();
        $tasks = Task::whereIn('status', ['pending', 'in_progress'])->orderBy('scheduled_for', 'desc')->get();

        return view('admin.supply-consumptions.index', compact('consumptions', 'supplies', 'crops', 'plots', 'tasks'));
    }

    public function create(): View
    {
        $supplies = Supply::where('status', 'active')->orderBy('name')->get();
        $crops = Crop::where('status', 'active')->orderBy('name')->get();
        $plots = Plot::orderBy('name')->get();
        $tasks = Task::whereIn('status', ['pending', 'in_progress'])
            ->orderBy('scheduled_for')
            ->get();

        return view('admin.supply-consumptions.create', compact('supplies', 'crops', 'plots', 'tasks'));
    }

    public function store(StoreSupplyConsumptionRequest $request): RedirectResponse
    {
        $supply = Supply::findOrFail($request->supply_id);

        // Calcular el costo total
        $totalCost = $request->qty * $supply->unit_cost;

        // Crear el consumo
        $consumption = SupplyConsumption::create([
            'supply_id' => $request->supply_id,
            'crop_id' => $request->crop_id,
            'plot_id' => $request->plot_id,
            'task_id' => $request->task_id,
            'qty' => $request->qty,
            'total_cost' => $totalCost,
            'used_at' => $request->used_at,
        ]);

        $consumption->supply->updateStock();

        return redirect()->route('admin.supply-consumptions.index')
            ->with('status', 'Consumo de insumo registrado correctamente');
    }

    public function show(SupplyConsumption $supplyConsumption)
    {
        return redirect()->route('admin.supply-consumptions.index');
    }

    public function edit(SupplyConsumption $supplyConsumption)
    {
        return redirect()->route('admin.supply-consumptions.index');
    }

    public function update(UpdateSupplyConsumptionRequest $request, SupplyConsumption $supplyConsumption): RedirectResponse|JsonResponse
    {
        $supply = Supply::findOrFail($request->supply_id);

        // Calcular el nuevo costo total
        $totalCost = $request->qty * $supply->unit_cost;

        $oldSupplyId = $supplyConsumption->supply_id;
        $qtyChanged = $supplyConsumption->qty != $request->qty;
        $supplyChanged = $oldSupplyId != $request->supply_id;

        $supplyConsumption->update([
            'supply_id' => $request->supply_id,
            'crop_id' => $request->crop_id,
            'plot_id' => $request->plot_id,
            'task_id' => $request->task_id,
            'qty' => $request->qty,
            'total_cost' => $totalCost,
            'used_at' => $request->used_at,
        ]);

        // Recalcular el stock del insumo actual
        $supply->updateStock();

        // Si el insumo cambió, recalcular también el stock del insumo anterior
        if ($supplyChanged) {
            $oldSupply = Supply::find($oldSupplyId);
            if ($oldSupply) {
                $oldSupply->updateStock();
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Consumo de insumo actualizado correctamente',
                'consumption' => $supplyConsumption->load(['supply', 'crop', 'plot'])
            ]);
        }

        return redirect()->route('admin.supply-consumptions.index')
            ->with('status', 'Consumo de insumo actualizado correctamente');
    }

    public function destroy(SupplyConsumption $supplyConsumption): RedirectResponse
    {
        return back()->with('error', 'No se permite eliminar los registros de consumo por integridad de datos.');
    }
}