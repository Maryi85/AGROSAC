<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CropTracking;
use App\Models\Crop;
use App\Models\Plot;
use Illuminate\Http\Request;

class CropTrackingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trackings = CropTracking::with(['crop', 'plot', 'createdBy'])
            ->orderBy('tracking_date', 'desc')
            ->paginate(20);

        return view('admin.crops.tracking.index', compact('trackings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $crops = Crop::where('status', 'active')->with('plot')->get();
        
        $phases = [
            'siembra' => 'Siembra',
            'germinacion' => 'Germinación',
            'crecimiento' => 'Crecimiento',
            'floracion' => 'Floración',
            'fructificacion' => 'Fructificación',
            'madurez' => 'Madurez',
            'cosecha' => 'Cosecha',
        ];

        return view('admin.crops.tracking.create', compact('crops', 'phases'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tracking_date' => 'required|date',
            'plot_id' => 'required|exists:plots,id',
            'crop_id' => 'required|exists:crops,id',
            'phase' => 'nullable|string|max:255',
            'cut_date' => 'nullable|date|after_or_equal:tracking_date',
        ]);

        CropTracking::create([
            'tracking_date' => $request->tracking_date,
            'plot_id' => $request->plot_id,
            'crop_id' => $request->crop_id,
            'phase' => $request->phase,
            'cut_date' => $request->cut_date,
            'created_by' => auth()->id()
        ]);

        return redirect()->route('admin.crop-tracking.index')
            ->with('status', 'Seguimiento de cultivo registrado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CropTracking $tracking)
    {
        $tracking->load(['crop', 'plot', 'createdBy']);
        return view('admin.crops.tracking.show', compact('tracking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($tracking)
    {
        $trackingModel = CropTracking::findOrFail($tracking);
        $crops = Crop::where('status', 'active')->with('plot')->get();
        
        $phases = [
            'siembra' => 'Siembra',
            'germinacion' => 'Germinación',
            'crecimiento' => 'Crecimiento',
            'floracion' => 'Floración',
            'fructificacion' => 'Fructificación',
            'madurez' => 'Madurez',
            'cosecha' => 'Cosecha',
        ];
        
        // Cargar actividades: tareas e insumos relacionados con el cultivo
        $tasks = \App\Models\Task::where('crop_id', $trackingModel->crop_id)
            ->with(['assignee', 'plot'])
            ->orderBy('scheduled_for', 'desc')
            ->get();
        
        $supplyConsumptions = \App\Models\SupplyConsumption::where('crop_id', $trackingModel->crop_id)
            ->with(['supply', 'plot', 'task'])
            ->orderBy('used_at', 'desc')
            ->get();
        
        // Formatear actividades automáticamente
        $activities = [];
        
        foreach ($tasks as $task) {
            $statusLabels = [
                'pending' => 'Pendiente',
                'in_progress' => 'En Progreso',
                'completed' => 'Completada',
                'approved' => 'Aprobada',
                'rejected' => 'Rechazada',
                'invalid' => 'Inválida',
            ];
            
            $activities[] = sprintf(
                "✓ Tarea: %s | Trabajador: %s | Fecha: %s | Estado: %s",
                $task->description ?? 'Sin descripción',
                $task->assignee->name ?? 'N/A',
                $task->scheduled_for ? $task->scheduled_for->format('d/m/Y') : 'N/A',
                $statusLabels[$task->status] ?? $task->status
            );
        }
        
        foreach ($supplyConsumptions as $consumption) {
            $activities[] = sprintf(
                "✓ Insumo: %s | Cantidad: %s %s | Costo: $%s | Fecha: %s",
                $consumption->supply->name ?? 'N/A',
                $consumption->qty,
                $consumption->supply->unit ?? '',
                number_format($consumption->total_cost, 2),
                $consumption->used_at ? $consumption->used_at->format('d/m/Y') : 'N/A'
            );
        }
        
        $activitiesText = implode("\n", $activities);

        return view('admin.crops.tracking.edit', compact('trackingModel', 'crops', 'phases', 'activitiesText'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $tracking)
    {
        $trackingModel = CropTracking::findOrFail($tracking);
        $request->validate([
            'tracking_date' => 'required|date',
            'plot_id' => 'required|exists:plots,id',
            'crop_id' => 'required|exists:crops,id',
            'phase' => 'nullable|string|max:255',
            'cut_date' => 'nullable|date|after_or_equal:tracking_date',
            'activities' => 'nullable|string',
        ]);

        // Generar actividades automáticamente si no se proporcionan
        $activities = $request->activities;
        if (empty($activities)) {
            $tasks = \App\Models\Task::where('crop_id', $request->crop_id)
                ->with(['assignee'])
                ->orderBy('scheduled_for', 'desc')
                ->get();
            
            $supplyConsumptions = \App\Models\SupplyConsumption::where('crop_id', $request->crop_id)
                ->with(['supply'])
                ->orderBy('used_at', 'desc')
                ->get();
            
            $activitiesList = [];
            
            foreach ($tasks as $task) {
                $statusLabels = [
                    'pending' => 'Pendiente',
                    'in_progress' => 'En Progreso',
                    'completed' => 'Completada',
                    'approved' => 'Aprobada',
                    'rejected' => 'Rechazada',
                    'invalid' => 'Inválida',
                ];
                
                $activitiesList[] = sprintf(
                    "✓ Tarea: %s | Trabajador: %s | Fecha: %s | Estado: %s",
                    $task->description ?? 'Sin descripción',
                    $task->assignee->name ?? 'N/A',
                    $task->scheduled_for ? $task->scheduled_for->format('d/m/Y') : 'N/A',
                    $statusLabels[$task->status] ?? $task->status
                );
            }
            
            foreach ($supplyConsumptions as $consumption) {
                $activitiesList[] = sprintf(
                    "✓ Insumo: %s | Cantidad: %s %s | Costo: $%s | Fecha: %s",
                    $consumption->supply->name ?? 'N/A',
                    $consumption->qty,
                    $consumption->supply->unit ?? '',
                    number_format($consumption->total_cost, 2),
                    $consumption->used_at ? $consumption->used_at->format('d/m/Y') : 'N/A'
                );
            }
            
            $activities = implode("\n", $activitiesList);
        }

        $trackingModel->update([
            'tracking_date' => $request->tracking_date,
            'plot_id' => $request->plot_id,
            'crop_id' => $request->crop_id,
            'phase' => $request->phase,
            'cut_date' => $request->cut_date,
            'activities' => $activities,
        ]);

        return redirect()->route('admin.crop-tracking.index')
            ->with('status', 'Seguimiento de cultivo actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $tracking)
    {
        $trackingModel = CropTracking::findOrFail($tracking);
        $trackingModel->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Seguimiento de cultivo eliminado exitosamente.'
            ]);
        }

        return redirect()->route('admin.crop-tracking.index')
            ->with('status', 'Seguimiento de cultivo eliminado exitosamente.');
    }
}
