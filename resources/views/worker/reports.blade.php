@extends('worker.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Reportes de Actividades</h2>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Generate Report Section -->
    <div class="bg-white border rounded p-6">
        <h3 class="text-lg font-semibold text-emerald-700 mb-4">Generar Reporte de Actividades</h3>
        <form method="POST" action="{{ route('worker.reports.generate') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-emerald-700 mb-1">Fecha de inicio</label>
                    <input type="date" id="start_date" name="start_date" required
                           class="w-full border border-emerald-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                
                <div>
                    <label for="end_date" class="block text-sm font-medium text-emerald-700 mb-1">Fecha de fin</label>
                    <input type="date" id="end_date" name="end_date" required
                           class="w-full border border-emerald-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>
            
            <div>
                <label for="report_notes" class="block text-sm font-medium text-emerald-700 mb-1">Notas del reporte (opcional)</label>
                <textarea id="report_notes" name="report_notes" rows="3"
                          class="w-full border border-emerald-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                          placeholder="Agrega comentarios adicionales sobre el período reportado..."></textarea>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors">
                    <i data-lucide="file-text" class="w-4 h-4 inline mr-1"></i>
                    Generar Reporte
                </button>
            </div>
        </form>
    </div>

    <!-- Completed Tasks History -->
    <div class="bg-white border rounded p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-emerald-700">Historial de Tareas Completadas</h3>
            <div class="text-sm text-emerald-600">
                Total: {{ $completedTasks->total() }} tareas
            </div>
        </div>

        @if($completedTasks->count() > 0)
            <div class="space-y-4">
                @foreach($completedTasks as $task)
                    <div class="border border-emerald-200 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h4 class="font-semibold text-emerald-800">{{ $task->description }}</h4>
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                        Completada
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-emerald-600 mb-3">
                                    <div>
                                        <span class="font-medium">Lote:</span> {{ $task->plot->name ?? 'Sin lote' }}
                                    </div>
                                    <div>
                                        <span class="font-medium">Cultivo:</span> {{ $task->crop->name ?? 'Sin cultivo' }}
                                    </div>
                                    @if($task->updated_at)
                                        <div>
                                            <span class="font-medium">Completada:</span> {{ $task->updated_at->format('d/m/Y H:i') }}
                                        </div>
                                    @endif
                                    @if($task->scheduled_for)
                                        <div>
                                            <span class="font-medium">Fecha programada:</span> {{ $task->scheduled_for->format('d/m/Y') }}
                                        </div>
                                    @endif
                                </div>

                                @if($task->description)
                                    <div class="mb-3 p-3 bg-green-50 rounded border">
                                        <span class="text-sm font-medium text-green-700">Descripción:</span>
                                        <p class="text-sm text-green-600 mt-1">{{ $task->description }}</p>
                                    </div>
                                @endif

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                    @if($task->hours)
                                        <div class="p-2 bg-green-50 rounded">
                                            <span class="font-medium text-green-700">Horas trabajadas:</span>
                                            <span class="text-green-600 block">{{ $task->hours }}</span>
                                        </div>
                                    @endif
                                    @if($task->kilos)
                                        <div class="p-2 bg-green-50 rounded">
                                            <span class="font-medium text-green-700">Cantidad recolectada:</span>
                                            <span class="text-green-600 block">{{ $task->kilos }} kg</span>
                                        </div>
                                    @endif
                                    @if($task->type)
                                        <div class="p-2 bg-emerald-50 rounded">
                                            <span class="font-medium text-emerald-700">Tipo de tarea:</span>
                                            <span class="text-emerald-600 block text-xs">{{ ucfirst($task->type) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($completedTasks->hasPages())
                <div class="mt-6">
                    {{ $completedTasks->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-8">
                <i data-lucide="clipboard-check" class="w-12 h-12 text-emerald-400 mx-auto mb-4"></i>
                <h3 class="text-lg font-semibold text-emerald-700 mb-2">No hay tareas completadas</h3>
                <p class="text-emerald-600">No has completado ninguna tarea aún.</p>
            </div>
        @endif
    </div>

    <!-- Quick Stats -->
    @if($completedTasks->count() > 0)
        <div class="bg-white border rounded p-6">
            <h3 class="text-lg font-semibold text-emerald-700 mb-4">Estadísticas Rápidas</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-emerald-50 rounded border">
                    <div class="text-2xl font-semibold text-emerald-700">{{ $completedTasks->count() }}</div>
                    <div class="text-sm text-emerald-600">Tareas completadas</div>
                </div>
                
                <div class="p-4 bg-emerald-50 rounded border">
                    <div class="text-2xl font-semibold text-emerald-700">
                        {{ $completedTasks->where('hours', '>', 0)->sum('hours') }}
                    </div>
                    <div class="text-sm text-emerald-600">Horas trabajadas</div>
                </div>
                
                <div class="p-4 bg-emerald-50 rounded border">
                    <div class="text-2xl font-semibold text-emerald-700">
                        {{ $completedTasks->where('kilos', '>', 0)->sum('kilos') }}
                    </div>
                    <div class="text-sm text-emerald-600">Cantidad recolectada (kg)</div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
// Set default date range (last 30 days)
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
    
    document.getElementById('end_date').value = today.toISOString().split('T')[0];
    document.getElementById('start_date').value = thirtyDaysAgo.toISOString().split('T')[0];
});
</script>
@endsection
