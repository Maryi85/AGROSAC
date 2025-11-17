<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreToolRequest;
use App\Http\Requests\UpdateToolRequest;
use App\Models\Tool;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;

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

        $layout = route_prefix() === 'foreman.' ? 'foreman.layout' : 'admin.layout';
        return view('admin.tools.index', compact('tools', 'categories', 'statuses'))->with('layout', $layout);
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

        $layout = route_prefix() === 'foreman.' ? 'foreman.layout' : 'admin.layout';
        return view('admin.tools.create', compact('categories', 'statuses'))->with('layout', $layout);
    }

    public function store(StoreToolRequest $request): RedirectResponse
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
                $directory = storage_path('app/public/photos/tools');
                if (!File::exists($directory)) {
                    File::makeDirectory($directory, 0755, true);
                }
                
                // Guardar la foto usando Storage directamente
                $path = Storage::disk('public')->putFileAs('photos/tools', $photo, $photoName);
                
                if ($path) {
                    $data['photo'] = $path;
                }
            } catch (\Exception $e) {
                \Log::error('Error al procesar la foto de herramienta: ' . $e->getMessage());
            }
        }
        
        $tool = Tool::create($data);

        return redirect()->route(route_prefix() . 'tools.index')
            ->with('status', 'Herramienta registrada correctamente');
    }

    public function show(Tool $tool): View
    {
        $layout = route_prefix() === 'foreman.' ? 'foreman.layout' : 'admin.layout';
        return view('admin.tools.show', compact('tool'))->with('layout', $layout);
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

        $layout = route_prefix() === 'foreman.' ? 'foreman.layout' : 'admin.layout';
        return view('admin.tools.edit', compact('tool', 'categories', 'statuses'))->with('layout', $layout);
    }

    public function update(UpdateToolRequest $request, Tool $tool): RedirectResponse
    {
        $data = $request->validated();
        
        // Manejar la subida de la foto
        if ($request->hasFile('photo')) {
            try {
                // Eliminar la foto anterior si existe
                if ($tool->photo && Storage::disk('public')->exists($tool->photo)) {
                    Storage::disk('public')->delete($tool->photo);
                }
                
                $photo = $request->file('photo');
                
                // Generar nombre de archivo seguro (sin espacios ni caracteres especiales)
                $originalName = $photo->getClientOriginalName();
                $extension = $photo->getClientOriginalExtension();
                $safeName = preg_replace('/[^A-Za-z0-9\-_]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
                $photoName = time() . '_' . $safeName . '.' . $extension;
                
                // Asegurar que el directorio existe
                $directory = storage_path('app/public/photos/tools');
                if (!File::exists($directory)) {
                    File::makeDirectory($directory, 0755, true);
                }
                
                // Guardar la foto usando Storage directamente
                $path = Storage::disk('public')->putFileAs('photos/tools', $photo, $photoName);
                
                if ($path) {
                    $data['photo'] = $path;
                }
            } catch (\Exception $e) {
                \Log::error('Error al procesar la foto de herramienta: ' . $e->getMessage());
            }
        }
        
        $tool->update($data);

        return redirect()->route(route_prefix() . 'tools.index')
            ->with('status', 'Herramienta actualizada correctamente');
    }

    public function destroy(Tool $tool): RedirectResponse
    {
        // Verificar si la herramienta tiene préstamos activos
        if ($tool->loans()->where('status', 'active')->exists()) {
            return redirect()->route(route_prefix() . 'tools.index')
                ->with('error', 'No se puede eliminar una herramienta que tiene préstamos activos.');
        }

        $tool->delete();

        return redirect()->route(route_prefix() . 'tools.index')
            ->with('status', 'Herramienta eliminada correctamente');
    }

    public function downloadPdf(Request $request)
    {
        $query = Tool::query();

        // Aplicar los mismos filtros que en index
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $tools = $query->orderBy('name')->get();

        $statuses = [
            'operational' => 'Operacional',
            'damaged' => 'Dañado',
            'lost' => 'Perdido',
            'retired' => 'Retirado',
        ];

        $pdf = Pdf::loadView('admin.tools.pdf', compact('tools', 'statuses'));
        return $pdf->download('herramientas-' . now()->format('Y-m-d') . '.pdf');
    }
}