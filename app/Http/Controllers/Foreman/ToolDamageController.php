<?php

namespace App\Http\Controllers\Foreman;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use App\Models\ToolEntry;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

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

        return view('foreman.tools.damage.index', compact('tools', 'allTools', 'statuses', 'totalTools', 'totalEntries', 'totalAvailable', 'totalDamaged', 'totalLost'));
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

        return view('foreman.tools.damage.create', compact('tools', 'selectedTool', 'damageTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tool_id' => 'required|exists:tools,id',
            'damage_type' => 'required|in:damage,loss',
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Bloqueamos la herramienta para evitar condiciones de carrera al descontar stock
            $tool = Tool::whereKey($request->tool_id)->lockForUpdate()->firstOrFail();
            
            // Verificar que hay suficientes herramientas disponibles
            if ($tool->available_qty < $request->quantity) {
                DB::rollBack();
                return back()->withErrors(['quantity' => 'No hay suficientes herramientas disponibles. Disponible: ' . $tool->available_qty]);
            }

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $originalName = $photo->getClientOriginalName();
                $extension = $photo->getClientOriginalExtension();
                $safeName = preg_replace('/[^A-Za-z0-9\-_]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
                $photoName = time() . '_' . $safeName . '.' . $extension;

                // Aseguramos el directorio destino
                $directory = storage_path('app/public/photos/tool-damages');
                if (!File::exists($directory)) {
                    File::makeDirectory($directory, 0755, true);
                }

                $path = Storage::disk('public')->putFileAs('photos/tool-damages', $photo, $photoName);
                if ($path) {
                    $photoPath = $path;
                }
            }

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
                'damage_photo' => $photoPath,
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

            // Descontar de las existencias disponibles reales
            $tool->decrementAvailableQty($request->quantity);

            DB::commit();

            $message = $request->damage_type === 'damage' 
                ? 'Daño registrado correctamente' 
                : 'Pérdida registrada correctamente';

            return redirect()->route('foreman.tool-damage.index')
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

        return view('foreman.tools.damage.show', compact('tool'));
    }

    public function edit(ToolEntry $tool_damage): View
    {
        $entry = $tool_damage;
        $tool = $entry->tool;

        $damageTypes = [
            'damage' => 'Registrar Daño',
            'loss' => 'Registrar Pérdida',
        ];

        return view('foreman.tools.damage.edit', compact('entry', 'tool', 'damageTypes'));
    }

    public function update(Request $request, ToolEntry $tool_damage): RedirectResponse
    {
        $entry = $tool_damage;
        $request->validate([
            'damage_type' => 'required|in:damage,loss',
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $tool = Tool::whereKey($entry->tool_id)->lockForUpdate()->firstOrFail();

            // Reponer disponibilidad previa
            $previousQty = $entry->type === 'damage' ? ($entry->damaged_qty ?? 0) : ($entry->lost_qty ?? 0);
            if ($previousQty > 0) {
                $tool->incrementAvailableQty($previousQty);
            }

            // Validar disponibilidad para la nueva cantidad
            if ($tool->available_qty < $request->quantity) {
                DB::rollBack();
                return back()->withErrors(['quantity' => 'No hay suficientes herramientas disponibles. Disponible: ' . $tool->available_qty])->withInput();
            }

            // Foto
            $photoPath = $entry->damage_photo;
            if ($request->hasFile('photo')) {
                if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                    Storage::disk('public')->delete($photoPath);
                }

                $photo = $request->file('photo');
                $originalName = $photo->getClientOriginalName();
                $extension = $photo->getClientOriginalExtension();
                $safeName = preg_replace('/[^A-Za-z0-9\-_]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
                $photoName = time() . '_' . $safeName . '.' . $extension;

                $directory = storage_path('app/public/photos/tool-damages');
                if (!File::exists($directory)) {
                    File::makeDirectory($directory, 0755, true);
                }

                $path = Storage::disk('public')->putFileAs('photos/tool-damages', $photo, $photoName);
                if ($path) {
                    $photoPath = $path;
                }
            }

            // Actualizar entrada
            $entry->update([
                'type' => $request->damage_type,
                'entry_date' => $request->date,
                'damage_notes' => $request->damage_type === 'damage' ? $request->notes : null,
                'loss_notes' => $request->damage_type === 'loss' ? $request->notes : null,
                'damage_date' => $request->damage_type === 'damage' ? $request->date : null,
                'loss_date' => $request->damage_type === 'loss' ? $request->date : null,
                'damage_photo' => $photoPath,
                'damaged_qty' => $request->damage_type === 'damage' ? $request->quantity : 0,
                'lost_qty' => $request->damage_type === 'loss' ? $request->quantity : 0,
                'available_qty' => 0,
            ]);

            // Aplicar nueva reducción
            $tool->decrementAvailableQty($request->quantity);

            DB::commit();

            $message = $request->damage_type === 'damage'
                ? 'Daño actualizado correctamente'
                : 'Pérdida actualizada correctamente';

            return redirect()->route('foreman.tool-damage.show', $tool->id)->with('status', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()])->withInput();
        }
    }
}

