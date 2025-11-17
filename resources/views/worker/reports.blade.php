@extends('worker.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Reporte de Actividades</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-6">
    <!-- Información del Trabajador -->
    <div class="mb-6 pb-6 border-b">
        <h3 class="text-xl font-semibold text-emerald-800 mb-4">Información del Trabajador</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre</label>
                <p class="text-lg text-gray-900 mt-1">{{ $user->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <p class="text-lg text-gray-900 mt-1">{{ $user->email }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Estado</label>
                <p class="text-lg mt-1">
                    @if($user->email_verified_at)
                        <span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Activo</span>
                    @else
                        <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Inactivo</span>
                    @endif
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Fecha de Registro</label>
                <p class="text-lg text-gray-900 mt-1">{{ $user->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Resumen General -->
    <div class="mb-6 pb-6 border-b">
        <h3 class="text-xl font-semibold text-emerald-800 mb-4">Resumen General</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="text-sm font-medium text-blue-700">Total de Tareas</div>
                <div class="text-2xl font-bold text-blue-900 mt-2">{{ $totalTasks }}</div>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="text-sm font-medium text-green-700">Total Horas</div>
                <div class="text-2xl font-bold text-green-900 mt-2">{{ number_format($totalHours ?? 0, 2) }}</div>
            </div>
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                <div class="text-sm font-medium text-orange-700">Total Kilos</div>
                <div class="text-2xl font-bold text-orange-900 mt-2">{{ number_format($totalKilos ?? 0, 3) }}</div>
            </div>
            <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                <div class="text-sm font-medium text-emerald-700">Total Acumulado</div>
                <div class="text-2xl font-bold text-emerald-900 mt-2">${{ number_format($totalPayment ?? 0, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Resumen por Cultivo -->
    @if(count($cropTotals) > 0)
    <div class="mb-6 pb-6 border-b">
        <h3 class="text-xl font-semibold text-emerald-800 mb-4">Resumen por Cultivo</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-emerald-800 border-b">
                        <th class="py-3 pr-4">Cultivo</th>
                        <th class="py-3 pr-4 text-right">Tareas</th>
                        <th class="py-3 pr-4 text-right">Horas</th>
                        <th class="py-3 pr-4 text-right">Kilos</th>
                        <th class="py-3 pr-4 text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cropTotals as $cropTotal)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 pr-4 font-medium">{{ $cropTotal['crop'] }}</td>
                        <td class="py-3 pr-4 text-right">{{ $cropTotal['tasks_count'] }}</td>
                        <td class="py-3 pr-4 text-right">{{ number_format($cropTotal['total_hours'], 2) }}</td>
                        <td class="py-3 pr-4 text-right">{{ number_format($cropTotal['total_kilos'], 3) }}</td>
                        <td class="py-3 pr-4 text-right font-semibold text-emerald-700">${{ number_format($cropTotal['total_payment'] ?? 0, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Detalle de Tareas -->
    <div class="mb-6">
        <h3 class="text-xl font-semibold text-emerald-800 mb-4">Detalle de Tareas</h3>
        @if($tasks->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-emerald-800 border-b">
                        <th class="py-3 pr-4">Fecha</th>
                        <th class="py-3 pr-4">Tipo de Tarea</th>
                        <th class="py-3 pr-4">Descripción</th>
                        <th class="py-3 pr-4">Cultivo</th>
                        <th class="py-3 pr-4">Lote</th>
                        <th class="py-3 pr-4 text-right">Horas</th>
                        <th class="py-3 pr-4 text-right">Kilos</th>
                        <th class="py-3 pr-4 text-right">Precio Relacionado</th>
                        <th class="py-3 pr-4 text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $task)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 pr-4">{{ $task->scheduled_for->format('d/m/Y') }}</td>
                        <td class="py-3 pr-4">
                            <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">
                                {{ ucfirst(str_replace('_', ' ', $task->type)) }}
                            </span>
                        </td>
                        <td class="py-3 pr-4">
                            <div class="text-sm text-gray-900 max-w-xs">
                                {{ $task->description ?: 'Sin descripción' }}
                            </div>
                        </td>
                        <td class="py-3 pr-4">
                            <span class="font-medium">{{ $task->crop ? $task->crop->name : 'Sin cultivo' }}</span>
                        </td>
                        <td class="py-3 pr-4">{{ $task->plot ? $task->plot->name : 'Sin lote' }}</td>
                        <td class="py-3 pr-4 text-right">
                            @if($task->hours > 0)
                                {{ number_format($task->hours, 2) }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="py-3 pr-4 text-right">
                            @if($task->kilos > 0)
                                {{ number_format($task->kilos, 3) }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="py-3 pr-4 text-right">
                            @if($task->price_per_hour)
                                <div class="text-sm">
                                    <div class="font-medium text-emerald-700">${{ number_format($task->price_per_hour, 2) }}/hora</div>
                                    <div class="text-xs text-gray-500">{{ number_format($task->hours, 2) }}h × ${{ number_format($task->price_per_hour, 2) }}</div>
                                </div>
                            @elseif($task->price_per_day)
                                <div class="text-sm">
                                    <div class="font-medium text-emerald-700">${{ number_format($task->price_per_day, 2) }}/día</div>
                                    <div class="text-xs text-gray-500">{{ number_format($task->hours / 8, 1) }}d × ${{ number_format($task->price_per_day, 2) }}</div>
                                </div>
                            @elseif($task->price_per_kg)
                                <div class="text-sm">
                                    <div class="font-medium text-emerald-700">${{ number_format($task->price_per_kg, 2) }}/kg</div>
                                    <div class="text-xs text-gray-500">{{ number_format($task->kilos, 3) }}kg × ${{ number_format($task->price_per_kg, 2) }}</div>
                                </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="py-3 pr-4 text-right font-semibold text-emerald-700">
                            ${{ number_format($task->calculated_payment ?? $task->total_payment ?? 0, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-emerald-50 font-semibold">
                        <td colspan="8" class="py-3 pr-4 text-right">Total Acumulado:</td>
                        <td class="py-3 pr-4 text-right text-emerald-700 text-lg">
                            ${{ number_format($totalPayment ?? 0, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <i data-lucide="clipboard-x" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
            <p>No hay tareas aprobadas para este trabajador.</p>
        </div>
        @endif
    </div>

    <!-- Botones de acción -->
    <div class="flex justify-end gap-4 mt-6 pt-6 border-t">
        <a href="{{ route('worker.reports.pdf') }}" 
           class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors">
            <i data-lucide="download" class="w-4 h-4 inline mr-2"></i>
            Descargar PDF
        </a>
        <button onclick="window.print()" 
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
            <i data-lucide="printer" class="w-4 h-4 inline mr-2"></i>
            Imprimir
        </button>
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
@endsection
