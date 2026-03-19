@extends('worker.layout')

@section('header')
<div>
    <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
    <p class="text-sm text-gray-600 mt-1">Panel de control personal</p>
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
                <p id="pending-tasks-count" class="text-2xl font-black text-amber-700">{{ $pendingTasks }}</p>
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
                <p id="completed-tasks-count" class="text-2xl font-black text-emerald-700">{{ $completedTasks }}</p>
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
                <p id="active-loans-count" class="text-2xl font-black text-blue-700">{{ $activeLoans }}</p>
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
                <p id="compliance-percentage" class="text-2xl font-black text-purple-700">
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
            
            <div id="my-loans-container" class="flex-1 overflow-y-auto pr-1 space-y-3 max-h-[300px]">
                @forelse($myLoans as $loan)
                    <div class="flex items-center justify-between p-3 bg-blue-50 border border-blue-100 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-200 flex items-center justify-center text-blue-700">
                                <i data-lucide="wrench" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $loan->tool->name }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $loan->quantity }} unidad(es) • {{ ($loan->out_at ?? $loan->created_at)->format('d/m') }}
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

    {{-- Grid Inferior: Próximas Tareas y Actividad Reciente --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
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
            
            <div id="upcoming-tasks-container" class="space-y-3">
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
                    <div class="text-center py-8 text-gray-400">
                        <p class="text-sm">No tienes tareas pendientes próximas</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Actividad Reciente (Evaluaciones) --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-100 rounded-lg">
                        <i data-lucide="history" class="w-5 h-5 text-amber-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Actividad Reciente</h3>
                        <p class="text-xs text-gray-500">Tareas finalizadas y evaluadas</p>
                    </div>
                </div>
                <a href="{{ route('worker.reports') }}" class="text-xs text-amber-600 hover:text-amber-700 underline">Mi historial</a>
            </div>
            
            <div id="recent-evaluations-container" class="space-y-3">
                @forelse($recentEvaluations as $task)
                    @php
                        $statusMap = [
                            'completed' => ['label' => 'Completada', 'class' => 'bg-emerald-100 text-emerald-700', 'icon' => 'check-circle'],
                            'approved'  => ['label' => 'Aprobada',   'class' => 'bg-blue-100 text-blue-700',     'icon' => 'check-double'],
                            'rejected'  => ['label' => 'Rechazada',  'class' => 'bg-red-100 text-red-700',       'icon' => 'x-circle'],
                        ];
                        $s = $statusMap[$task->status] ?? ['label' => $task->status, 'class' => 'bg-gray-100 text-gray-700', 'icon' => 'circle'];
                    @endphp
                    <div class="flex items-center justify-between p-3 bg-gray-50/50 rounded-lg border border-gray-100">
                        <div class="flex-1 min-w-0 pr-4">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $task->description }}</p>
                            <p class="text-[10px] text-gray-500 mt-0.5">
                                {{ $task->updated_at ? $task->updated_at->format('d/m H:i') : '—' }}
                            </p>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $s['class'] }}">
                            <i data-lucide="{{ $s['icon'] }}" class="w-3 h-3"></i>
                            {{ $s['label'] }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400">
                        <p class="text-sm">No hay actividad reciente</p>
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
                id: 'performanceChart',
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

        // Función para actualizar datos en tiempo real
        function refreshDashboardData() {
            fetch('{{ route("worker.dashboard.data") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // 1. Actualizar KPIs
                        document.getElementById('pending-tasks-count').innerText = data.stats.pendingTasks;
                        document.getElementById('completed-tasks-count').innerText = data.stats.completedTasks;
                        document.getElementById('active-loans-count').innerText = data.stats.activeLoans;
                        document.getElementById('compliance-percentage').innerText = data.stats.compliance + '%';

                        // 2. Actualizar Gráfico
                        ApexCharts.exec('performanceChart', 'updateSeries', [
                            { name: 'Completadas', data: data.weeklyPerformance.completed },
                            { name: 'Asignadas', data: data.weeklyPerformance.assigned }
                        ]);

                        // 3. Actualizar Listas
                        updateUpcomingTasks(data.myPendingTasks);
                        updateRecentEvaluations(data.recentEvaluations);
                        updateMyLoans(data.myLoans);
                    }
                })
                .catch(error => console.error('Error al actualizar el dashboard:', error));
        }

        function updateMyLoans(loans) {
            const container = document.getElementById('my-loans-container');
            if (!container) return;

            if (loans.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-6 text-gray-400">
                        <i data-lucide="check-circle-2" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                        <p class="text-sm">No tienes herramientas prestadas</p>
                    </div>
                `;
                if (window.lucide) window.lucide.createIcons();
                return;
            }

            container.innerHTML = loans.map(loan => `
                <div class="flex items-center justify-between p-3 bg-blue-50 border border-blue-100 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-200 flex items-center justify-center text-blue-700">
                            <i data-lucide="wrench" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">${loan.tool_name}</p>
                            <p class="text-xs text-gray-500">
                                ${loan.quantity} unidad(es) • ${loan.created_at}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-bold text-blue-600 bg-white px-2 py-1 rounded-full border border-gray-200">
                            Activo
                        </span>
                    </div>
                </div>
            `).join('');
            
            if (window.lucide) window.lucide.createIcons();
        }

        function updateUpcomingTasks(tasks) {
            const container = document.getElementById('upcoming-tasks-container');
            if (tasks.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-gray-400">
                        <p class="text-sm">No tienes tareas pendientes próximas</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = tasks.map(task => `
                <div class="group flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg border border-gray-100 hover:border-emerald-200 transition-all">
                    <div class="flex items-start gap-3">
                        <div class="mt-1">
                            <div class="w-2 h-2 rounded-full ${task.priority === 'high' ? 'bg-red-500' : 'bg-emerald-500'}"></div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800 group-hover:text-emerald-700 transition-colors line-clamp-1">${task.description}</p>
                            <p class="text-xs text-gray-500">
                                <i data-lucide="sprout" class="w-3 h-3 inline mr-0.5"></i> ${task.plot_name}
                                <span class="mx-1">•</span>
                                <i data-lucide="leaf" class="w-3 h-3 inline mr-0.5"></i> ${task.crop_name}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                         <span class="text-xs font-semibold px-2 py-1 rounded-full bg-gray-100 text-gray-600">
                            ${task.scheduled_for}
                         </span>
                    </div>
                </div>
            `).join('');
        }

        function updateRecentEvaluations(evaluations) {
            const container = document.getElementById('recent-evaluations-container');
            if (evaluations.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-gray-400">
                        <p class="text-sm">No hay actividad reciente</p>
                    </div>
                `;
                return;
            }

            const iconsMap = {
                'completed': 'check-circle',
                'approved': 'check-double',
                'rejected': 'x-circle'
            };

            const bgMap = {
                'emerald': 'bg-emerald-100 text-emerald-700',
                'blue': 'bg-blue-100 text-blue-700',
                'red': 'bg-red-100 text-red-700'
            };

            container.innerHTML = evaluations.map(task => `
                <div class="flex items-center justify-between p-3 bg-gray-50/50 rounded-lg border border-gray-100">
                    <div class="flex-1 min-w-0 pr-4">
                        <p class="text-sm font-medium text-gray-800 truncate">${task.description}</p>
                        <p class="text-[10px] text-gray-500 mt-0.5">${task.updated_at}</p>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider ${bgMap[task.status_color] || 'bg-gray-100 text-gray-700'}">
                        <i data-lucide="${iconsMap[task.status] || 'circle'}" class="w-3 h-3"></i>
                        ${task.status_label}
                    </span>
                </div>
            `).join('');

            // Reinicializar iconos de Lucide
            if (window.lucide) {
                window.lucide.createIcons();
            }
        }

        // Actualizar cada 3 segundos para respuesta "instantánea"
        setInterval(refreshDashboardData, 3000);
    });
</script>
@endpush
