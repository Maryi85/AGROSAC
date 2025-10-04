@extends('foreman.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Dashboard Mayordomo</h2>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-white border rounded p-6">
        <p class="text-sm text-emerald-800/80">Bienvenido, {{ auth()->user()->name ?? 'Mayordomo' }}.</p>
    </div>

    <!-- Main Statistics Cards - Enfocados en responsabilidades del mayordomo -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Trabajadores Activos -->
        <div class="border border-emerald-200 rounded p-4 bg-emerald-50">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-emerald-800/80">Trabajadores Activos</div>
                    <div class="text-2xl font-semibold text-emerald-700">{{ $activeWorkers }}</div>
                </div>
                <i data-lucide="users" class="w-8 h-8 text-emerald-600"></i>
            </div>
        </div>

        <!-- Tareas Pendientes -->
        <div class="border border-emerald-200 rounded p-4 bg-emerald-50">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-emerald-800/80">Tareas Pendientes</div>
                    <div class="text-2xl font-semibold text-emerald-700">{{ $pendingTasks }}</div>
                    <div class="text-xs text-emerald-600/70">Completadas: {{ $completedTasks }}</div>
                </div>
                <i data-lucide="clipboard-check" class="w-8 h-8 text-emerald-600"></i>
            </div>
        </div>

        <!-- Tareas por Aprobar -->
        <div class="border border-emerald-200 rounded p-4 bg-emerald-50">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-emerald-800/80">Por Aprobar</div>
                    <div class="text-2xl font-semibold text-emerald-700">{{ $tasksToApprove }}</div>
                    <div class="text-xs text-emerald-600/70">Esperando validación</div>
                </div>
                <i data-lucide="check-circle" class="w-8 h-8 text-emerald-600"></i>
            </div>
        </div>

        <!-- Herramientas Disponibles -->
        <div class="border border-emerald-200 rounded p-4 bg-emerald-50">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-emerald-800/80">Herramientas Disponibles</div>
                    <div class="text-2xl font-semibold text-emerald-700">{{ $availableTools }}</div>
                    <div class="text-xs text-emerald-600/70">Total: {{ $totalTools }}</div>
                </div>
                <i data-lucide="wrench" class="w-8 h-8 text-emerald-600"></i>
            </div>
        </div>
    </div>

    <!-- Quick Actions - Solo las funcionalidades del mayordomo -->
    <div class="bg-white border rounded p-6">
        <h3 class="text-lg font-semibold text-emerald-700 mb-4">Acciones Rápidas</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('foreman.tasks.index') }}" class="flex items-center p-4 border border-emerald-200 rounded hover:bg-emerald-50 transition-colors">
                <i data-lucide="clipboard-list" class="w-6 h-6 text-emerald-600 mr-3"></i>
                <div>
                    <div class="font-medium text-emerald-800">Gestionar Tareas</div>
                    <div class="text-sm text-emerald-600">Crear, editar y aprobar tareas</div>
                </div>
            </a>
            
            <a href="{{ route('foreman.tools.index') }}" class="flex items-center p-4 border border-emerald-200 rounded hover:bg-emerald-50 transition-colors">
                <i data-lucide="wrench" class="w-6 h-6 text-emerald-600 mr-3"></i>
                <div>
                    <div class="font-medium text-emerald-800">Inventario de Herramientas</div>
                    <div class="text-sm text-emerald-600">Registrar y gestionar herramientas</div>
                </div>
            </a>
            
            <a href="{{ route('foreman.workers.index') }}" class="flex items-center p-4 border border-emerald-200 rounded hover:bg-emerald-50 transition-colors">
                <i data-lucide="users" class="w-6 h-6 text-emerald-600 mr-3"></i>
                <div>
                    <div class="font-medium text-emerald-800">Gestionar Trabajadores</div>
                    <div class="text-sm text-emerald-600">Ver, editar y desactivar trabajadores</div>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Activity - Solo información relevante para el mayordomo -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Tareas Recientes -->
        <div class="bg-white border rounded p-6">
            <h3 class="text-lg font-semibold text-emerald-700 mb-4">Tareas Recientes</h3>
            @if($recentTasks->count() > 0)
                <div class="space-y-3">
                    @foreach($recentTasks as $task)
                        <div class="flex items-center justify-between p-3 bg-emerald-50 rounded">
                            <div>
                                <div class="text-sm font-medium text-emerald-800">{{ $task->description }}</div>
                                <div class="text-xs text-emerald-600">
                                    {{ $task->plot->name ?? 'Sin lote' }} • 
                                    {{ $task->assignee->name ?? 'Sin asignar' }}
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($task->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($task->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ ucfirst($task->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-emerald-600/70">No hay tareas recientes</p>
            @endif
        </div>

        <!-- Herramientas en Uso -->
        <div class="bg-white border rounded p-6">
            <h3 class="text-lg font-semibold text-emerald-700 mb-4">Herramientas en Uso</h3>
            @if($toolsInUse->count() > 0)
                <div class="space-y-3">
                    @foreach($toolsInUse as $tool)
                        <div class="flex items-center justify-between p-3 bg-emerald-50 rounded">
                            <div>
                                <div class="text-sm font-medium text-emerald-800">{{ $tool->name }}</div>
                                <div class="text-xs text-emerald-600">
                                    {{ $tool->category ?? 'Sin categoría' }} • 
                                    {{ $tool->available_qty }}/{{ $tool->total_qty }} disponibles
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $tool->status === 'operational' ? 'bg-green-100 text-green-800' : 
                                   ($tool->status === 'damaged' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($tool->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-emerald-600/70">No hay herramientas en uso</p>
            @endif
        </div>
    </div>
</div>
@endsection
