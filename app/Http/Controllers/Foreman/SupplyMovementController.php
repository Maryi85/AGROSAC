<?php

namespace App\Http\Controllers\Foreman;

use App\Http\Controllers\Controller;
use App\Models\SupplyMovement;
use App\Models\Supply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplyMovementController extends Controller
{
    public function index()
    {
        // Actualizar el stock de todos los insumos antes de mostrar los movimientos
        Supply::all()->each(function ($supply) {
            $supply->updateStock();
        });

        $movements = SupplyMovement::with(['supply', 'crop', 'plot', 'task', 'createdBy'])
            ->orderBy('movement_date', 'desc')
            ->paginate(20);

        return view('foreman.supplies.movements.index', compact('movements'));
    }

    public function create(Request $request)
    {
        $type = $request->get('type', 'entry'); // entry o exit
        $supplies = Supply::where('status', 'active')->get();

        return view('foreman.supplies.movements.create', compact('type', 'supplies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supply_id' => 'required|exists:supplies,id',
            'type' => 'required|in:entry,exit',
            'quantity' => 'required|numeric|min:0.001',
            'unit_cost' => 'required|numeric|min:0',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'movement_date' => 'required|date'
        ]);

        DB::beginTransaction();
        try {
            $supply = Supply::findOrFail($request->supply_id);
            
            // Verificar que hay suficiente stock para salidas
            if ($request->type === 'exit' && $supply->current_stock < $request->quantity) {
                return back()->withErrors(['quantity' => 'No hay suficiente stock disponible. Stock actual: ' . $supply->current_stock]);
            }

            $totalCost = $request->quantity * $request->unit_cost;

            $movement = SupplyMovement::create([
                'supply_id' => $request->supply_id,
                'type' => $request->type,
                'quantity' => $request->quantity,
                'unit_cost' => $request->unit_cost,
                'total_cost' => $totalCost,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
                'movement_date' => $request->movement_date
            ]);

            // Actualizar el stock del insumo
            $supply->updateStock();

            // Si es una entrada, actualizar el costo por unidad del insumo
            if ($request->type === 'entry') {
                $supply->unit_cost = $request->unit_cost;
                $supply->save();
            }

            DB::commit();

            $message = $request->type === 'entry' 
                ? 'Entrada de insumo registrada exitosamente.' 
                : 'Salida de insumo registrada exitosamente.';

            return redirect()->route('foreman.supplies.index')
                ->with('status', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al registrar el movimiento: ' . $e->getMessage()]);
        }
    }

    public function show(SupplyMovement $supplyMovement)
    {
        $supplyMovement->load(['supply', 'crop', 'plot', 'task', 'createdBy']);
        return view('foreman.supplies.movements.show', compact('supplyMovement'));
    }

    public function edit(SupplyMovement $supplyMovement)
    {
        $supplies = Supply::where('status', 'active')->get();

        return view('foreman.supplies.movements.edit', compact('supplyMovement', 'supplies'));
    }

    public function update(Request $request, SupplyMovement $supplyMovement)
    {
        $request->validate([
            'supply_id' => 'required|exists:supplies,id',
            'type' => 'required|in:entry,exit',
            'quantity' => 'required|numeric|min:0.001',
            'unit_cost' => 'required|numeric|min:0',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'movement_date' => 'required|date'
        ]);

        DB::beginTransaction();
        try {
            $supply = Supply::findOrFail($request->supply_id);
            
            // Verificar que hay suficiente stock para salidas
            if ($request->type === 'exit') {
                $currentStock = $supply->current_stock;
                // Si es una salida, agregar la cantidad actual del movimiento al stock temporal
                if ($supplyMovement->type === 'exit') {
                    $currentStock += $supplyMovement->quantity;
                }
                
                if ($currentStock < $request->quantity) {
                    return back()->withErrors(['quantity' => 'No hay suficiente stock disponible. Stock actual: ' . $supply->current_stock]);
                }
            }

            $totalCost = $request->quantity * $request->unit_cost;

            $supplyMovement->update([
                'supply_id' => $request->supply_id,
                'type' => $request->type,
                'quantity' => $request->quantity,
                'unit_cost' => $request->unit_cost,
                'total_cost' => $totalCost,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'movement_date' => $request->movement_date
            ]);

            // Actualizar el stock del insumo
            $supply->updateStock();

            // Si es una entrada, actualizar el costo por unidad del insumo
            if ($request->type === 'entry') {
                $supply->unit_cost = $request->unit_cost;
                $supply->save();
            }

            DB::commit();

            return redirect()->route('foreman.supply-movements.index')
                ->with('status', 'Movimiento de inventario actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al actualizar el movimiento: ' . $e->getMessage()]);
        }
    }

    public function destroy(SupplyMovement $supplyMovement)
    {
        DB::beginTransaction();
        try {
            $supply = $supplyMovement->supply;
            $movementInfo = $supply->name . ' (Movimiento: ' . $supplyMovement->quantity . ')';
            $supplyMovement->delete();
            
            // Actualizar el stock del insumo
            $supply->updateStock();

            DB::commit();
            return redirect()->route('foreman.supply-movements.index')
                ->with('status', 'Movimiento de inventario eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al eliminar el movimiento: ' . $e->getMessage()]);
        }
    }
}





