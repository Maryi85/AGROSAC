<?php

namespace App\Http\Controllers\Foreman;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;

class ToolController extends Controller
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

        $tools = $query->orderBy('name')->paginate(10);

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
            'description' => 'nullable|string',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
        ]);

        $tool = Tool::create($request->only([
            'name', 'category', 'status', 'description', 'brand', 'model', 'serial_number'
        ]));
        
        return redirect()->route('foreman.tools.index')
            ->with('status', 'Herramienta registrada correctamente. Ahora puedes agregar entradas de inventario desde la sección de entradas.');
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
            'description' => 'nullable|string',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'total_qty' => 'nullable|integer|min:0',
            'available_qty' => 'nullable|integer|min:0',
        ]);

        try {
            $data = $request->only([
                'name', 'category', 'status', 'description', 'brand', 'model', 'serial_number', 'total_qty', 'available_qty'
            ]);
            
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
            $tool->refresh();

            // Si es una petición AJAX, devolver JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Herramienta actualizada correctamente',
                    'tool' => [
                        'id' => $tool->id,
                        'name' => $tool->name,
                        'category' => $tool->category,
                        'status' => $tool->status,
                        'total_entries' => $tool->total_entries,
                        'available_qty' => $tool->available_qty,
                        'photo' => $tool->photo ? asset('storage/' . $tool->photo) : null
                    ]
                ]);
            }
            return redirect()->route('foreman.tools.index')
                ->with('status', 'Herramienta actualizada correctamente');
        } catch (\Exception $e) {
            // Si es una petición AJAX, devolver error JSON
            if ($request->ajax() || $request->wantsJson()) {
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
        $toolName = $tool->name;
        $tool->delete();
        return redirect()->route('foreman.tools.index')
            ->with('status', 'Herramienta eliminada correctamente');
    }

    public function downloadPdf(Request $request)
    {
        $query = Tool::query();

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

        $tools = $query->orderBy('name')->get();

        $statuses = [
            'operational' => 'Operacional',
            'damaged' => 'Dañado',
            'lost' => 'Perdido',
            'retired' => 'Retirado'
        ];

        $pdf = Pdf::loadView('foreman.tools.pdf', compact('tools', 'statuses'));
        return $pdf->download('herramientas-' . now()->format('Y-m-d') . '.pdf');
    }
}

