@extends('foreman.layout')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Dashboard Mayordomo</h2>
        <p class="text-sm text-gray-600 mt-1">Panel de control para supervisión agrícola</p>
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
        .foreman-card {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            border: 1px solid rgba(16, 185, 129, 0.1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }
        
        .foreman-card:hover {
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
        
        .metric-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px -5px rgba(16, 185, 129, 0.2);
        }
        
        .metric-icon {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 4px 14px 0 rgba(16, 185, 129, 0.3);
            transition: all 0.3s ease;
        }
        
        .metric-card:hover .metric-icon {
            transform: scale(1.1);
            box-shadow: 0 6px 20px 0 rgba(16, 185, 129, 0.4);
        }
        
        .action-card {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            border: 1px solid rgba(16, 185, 129, 0.15);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(16, 185, 129, 0.1), transparent);
            transition: left 0.5s ease;
        }
        
        .action-card:hover::before {
            left: 100%;
        }
        
        .action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px -5px rgba(16, 185, 129, 0.2);
            border-color: rgba(16, 185, 129, 0.3);
        }
        
        .activity-card {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            border: 1px solid rgba(16, 185, 129, 0.15);
            position: relative;
        }
        
        .activity-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #10b981, #059669);
        }
        
        .activity-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px -3px rgba(16, 185, 129, 0.15);
        }
        
        .status-badge {
            font-weight: 600;
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: all 0.2s ease;
        }
        
        .status-active {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: #166534;
            border: 1px solid #bbf7d0;
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
        
        .status-inactive {
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
            color: #374151;
            border: 1px solid #e5e7eb;
        }
        
        .empty-state {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
        }
        
        .progress-ring {
            transform: rotate(-90deg);
        }
        
        .progress-ring-circle {
            stroke-dasharray: 251.2;
            stroke-dashoffset: 251.2;
            transition: stroke-dashoffset 0.5s ease-in-out;
        }
        
        .floating-animation {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #10b981, #059669);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
    <!-- Welcome Section -->
    <div class="foreman-card rounded-xl p-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-full -translate-y-16 translate-x-16 opacity-50"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-emerald-100 to-emerald-200 rounded-full translate-y-12 -translate-x-12 opacity-30"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="p-4 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl floating-animation">
                        <i data-lucide="user-check" class="w-7 h-7 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold gradient-text">Bienvenido, {{ auth()->user()->name ?? 'Mayordomo' }}</h3>
                        <p class="text-sm text-gray-600 mt-1">Panel de supervisión agrícola</p>
                        <div class="flex items-center gap-4 mt-3">
                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                <i data-lucide="calendar" class="w-4 h-4"></i>
                                <span>{{ now()->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                <i data-lucide="clock" class="w-4 h-4"></i>
                                <span>{{ now()->format('H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="text-right">
                        <div class="text-sm text-gray-500 mb-1">Estado del Sistema</div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-green-500 rounded-full pulse-animation"></div>
                            <span class="text-sm font-medium text-green-600">Operativo</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Statistics Cards - Enfocados en responsabilidades del mayordomo -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Trabajadores Activos -->
        <div class="metric-card rounded-xl p-6 group">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <p class="text-sm font-semibold text-gray-600">Trabajadores Activos</p>
                        <div class="w-2 h-2 bg-green-500 rounded-full pulse-animation"></div>
                    </div>
                    <p class="text-4xl font-bold text-emerald-600 mb-2 group-hover:scale-105 transition-transform">{{ $activeWorkers }}</p>
            <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-500">👥 Equipo de trabajo</p>
                        <div class="text-xs text-emerald-600 font-medium">+12% vs mes anterior</div>
                    </div>
                </div>
                <div class="p-4 metric-icon rounded-2xl group-hover:scale-110 transition-transform">
                    <i data-lucide="users" class="w-7 h-7 text-white"></i>
                </div>
            </div>
            <div class="mt-4 bg-emerald-50 rounded-lg p-3">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-600">Productividad</span>
                    <span class="font-semibold text-emerald-600">85%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-emerald-500 h-2 rounded-full" style="width: 85%"></div>
                </div>
            </div>
        </div>

        <!-- Tareas Pendientes -->
        <div class="metric-card rounded-xl p-6 group">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <p class="text-sm font-semibold text-gray-600">Tareas Pendientes</p>
                        <div class="w-2 h-2 bg-yellow-500 rounded-full pulse-animation"></div>
                    </div>
                    <p class="text-4xl font-bold text-emerald-600 mb-2 group-hover:scale-105 transition-transform">{{ $pendingTasks }}</p>
            <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-500">✅ Completadas: {{ $completedTasks }}</p>
                        <div class="text-xs text-emerald-600 font-medium">{{ $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0 }}% completado</div>
                    </div>
                </div>
                <div class="p-4 metric-icon rounded-2xl group-hover:scale-110 transition-transform">
                    <i data-lucide="clipboard-check" class="w-7 h-7 text-white"></i>
                </div>
            </div>
            <div class="mt-4 bg-yellow-50 rounded-lg p-3">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-600">Progreso</span>
                    <span class="font-semibold text-yellow-600">{{ $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0 }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>

        <!-- Tareas por Aprobar -->
        <div class="metric-card rounded-xl p-6 group">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <p class="text-sm font-semibold text-gray-600">Por Aprobar</p>
                        <div class="w-2 h-2 bg-blue-500 rounded-full pulse-animation"></div>
                    </div>
                    <p class="text-4xl font-bold text-emerald-600 mb-2 group-hover:scale-105 transition-transform">{{ $tasksToApprove }}</p>
            <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-500">⏳ Esperando validación</p>
                        <div class="text-xs text-blue-600 font-medium">Requiere atención</div>
                    </div>
                </div>
                <div class="p-4 metric-icon rounded-2xl group-hover:scale-110 transition-transform">
                    <i data-lucide="check-circle" class="w-7 h-7 text-white"></i>
                </div>
            </div>
            <div class="mt-4 bg-blue-50 rounded-lg p-3">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-600">Tiempo promedio</span>
                    <span class="font-semibold text-blue-600">2.5 días</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-blue-500 h-2 rounded-full" style="width: 75%"></div>
                </div>
            </div>
        </div>

        <!-- Herramientas Disponibles -->
        <div class="metric-card rounded-xl p-6 group">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <p class="text-sm font-semibold text-gray-600">Herramientas Disponibles</p>
                        <div class="w-2 h-2 bg-purple-500 rounded-full pulse-animation"></div>
                    </div>
                    <p class="text-4xl font-bold text-emerald-600 mb-2 group-hover:scale-105 transition-transform">{{ $availableTools }}</p>
            <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-500">🔧 Total: {{ $totalTools }}</p>
                        <div class="text-xs text-purple-600 font-medium">{{ $totalTools > 0 ? round(($availableTools / $totalTools) * 100) : 0 }}% disponibles</div>
                    </div>
                </div>
                <div class="p-4 metric-icon rounded-2xl group-hover:scale-110 transition-transform">
                    <i data-lucide="wrench" class="w-7 h-7 text-white"></i>
                </div>
            </div>
            <div class="mt-4 bg-purple-50 rounded-lg p-3">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-600">Disponibilidad</span>
                    <span class="font-semibold text-purple-600">{{ $totalTools > 0 ? round(($availableTools / $totalTools) * 100) : 0 }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $totalTools > 0 ? ($availableTools / $totalTools) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions - Solo las funcionalidades del mayordomo -->
    <div class="foreman-card rounded-xl p-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-bl from-emerald-100 to-emerald-200 rounded-full -translate-y-20 translate-x-20 opacity-30"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl floating-animation">
                        <i data-lucide="zap" class="w-6 h-6 text-white"></i>
                    </div>
                <div>
                        <h3 class="text-2xl font-bold gradient-text">Acciones Rápidas</h3>
                        <p class="text-sm text-gray-600 mt-1">Gestiona las operaciones agrícolas</p>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="text-right">
                        <div class="text-sm text-gray-500 mb-1">Acceso Directo</div>
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                            <span class="text-sm font-medium text-emerald-600">3 módulos</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ route('foreman.tasks.index') }}" class="action-card flex items-center p-6 rounded-xl transition-all duration-300 group">
                    <div class="p-4 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl mr-4 group-hover:scale-110 transition-transform">
                        <i data-lucide="clipboard-list" class="w-6 h-6 text-white"></i>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-gray-800 mb-1 group-hover:text-emerald-600 transition-colors">Gestionar Tareas</div>
                        <div class="text-sm text-gray-600 mb-2">Crear, editar y aprobar tareas</div>
                        <div class="flex items-center gap-2 text-xs text-emerald-600">
                            <i data-lucide="arrow-right" class="w-3 h-3"></i>
                            <span>Acceder al módulo</span>
                        </div>
                </div>
            </a>
            
                <a href="{{ route('foreman.tools.index') }}" class="action-card flex items-center p-6 rounded-xl transition-all duration-300 group">
                    <div class="p-4 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl mr-4 group-hover:scale-110 transition-transform">
                        <i data-lucide="wrench" class="w-6 h-6 text-white"></i>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-gray-800 mb-1 group-hover:text-blue-600 transition-colors">Inventario de Herramientas</div>
                        <div class="text-sm text-gray-600 mb-2">Registrar y gestionar herramientas</div>
                        <div class="flex items-center gap-2 text-xs text-blue-600">
                            <i data-lucide="arrow-right" class="w-3 h-3"></i>
                            <span>Acceder al módulo</span>
                        </div>
                </div>
            </a>
            
                <a href="{{ route('foreman.workers.index') }}" class="action-card flex items-center p-6 rounded-xl transition-all duration-300 group">
                    <div class="p-4 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl mr-4 group-hover:scale-110 transition-transform">
                        <i data-lucide="users" class="w-6 h-6 text-white"></i>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-gray-800 mb-1 group-hover:text-purple-600 transition-colors">Gestionar Trabajadores</div>
                        <div class="text-sm text-gray-600 mb-2">Ver, editar y desactivar trabajadores</div>
                        <div class="flex items-center gap-2 text-xs text-purple-600">
                            <i data-lucide="arrow-right" class="w-3 h-3"></i>
                            <span>Acceder al módulo</span>
                        </div>
                </div>
            </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity - Solo información relevante para el mayordomo -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Tareas Recientes -->
        <div class="activity-card rounded-xl p-6 group">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl group-hover:scale-110 transition-transform">
                        <i data-lucide="clipboard-check" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Tareas Recientes</h3>
                        <p class="text-sm text-gray-600">Últimas actividades del equipo</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500 mb-1">Actualizado</div>
                    <div class="text-xs font-medium text-emerald-600">{{ now()->format('H:i') }}</div>
                </div>
            </div>
            @if($recentTasks->count() > 0)
                <div class="space-y-4">
                    @foreach($recentTasks as $task)
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-emerald-50 to-emerald-100 rounded-xl transition-all duration-200 hover:shadow-md">
                            <div>
                                <div class="text-sm font-semibold text-gray-800 mb-1">{{ $task->description }}</div>
                                <div class="text-xs text-gray-600">
                                    {{ $task->plot->name ?? 'Sin lote' }} • 
                                    {{ $task->assignee->name ?? 'Sin asignar' }}
                                </div>
                            </div>
                            <span class="status-badge 
                                {{ $task->status === 'completed' ? 'status-completed' : 
                                   ($task->status === 'pending' ? 'status-pending' : 
                                   ($task->status === 'approved' ? 'status-active' : 'status-inactive')) }}">
                                {{ ucfirst($task->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state text-center py-8">
                    <i data-lucide="clipboard-check" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                    <p class="text-gray-500 font-medium">No hay tareas recientes</p>
                    <p class="text-sm text-gray-400 mt-1">Las tareas aparecerán aquí cuando se asignen</p>
                </div>
            @endif
        </div>

        <!-- Herramientas en Uso -->
        <div class="activity-card rounded-xl p-6 group">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl group-hover:scale-110 transition-transform">
                        <i data-lucide="wrench" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Herramientas en Uso</h3>
                        <p class="text-sm text-gray-600">Estado del inventario</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500 mb-1">Inventario</div>
                    <div class="text-xs font-medium text-blue-600">{{ $toolsInUse->count() }} activas</div>
                </div>
            </div>
            @if($toolsInUse->count() > 0)
                <div class="space-y-4">
                    @foreach($toolsInUse as $tool)
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-emerald-50 to-emerald-100 rounded-xl transition-all duration-200 hover:shadow-md">
                            <div>
                                <div class="text-sm font-semibold text-gray-800 mb-1">{{ $tool->name }}</div>
                                <div class="text-xs text-gray-600">
                                    {{ $tool->category ?? 'Sin categoría' }} • 
                                    {{ $tool->available_qty }}/{{ $tool->total_qty }} disponibles
                                </div>
                            </div>
                            <span class="status-badge 
                                {{ $tool->status === 'operational' ? 'status-active' : 
                                   ($tool->status === 'damaged' ? 'status-pending' : 'status-inactive') }}">
                                {{ ucfirst($tool->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state text-center py-8">
                    <i data-lucide="wrench" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                    <p class="text-gray-500 font-medium">No hay herramientas en uso</p>
                    <p class="text-sm text-gray-400 mt-1">Las herramientas aparecerán aquí cuando se usen</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
