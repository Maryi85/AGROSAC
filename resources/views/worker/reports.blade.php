@extends('worker.layout')

@section('header')
<h2 class="text-xl font-bold text-gray-900">Reporte de Actividades</h2>
@endsection

@section('content')
<div class="space-y-6 pb-8">
    <!-- Worker Profile Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative">
        <div class="absolute top-6 right-6 print:hidden">
            <a href="{{ route('worker.reports.pdf') }}" 
               class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-medium shadow-sm transition-all text-sm">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Descargar PDF</span>
            </a>
        </div>
        <div class="p-6">
            <div class="flex items-start gap-4">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0 text-emerald-700 font-bold text-2xl">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-bold text-gray-900 truncate">{{ $user->name }}</h3>
                    <p class="text-gray-500 text-sm mb-4">{{ $user->email }}</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 border-t border-gray-100 pt-4">
                        <div>
                            <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</span>
                            <span class="mt-1 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $user->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                <i data-lucide="{{ $user->email_verified_at ? 'check-circle' : 'minus-circle' }}" class="w-3.5 h-3.5"></i>
                                {{ $user->email_verified_at ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Registro</span>
                            <span class="block mt-1 text-sm font-semibold text-gray-900">{{ $user->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</span>
                            <span class="block mt-1 text-sm font-semibold text-gray-900">Trabajador</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Tasks -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <i data-lucide="check-square" class="w-5 h-5"></i>
                </div>
                <span class="text-sm font-medium text-gray-600">Total Tareas</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $totalTasks }}</p>
        </div>
        
        <!-- Hours -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-orange-50 text-orange-600 rounded-lg">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
                <span class="text-sm font-medium text-gray-600">Total Horas</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalHours ?? 0, 2) }}</p>
        </div>
        
        <!-- Kilos -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                    <i data-lucide="scale" class="w-5 h-5"></i>
                </div>
                <span class="text-sm font-medium text-gray-600">Total Kilos</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalKilos ?? 0, 3) }}</p>
        </div>
        
        <!-- Total -->
        <div class="bg-white rounded-xl shadow-sm border border-emerald-200 p-5 relative overflow-hidden">
            <div class="absolute right-0 top-0 h-full w-1 bg-emerald-500"></div>
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                    <i data-lucide="dollar-sign" class="w-5 h-5"></i>
                </div>
                <span class="text-sm font-medium text-gray-600">Total Acumulado</span>
            </div>
            <p class="text-2xl font-bold text-emerald-700">${{ number_format($totalPayment ?? 0, 2) }}</p>
        </div>
    </div>

    <!-- Crop Summary -->
    @if(count($cropTotals) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <h3 class="font-bold text-gray-900">Resumen por Cultivo</h3>
            <i data-lucide="sprout" class="w-5 h-5 text-gray-400"></i>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 font-medium border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3">Cultivo</th>
                        <th class="px-6 py-3 text-right">Tareas</th>
                        <th class="px-6 py-3 text-right">Horas</th>
                        <th class="px-6 py-3 text-right">Kilos</th>
                        <th class="px-6 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($cropTotals as $cropTotal)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                {{ $cropTotal['crop'] }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right text-gray-600">{{ $cropTotal['tasks_count'] }}</td>
                        <td class="px-6 py-4 text-right text-gray-600">{{ number_format($cropTotal['total_hours'], 2) }}</td>
                        <td class="px-6 py-4 text-right text-gray-600">{{ number_format($cropTotal['total_kilos'], 3) }}</td>
                        <td class="px-6 py-4 text-right font-bold text-emerald-700">${{ number_format($cropTotal['total_payment'] ?? 0, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Tasks Detail -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <h3 class="font-bold text-gray-900">Detalle de Tareas</h3>
            <i data-lucide="list-checks" class="w-5 h-5 text-gray-400"></i>
        </div>
        
        @if($tasks->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 font-medium border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3 whitespace-nowrap">Fecha</th>
                        <th class="px-6 py-3 whitespace-nowrap">Actividad</th>
                        <th class="px-6 py-3 whitespace-nowrap">Ubicación</th>
                        <th class="px-6 py-3 text-right whitespace-nowrap">Medición</th>
                        <th class="px-6 py-3 text-right whitespace-nowrap">Pago</th>
                        <th class="px-6 py-3 text-right whitespace-nowrap">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($tasks as $task)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-gray-900 font-medium">{{ $task->scheduled_for->format('d/m/Y') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-gray-900 font-medium">
                                    {{ ucfirst(str_replace('_', ' ', $task->type)) }}
                                </span>
                                @if($task->description)
                                    <span class="text-gray-500 text-xs truncate max-w-xs">{{ $task->description }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-gray-900">{{ $task->plot ? $task->plot->name : 'Sin lote' }}</span>
                                <span class="text-gray-500 text-xs">{{ $task->crop ? $task->crop->name : 'Sin cultivo' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            @if($task->hours > 0)
                                <div class="text-gray-900">{{ number_format($task->hours, 2) }} h</div>
                            @endif
                            @if($task->kilos > 0)
                                <div class="text-gray-900">{{ number_format($task->kilos, 3) }} kg</div>
                            @endif
                            @if($task->hours <= 0 && $task->kilos <= 0)
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            @if($task->price_per_hour)
                                <span class="text-xs text-gray-500">${{ number_format($task->price_per_hour, 2) }}/h</span>
                            @elseif($task->price_per_day)
                                <span class="text-xs text-gray-500">${{ number_format($task->price_per_day, 2) }}/d</span>
                            @elseif($task->price_per_kg)
                                <span class="text-xs text-gray-500">${{ number_format($task->price_per_kg, 2) }}/kg</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap font-bold text-emerald-700">
                            ${{ number_format($task->calculated_payment ?? $task->total_payment ?? 0, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-right font-bold text-gray-700">Total Acumulado:</td>
                        <td class="px-6 py-4 text-right font-bold text-emerald-700 text-lg">
                            ${{ number_format($totalPayment ?? 0, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="py-12 flex flex-col items-center justify-center text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i data-lucide="clipboard-x" class="w-8 h-8 text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">Sin Actividad</h3>
            <p class="text-gray-500 text-sm max-w-sm">No se han registrado tareas aprobadas en este periodo.</p>
        </div>
        @endif
    </div>
</div>

<style media="print">
    @page {
        margin: 1.5cm;
    }
    body {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .print\:hidden {
        display: none !important;
    }
</style>
@endsection
