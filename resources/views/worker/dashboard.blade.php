@extends('worker.layout')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Dashboard Trabajador</h2>
        <p class="text-sm text-gray-600 mt-1">Panel de trabajo agrícola</p>
    </div>
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <i data-lucide="calendar" class="w-4 h-4"></i>
        <span>{{ now()->format('d/m/Y') }}</span>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-8">
    <style>
        .worker-card {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            border: 1px solid rgba(16, 185, 129, 0.1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }
        
        .worker-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .metric-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(16, 185, 129, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #10b981, #059669, #047857);
        }
        
        .metric-icon {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 4px 14px 0 rgba(16, 185, 129, 0.3);
        }
        
        .task-card {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            border: 1px solid rgba(16, 185, 129, 0.15);
            position: relative;
        }
        
        .task-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #10b981, #059669);
        }
        
        .task-item {
            transition: all 0.2s ease;
            border-radius: 12px;
            padding: 16px;
        }
        
        .task-item:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
        }
        
        .status-badge {
            font-weight: 600;
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .status-pending {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #92400e;
            border: 1px solid #fde68a;
        }
        
        .status-completed {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        
        .status-loaned {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }
        
        .empty-state {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
        }
    </style>
    <!-- Welcome Section -->
    <div class="worker-card rounded-xl p-6">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl">
                <i data-lucide="user" class="w-6 h-6 text-white"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Bienvenido, {{ auth()->user()->name ?? 'Trabajador' }}</h3>
                <p class="text-sm text-gray-600">Panel de trabajo agrícola</p>
            </div>
        </div>
    </div>

    <!-- Main Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Tareas Pendientes -->
        <div class="metric-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Tareas Pendientes</p>
                    <p class="text-3xl font-bold text-emerald-600 mb-2">{{ $pendingTasks }}</p>
                    <p class="text-xs text-gray-500">📋 Total: {{ $totalTasks }}</p>
                </div>
                <div class="p-4 metric-icon rounded-2xl">
                    <i data-lucide="clock" class="w-7 h-7 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Tareas Completadas -->
        <div class="metric-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Tareas Completadas</p>
                    <p class="text-3xl font-bold text-emerald-600 mb-2">{{ $completedTasks }}</p>
                    <p class="text-xs text-gray-500">✅ Este período</p>
                </div>
                <div class="p-4 metric-icon rounded-2xl">
                    <i data-lucide="check-circle" class="w-7 h-7 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Herramientas Prestadas -->
        <div class="metric-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Herramientas Prestadas</p>
                    <p class="text-3xl font-bold text-emerald-600 mb-2">{{ $activeLoans }}</p>
                    <p class="text-xs text-gray-500">🔧 Activas</p>
                </div>
                <div class="p-4 metric-icon rounded-2xl">
                    <i data-lucide="wrench" class="w-7 h-7 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Progreso General -->
        <div class="metric-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Progreso</p>
                    <p class="text-3xl font-bold text-emerald-600 mb-2">
                        {{ $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0 }}%
                    </p>
                    <p class="text-xs text-gray-500">📈 Completado</p>
                </div>
                <div class="p-4 metric-icon rounded-2xl">
                    <i data-lucide="trending-up" class="w-7 h-7 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Tareas Pendientes -->
        <div class="task-card rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 rounded-lg">
                        <i data-lucide="clock" class="w-5 h-5 text-emerald-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Tareas Pendientes</h3>
                </div>
                <a href="{{ route('worker.tasks') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-emerald-600 hover:text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors">
                    <i data-lucide="external-link" class="w-4 h-4"></i>
                    Ver todas
                </a>
            </div>
            @if($myPendingTasks->count() > 0)
                <div class="space-y-4">
                    @foreach($myPendingTasks as $task)
                        <div class="task-item flex items-center justify-between bg-gradient-to-r from-yellow-50 to-yellow-100">
                            <div class="flex-1">
                                <div class="text-sm font-semibold text-gray-800 mb-1">{{ $task->description }}</div>
                                <div class="text-xs text-gray-600 mb-1">
                                    {{ $task->plot->name ?? 'Sin lote' }} • 
                                    {{ $task->crop->name ?? 'Sin cultivo' }}
                                </div>
                                @if($task->scheduled_for)
                                    <div class="text-xs text-yellow-600">
                                        📅 Programada: {{ $task->scheduled_for->format('d/m/Y') }}
                                    </div>
                                @endif
                            </div>
                            <span class="status-badge status-pending">
                                Pendiente
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state text-center py-8">
                    <i data-lucide="clock" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                    <p class="text-gray-500 font-medium">No tienes tareas pendientes</p>
                    <p class="text-sm text-gray-400 mt-1">Las tareas asignadas aparecerán aquí</p>
                </div>
            @endif
        </div>

        <!-- Tareas Completadas Recientes -->
        <div class="task-card rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 rounded-lg">
                        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Tareas Completadas</h3>
                </div>
                <a href="{{ route('worker.reports') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-emerald-600 hover:text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors">
                    <i data-lucide="external-link" class="w-4 h-4"></i>
                    Ver reportes
                </a>
            </div>
            @if($recentCompletedTasks->count() > 0)
                <div class="space-y-4">
                    @foreach($recentCompletedTasks as $task)
                        <div class="task-item flex items-center justify-between bg-gradient-to-r from-green-50 to-green-100">
                            <div class="flex-1">
                                <div class="text-sm font-semibold text-gray-800 mb-1">{{ $task->description }}</div>
                                <div class="text-xs text-gray-600 mb-1">
                                    {{ $task->plot->name ?? 'Sin lote' }} • 
                                    {{ $task->crop->name ?? 'Sin cultivo' }}
                                </div>
                                @if($task->updated_at)
                                    <div class="text-xs text-green-600">
                                        ✅ Completada: {{ $task->updated_at->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                            </div>
                            <span class="status-badge status-completed">
                                Completada
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state text-center py-8">
                    <i data-lucide="check-circle" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                    <p class="text-gray-500 font-medium">No hay tareas completadas</p>
                    <p class="text-sm text-gray-400 mt-1">Las tareas completadas aparecerán aquí</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Herramientas Prestadas -->
    <div class="task-card rounded-xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-emerald-100 rounded-lg">
                    <i data-lucide="wrench" class="w-5 h-5 text-emerald-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800">Herramientas Prestadas</h3>
            </div>
            <a href="{{ route('worker.tools') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-emerald-600 hover:text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors">
                <i data-lucide="external-link" class="w-4 h-4"></i>
                Gestionar herramientas
            </a>
        </div>
        @if($myLoans->count() > 0)
            <div class="space-y-4">
                @foreach($myLoans as $loan)
                    <div class="task-item flex items-center justify-between bg-gradient-to-r from-blue-50 to-blue-100">
                        <div class="flex-1">
                            <div class="text-sm font-semibold text-gray-800 mb-1">{{ $loan->tool->name }}</div>
                            <div class="text-xs text-gray-600 mb-1">
                                📅 Prestada: {{ $loan->out_at->format('d/m/Y') }}
                            </div>
                            @if($loan->due_at)
                                <div class="text-xs text-blue-600">⏰ Vence: {{ $loan->due_at->format('d/m/Y') }}</div>
                            @endif
                        </div>
                        <span class="status-badge 
                            {{ $loan->status === 'out' ? 'status-loaned' : 
                               ($loan->status === 'returned' ? 'status-completed' : 'status-pending') }}">
                            {{ $loan->status === 'out' ? 'Prestada' : ucfirst($loan->status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state text-center py-8">
                <i data-lucide="wrench" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                <p class="text-gray-500 font-medium">No tienes herramientas prestadas</p>
                <p class="text-sm text-gray-400 mt-1">Las herramientas prestadas aparecerán aquí</p>
            </div>
        @endif
    </div>
</div>
@endsection
