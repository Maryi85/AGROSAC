@extends('foreman.layout')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Dashboard Operativo</h2>
        <p class="text-sm text-gray-600 mt-1">Supervisión de campo diaria</p>
    </div>
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <i data-lucide="calendar" class="w-4 h-4"></i>
        <span>{{ now()->format('d/m/Y') }}</span>
    </div>
</div>
@endsection

@section('content')
<style>
    /* Estilos de Tarjetas estilo "Bento" adaptados del Admin */
    .kpi-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.85) 100%);
        border: 2px solid transparent;
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: 18px;
        padding: 2px;
        background: linear-gradient(135deg, var(--gradient-from), var(--gradient-to));
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        opacity: 0;
        transition: opacity 0.4s ease;
    }
    
    .kpi-card:hover::before {
        opacity: 1;
    }
    
    .kpi-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.15);
    }
    
    /* Variaciones de Color para KPIs Operativos */
    .kpi-card.workers {
        --gradient-from: #10b981; /* Emerald */
        --gradient-to: #059669;
        background: linear-gradient(135deg, #f0fdf9 0%, #ecfdf5 100%);
    }
    
    .kpi-card.tasks {
        --gradient-from: #f59e0b; /* Amber */
        --gradient-to: #d97706;
        background: linear-gradient(135deg, #fffef8 0%, #fffbf0 100%);
    }
    
    .kpi-card.tools {
        --gradient-from: #3b82f6; /* Blue */
        --gradient-to: #2563eb;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    }
    
    .kpi-card.alerts {
        --gradient-from: #ef4444; /* Red */
        --gradient-to: #dc2626;
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    }
    
    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    .kpi-icon::before {
        content: '';
        position: absolute;
        inset: -4px;
        border-radius: 20px;
        padding: 4px;
        background: linear-gradient(135deg, var(--icon-from), var(--icon-to));
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        opacity: 0.5;
    }
    
    .kpi-icon.workers-icon { --icon-from: #10b981; --icon-to: #059669; background: linear-gradient(135deg, #10b981, #059669); }
    .kpi-icon.tasks-icon { --icon-from: #f59e0b; --icon-to: #d97706; background: linear-gradient(135deg, #f59e0b, #d97706); }
    .kpi-icon.tools-icon { --icon-from: #3b82f6; --icon-to: #2563eb; background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .kpi-icon.alerts-icon { --icon-from: #ef4444; --icon-to: #dc2626; background: linear-gradient(135deg, #ef4444, #dc2626); }
    
    .pulse-dot {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .5; }
    }
</style>

<div class="space-y-6">
    {{-- Sección A: KPIs Superiores --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- KPI 1: Personal Presente --}}
        <div class="kpi-card workers p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="kpi-icon workers-icon">
                    <i data-lucide="users" class="w-6 h-6 text-white"></i>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full pulse-dot"></div>
                    <span class="text-xs font-medium text-emerald-700">En campo</span>
                </div>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-600 mb-1">Personal Presente</p>
                <p id="kpi-present-workers" class="text-2xl font-black text-emerald-700">{{ $presentWorkers }}</p>
                <p class="text-xs text-gray-500 mt-1">Trabajadores activos hoy</p>
            </div>
        </div>

        {{-- KPI 2: Tareas Pendientes --}}
        <div class="kpi-card tasks p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="kpi-icon tasks-icon">
                    <i data-lucide="clipboard-list" class="w-6 h-6 text-white"></i>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 bg-amber-500 rounded-full pulse-dot"></div>
                    <span class="text-xs font-medium text-amber-700">Por ejecutar</span>
                </div>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-600 mb-1">Tareas Asignadas</p>
                <p id="kpi-pending-tasks" class="text-2xl font-black text-amber-700">{{ $pendingTasks }}</p>
                <p class="text-xs text-gray-500 mt-1">Pendientes de cierre</p>
            </div>
        </div>

        {{-- KPI 3: Herramientas en Uso --}}
        <div class="kpi-card tools p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="kpi-icon tools-icon">
                    <i data-lucide="wrench" class="w-6 h-6 text-white"></i>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 bg-blue-500 rounded-full pulse-dot"></div>
                    <span class="text-xs font-medium text-blue-700">En uso</span>
                </div>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-600 mb-1">Herramientas Prestadas</p>
                <p id="kpi-tools-in-use" class="text-2xl font-black text-blue-700">{{ $toolsInUseCount }}</p>
                <p class="text-xs text-gray-500 mt-1">Fuera de almacén</p>
            </div>
        </div>

        {{-- KPI 4: Tareas Completadas --}}
        <div class="kpi-card alerts p-4" style="--gradient-from: #10b981; --gradient-to: #059669; background: linear-gradient(135deg, #f0fdf9 0%, #ecfdf5 100%);">
            <div class="flex items-start justify-between mb-3">
                <div class="kpi-icon" style="--icon-from: #10b981; --icon-to: #059669; background: linear-gradient(135deg, #10b981, #059669); border-radius: 12px; width: 48px; height: 48px; display: flex; align-items: center; justify-center: center;">
                    <i data-lucide="check-circle" class="w-6 h-6 text-white"></i>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                    <span class="text-xs font-medium text-emerald-700">Finalizadas</span>
                </div>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-600 mb-1">Tareas Completadas</p>
                <p id="kpi-completed-tasks" class="text-2xl font-black text-emerald-700">{{ $completedTasks }}</p>
                <p class="text-xs text-gray-500 mt-1">Histórico total</p>
            </div>
        </div>
    </div>

    {{-- Grid Principal: Gráfico e Insumos --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {{-- Gráfico de Rendimiento (60%) --}}
        <div class="lg:col-span-3 bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-100 rounded-lg">
                        <i data-lucide="bar-chart-3" class="w-5 h-5 text-indigo-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Rendimiento Semanal</h3>
                        <p class="text-xs text-gray-500">Tareas Asignadas vs Completadas</p>
                    </div>
                </div>
            </div>
            <div id="weeklyTasksChart" class="w-full h-[300px]"></div>
        </div>

        {{-- Insumos Críticos (40%) --}}
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <i data-lucide="package-alert" class="w-5 h-5 text-orange-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Insumos Críticos</h3>
                        <p class="text-xs text-gray-500">Stock bajo necesario hoy</p>
                    </div>
                </div>
                <a href="{{ route('foreman.supplies.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 underline">Ver inventario</a>
            </div>
            
            <div class="flex-1 overflow-y-auto pr-1 space-y-3 max-h-[300px]">
                @forelse($criticalSupplies as $supply)
                    <div class="flex items-center justify-between p-3 bg-orange-50 border border-orange-100 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-orange-200 flex items-center justify-center text-orange-700 text-xs font-bold">
                                {{ substr($supply->name, 0, 2) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $supply->name }}</p>
                                <p class="text-xs text-gray-500">Mínimo: {{ $supply->min_stock }} {{ $supply->unit }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-orange-600">{{ $supply->current_stock }}</p>
                            <p class="text-[10px] text-orange-800 uppercase font-bold">Bajo</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-gray-400">
                        <i data-lucide="check-circle-2" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                        <p class="text-sm">Todo el stock en orden</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Grid Inferior: Próximas Tareas --}}
    <div class="grid grid-cols-1 gap-6">
        {{-- Próximas Tareas --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-teal-100 rounded-lg">
                        <i data-lucide="calendar-clock" class="w-5 h-5 text-teal-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Próximas Tareas</h3>
                        <p class="text-xs text-gray-500">Planificación inmediata</p>
                    </div>
                </div>
                <a href="{{ route('foreman.tasks.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 underline">Gestionar</a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @forelse($upcomingTasks as $task)
                    <div class="group flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg border border-gray-100 hover:border-emerald-200 transition-all">
                        <div class="flex items-start gap-3">
                            <div class="mt-1">
                                <div class="w-2 h-2 rounded-full {{ $task->priority == 'high' ? 'bg-red-500' : 'bg-emerald-500' }}"></div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800 group-hover:text-emerald-700 transition-colors line-clamp-1">{{ $task->description }}</p>
                                <p class="text-xs text-gray-500">
                                    <i data-lucide="user" class="w-3 h-3 inline mr-0.5"></i> {{ $task->assignee->name ?? 'Sin asignar' }} 
                                    <span class="mx-1">•</span> 
                                    <i data-lucide="sprout" class="w-3 h-3 inline mr-0.5"></i> {{ $task->plot->name ?? 'Lote General' }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                             <span class="text-xs font-semibold px-2 py-1 rounded-full bg-gray-100 text-gray-600">
                                {{ $task->scheduled_for ? \Carbon\Carbon::parse($task->scheduled_for)->format('d/m') : 'S/F' }}
                             </span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-8 text-gray-400">
                        <p class="text-sm">No hay tareas pendientes próximas</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- ApexCharts CDN --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>



{{-- Script para ApexCharts --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var options = {
            series: [{
                name: 'Completadas',
                data: @json($weeklyPerformance['completed'])
            }, {
                name: 'Asignadas',
                data: @json($weeklyPerformance['assigned'])
            }],
            chart: {
                type: 'bar',
                height: 300,
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded',
                    borderRadius: 4
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: @json($weeklyPerformance['dates']),
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                title: {
                    text: 'Tareas'
                }
            },
            fill: {
                opacity: 1
            },
            colors: ['#10b981', '#cbd5e1'], // Emerald para completado, Gray para asignado
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " tareas"
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right'
            }
        };

        var chart = new ApexCharts(document.querySelector("#weeklyTasksChart"), options);
        chart.render();

        // Polling para actualización en tiempo real de KPIs
        function refreshDashboardStats() {
            fetch('{{ route('foreman.data') }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const stats = data.stats;
                        
                        if (document.getElementById('kpi-present-workers')) 
                            document.getElementById('kpi-present-workers').textContent = stats.presentWorkers;
                        
                        if (document.getElementById('kpi-pending-tasks')) 
                            document.getElementById('kpi-pending-tasks').textContent = stats.pendingTasks;
                        
                        if (document.getElementById('kpi-tools-in-use')) 
                            document.getElementById('kpi-tools-in-use').textContent = stats.toolsInUse;
                        
                        if (document.getElementById('kpi-completed-tasks')) 
                            document.getElementById('kpi-completed-tasks').textContent = stats.completedTasks;
                    }
                })
                .catch(error => console.error('Error al actualizar stats:', error));
        }

        setInterval(refreshDashboardStats, 5000);
    });
</script>
@endpush
