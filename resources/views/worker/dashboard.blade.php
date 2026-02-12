@extends('worker.layout')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
        <p class="text-sm text-gray-600 mt-1">Panel de control personal</p>
    </div>
    <div class="flex items-center gap-2 text-sm text-gray-500 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm">
        <i data-lucide="calendar" class="w-4 h-4"></i>
        <span>{{ now()->format('d/m/Y') }}</span>
    </div>
</div>
@endsection

@section('content')
<style>
    .kpi-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
    }
    /* Worker specific colors */
    .kpi-card.pending { border-bottom: 3px solid #f59e0b; }
    .kpi-card.completed { border-bottom: 3px solid #10b981; }
    .kpi-card.tools { border-bottom: 3px solid #3b82f6; }
    .kpi-card.progress { border-bottom: 3px solid #8b5cf6; }
    
    .kpi-icon.pending-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .kpi-icon.completed-icon { background: linear-gradient(135deg, #10b981, #059669); }
    .kpi-icon.tools-icon { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .kpi-icon.progress-icon { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
    
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
        {{-- KPI 1: Tareas Pendientes --}}
        <div class="kpi-card pending p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="kpi-icon pending-icon">
                    <i data-lucide="clock" class="w-6 h-6 text-white"></i>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 bg-amber-500 rounded-full pulse-dot"></div>
                    <span class="text-xs font-medium text-amber-700">Por hacer</span>
                </div>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-600 mb-1">Tareas Pendientes</p>
                <p class="text-2xl font-black text-amber-700">{{ $pendingTasks }}</p>
                <p class="text-xs text-gray-500 mt-1">Asignadas a ti</p>
            </div>
        </div>

        {{-- KPI 2: Tareas Completadas --}}
        <div class="kpi-card completed p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="kpi-icon completed-icon">
                    <i data-lucide="check-circle" class="w-6 h-6 text-white"></i>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full pulse-dot"></div>
                    <span class="text-xs font-medium text-emerald-700">Completado</span>
                </div>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-600 mb-1">Tareas Finalizadas</p>
                <p class="text-2xl font-black text-emerald-700">{{ $completedTasks }}</p>
                <p class="text-xs text-gray-500 mt-1">Total histórico</p>
            </div>
        </div>

        {{-- KPI 3: Herramientas Prestadas --}}
        <div class="kpi-card tools p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="kpi-icon tools-icon">
                    <i data-lucide="wrench" class="w-6 h-6 text-white"></i>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 bg-blue-500 rounded-full pulse-dot"></div>
                    <span class="text-xs font-medium text-blue-700">En tu poder</span>
                </div>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-600 mb-1">Herramientas</p>
                <p class="text-2xl font-black text-blue-700">{{ $activeLoans }}</p>
                <p class="text-xs text-gray-500 mt-1">Préstamos activos</p>
            </div>
        </div>

        {{-- KPI 4: Progreso General --}}
        <div class="kpi-card progress p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="kpi-icon progress-icon">
                    <i data-lucide="trending-up" class="w-6 h-6 text-white"></i>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 bg-purple-500 rounded-full pulse-dot"></div>
                    <span class="text-xs font-medium text-purple-700">Efectividad</span>
                </div>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-600 mb-1">Cumplimiento</p>
                <p class="text-2xl font-black text-purple-700">
                    {{ $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0 }}%
                </p>
                <p class="text-xs text-gray-500 mt-1">Tareas completadas</p>
            </div>
        </div>
    </div>

    {{-- Grid Principal: Gráfico y Herramientas --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {{-- Gráfico de Rendimiento (60%) --}}
        <div class="lg:col-span-3 bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-100 rounded-lg">
                        <i data-lucide="bar-chart-3" class="w-5 h-5 text-indigo-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Mi Rendimiento Semanal</h3>
                        <p class="text-xs text-gray-500">Mis Tareas Asignadas vs Completadas</p>
                    </div>
                </div>
            </div>
            <div id="weeklyTasksChart" class="w-full h-[300px]"></div>
        </div>

        {{-- Herramientas en Poder (40%) --}}
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i data-lucide="briefcase" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Mis Herramientas</h3>
                        <p class="text-xs text-gray-500">Préstamos activos</p>
                    </div>
                </div>
                <a href="{{ route('worker.tools') }}" class="text-xs text-emerald-600 hover:text-emerald-700 underline">Gestionar</a>
            </div>
            
            <div class="flex-1 overflow-y-auto pr-1 space-y-3 max-h-[300px]">
                @forelse($myLoans as $loan)
                    <div class="flex items-center justify-between p-3 bg-blue-50 border border-blue-100 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-200 flex items-center justify-center text-blue-700">
                                <i data-lucide="wrench" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $loan->tool->name }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $loan->quantity }} unidad(es) • {{ $loan->out_at->format('d/m') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            @if($loan->due_at)
                                <span class="text-[10px] font-bold {{ $loan->due_at->isPast() ? 'text-red-600' : 'text-blue-600' }} bg-white px-2 py-1 rounded-full border border-gray-200">
                                    Vence: {{ $loan->due_at->format('d/m') }}
                                </span>
                            @else
                                <span class="text-[10px] font-bold text-gray-500 bg-white px-2 py-1 rounded-full border border-gray-200">
                                    Indefinido
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-gray-400">
                        <i data-lucide="check-circle-2" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                        <p class="text-sm">No tienes herramientas prestadas</p>
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
                        <h3 class="text-lg font-bold text-gray-800">Mis Próximas Tareas</h3>
                        <p class="text-xs text-gray-500">Planificación personal</p>
                    </div>
                </div>
                <a href="{{ route('worker.tasks') }}" class="text-xs text-emerald-600 hover:text-emerald-700 underline">Ver todas</a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @forelse($myPendingTasks as $task)
                    <div class="group flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg border border-gray-100 hover:border-emerald-200 transition-all">
                        <div class="flex items-start gap-3">
                            <div class="mt-1">
                                <div class="w-2 h-2 rounded-full {{ $task->priority == 'high' ? 'bg-red-500' : 'bg-emerald-500' }}"></div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800 group-hover:text-emerald-700 transition-colors line-clamp-1">{{ $task->description }}</p>
                                <p class="text-xs text-gray-500">
                                    <i data-lucide="sprout" class="w-3 h-3 inline mr-0.5"></i> {{ $task->plot->name ?? 'Lote General' }}
                                    <span class="mx-1">•</span>
                                    <i data-lucide="leaf" class="w-3 h-3 inline mr-0.5"></i> {{ $task->crop->name ?? 'Sin cultivo' }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                             <span class="text-xs font-semibold px-2 py-1 rounded-full bg-gray-100 text-gray-600">
                                {{ $task->scheduled_for ? $task->scheduled_for->format('d/m') : 'S/F' }}
                             </span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-8 text-gray-400">
                        <p class="text-sm">No tienes tareas pendientes próximas</p>
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
            colors: ['#10b981', '#6366f1'], // Emerald for completed, Indigo for assigned
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " Tareas"
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#weeklyTasksChart"), options);
        chart.render();
    });
</script>
@endpush
