<?php

namespace App\Http\Controllers\Foreman;

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
use Barryvdh\DomPDF\Facade\Pdf;

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

        return view('foreman.supplies.index', compact('supplies', 'statuses'));
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

        return view('foreman.supplies.create', compact('units'));
    }

    public function store(StoreSupplyRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        // Manejar la subida de la foto
        if ($request->hasFile('photo')) {
            try {
                $photo = $request->file('photo');
                
                // Generar nombre de archivo seguro (sin espacios ni caracteres especiales)
                $originalName = $photo->getClientOriginalName();
                $extension = $photo->getClientOriginalExtension();
                $safeName = preg_replace('/[^A-Za-z0-9\-_]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
                $photoName = time() . '_' . $safeName . '.' . $extension;
                
                // Asegurar que el directorio existe
                $directory = storage_path('app/public/photos/supplies');
                if (!File::exists($directory)) {
                    File::makeDirectory($directory, 0755, true);
                }
                
                // Guardar la foto usando Storage directamente
                $path = Storage::disk('public')->putFileAs('photos/supplies', $photo, $photoName);
                
                if ($path) {
                    $data['photo'] = $path;
                }
            } catch (\Exception $e) {
                \Log::error('Error al procesar la foto de insumo: ' . $e->getMessage());
            }
        }
        
        $supply = Supply::create($data);
        return redirect()->route('foreman.supplies.index')
            ->with('status', 'Insumo registrado correctamente');
    }

    public function show(Supply $supply): View
    {
        $supply->load(['consumptions.crop', 'consumptions.plot', 'consumptions.task']);
        return view('foreman.supplies.show', compact('supply'));
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

        return view('foreman.supplies.edit', compact('supply', 'units'));
    }

    public function update(UpdateSupplyRequest $request, Supply $supply): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        
        // Manejar la subida de la foto
        if ($request->hasFile('photo')) {
            try {
                // Eliminar la foto anterior si existe
                if ($supply->photo && Storage::disk('public')->exists($supply->photo)) {
                    Storage::disk('public')->delete($supply->photo);
                }
                
                $photo = $request->file('photo');
                
                // Generar nombre de archivo seguro (sin espacios ni caracteres especiales)
                $originalName = $photo->getClientOriginalName();
                $extension = $photo->getClientOriginalExtension();
                $safeName = preg_replace('/[^A-Za-z0-9\-_]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
                $photoName = time() . '_' . $safeName . '.' . $extension;
                
                // Asegurar que el directorio existe
                $directory = storage_path('app/public/photos/supplies');
                if (!File::exists($directory)) {
                    File::makeDirectory($directory, 0755, true);
                }
                
                // Guardar la foto usando Storage directamente
                $path = Storage::disk('public')->putFileAs('photos/supplies', $photo, $photoName);
                
                if ($path) {
                    $data['photo'] = $path;
                }
            } catch (\Exception $e) {
                \Log::error('Error al procesar la foto de insumo: ' . $e->getMessage());
            }
        }
        
        $supply->update($data);
        $supply->refresh();
        
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
        
        return redirect()->route('foreman.supplies.index')
            ->with('status', 'Insumo actualizado correctamente');
    }

    public function destroy(Supply $supply): RedirectResponse
    {
        // Verificar si el insumo tiene consumos registrados
        if ($supply->consumptions()->exists()) {
            return redirect()->route('foreman.supplies.index')
                ->with('error', 'No se puede eliminar un insumo que tiene consumos registrados.');
        }

        $supplyName = $supply->name;
        $supply->delete();
        return redirect()->route('foreman.supplies.index')
            ->with('status', 'Insumo eliminado correctamente');
    }

    public function downloadPdf(Request $request)
    {
        $query = Supply::query();

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

        $pdf = Pdf::loadView('foreman.supplies.pdf', compact('supplies', 'statuses'));
        return $pdf->download('insumos-' . now()->format('Y-m-d') . '.pdf');
    }
}



