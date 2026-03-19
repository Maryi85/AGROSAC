<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplyRequest;
use App\Http\Requests\UpdateSupplyRequest;
use App\Models\Supply;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\UploadsImages;

class SupplyController extends Controller
{
    use UploadsImages;
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
        $data = $request->validated();

        // Manejar la subida de la foto
        // Manejar la subida de la foto usando el Trait
        if ($request->hasFile('photo')) {
            $path = $this->uploadImage($request->file('photo'), 'supplies');
            if ($path) {
                $data['photo'] = $path;
            }
        }

        $supply = Supply::create($data);
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

    public function update(UpdateSupplyRequest $request, Supply $supply): RedirectResponse|JsonResponse
    {
        $data = $request->validated();

        // Manejar la subida de la foto
        // Manejar la subida de la foto usando el Trait
        if ($request->hasFile('photo')) {
            // Eliminar foto anterior
            $this->deleteImage($supply->photo);

            // Subir nueva foto
            $path = $this->uploadImage($request->file('photo'), 'supplies');
            if ($path) {
                $data['photo'] = $path;
            }
        }

        // Detectar si el unit_cost cambió
        $costChanged = false;
        if (isset($data['unit_cost']) && $data['unit_cost'] != $supply->unit_cost) {
            $costChanged = true;
        }

        $supply->update($data);
        $supply->refresh();

        // Actualizar el costo total en movimientos y consumos históricos si cambió el costo unitario
        if ($costChanged) {
            foreach ($supply->movements as $movement) {
                // Para ingresos ('in'), total_cost es qty * unit_cost
                if ($movement->type === 'in') {
                    $movement->update([
                        'total_cost' => $movement->qty * $supply->unit_cost
                    ]);
                }
            }

            foreach ($supply->consumptions as $consumption) {
                $consumption->update([
                    'total_cost' => $consumption->qty * $supply->unit_cost
                ]);
            }
        }


        // Si es una petición AJAX, devolver JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Insumo actualizado correctamente',
                'supply' => [
                    'id' => $supply->id,
                    'name' => $supply->name,
                    'unit' => $supply->unit,
                    'unit_cost' => $supply->unit_cost,
                    'status' => $supply->status,
                    'photo' => $supply->photo ? asset('storage/' . $supply->photo) : null
                ]
            ]);
        }

        return redirect()->route('admin.supplies.index')
            ->with('status', 'Insumo actualizado correctamente');
    }

    public function destroy(Supply $supply, Request $request): RedirectResponse|JsonResponse
    {
        return DB::transaction(function () use ($supply, $request) {
            try {
                // Auditoría de Dependencias
                $hasHistory = $supply->movements()->exists() || $supply->consumptions()->exists();

                // Escenario A: Insumo con Historial -> Inactivación Lógica
                if ($hasHistory) {
                    $supply->update(['status' => 'inactive']);
                    $message = 'No se puede eliminar el insumo porque tiene historial de movimientos o consumos. Se ha cambiado a estado Inactivo para preservar la integridad de los costos.';

                    if ($request->ajax()) {
                        return response()->json([
                            'success' => true,
                            'message' => $message,
                            'supply' => [
                                'id' => $supply->id,
                                'status' => 'inactive'
                            ]
                        ]);
                    }

                    return redirect()->route('admin.supplies.index')->with('warning', $message);
                }

                // Escenario B: Insumo Nuevo/Sin Uso -> Eliminación Física
                $this->deleteImage($supply->photo);
                $supply->delete();

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Insumo eliminado correctamente del sistema.',
                        'id' => $supply->id,
                        'deleted' => true
                    ]);
                }

                return redirect()->route('admin.supplies.index')
                    ->with('status', 'Insumo eliminado correctamente del sistema.');
            } catch (\Illuminate\Database\QueryException $e) {
                $errorCode = $e->errorInfo[1] ?? 0;
                if ($errorCode == 1451) {
                    $message = 'No se puede eliminar el insumo porque tiene movimientos o registros asociados en el sistema. Es recomendable cambiar su estado a "Inactivo" en lugar de eliminarlo.';
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $message
                        ], 422);
                    }
                    return redirect()->route('admin.supplies.index')->with('error', $message);
                }
                throw $e;
            }
        });
    }

    public function downloadPdf(Request $request)
    {
        $query = Supply::query();

        // Aplicar los mismos filtros que en index
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $supplies = $query->orderBy('name')->get();

        $statuses = [
            'active' => 'Activo',
            'inactive' => 'Inactivo',
        ];

        $pdf = Pdf::loadView('admin.supplies.pdf', compact('supplies', 'statuses'));
        return $pdf->download('insumos-' . now()->format('Y-m-d') . '.pdf');
    }
}