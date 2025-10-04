@extends('worker.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Dashboard Trabajador</h2>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-white border rounded p-6">
        <p class="text-sm text-emerald-800/80">Bienvenido, {{ auth()->user()->name ?? 'Trabajador' }}.</p>
    </div>

    <!-- Main Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Tareas Pendientes -->
        <div class="border border-emerald-200 rounded p-4 bg-emerald-50">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-emerald-800/80">Tareas Pendientes</div>
                    <div class="text-2xl font-semibold text-emerald-700">{{ $pendingTasks }}</div>
                    <div class="text-xs text-emerald-600/70">Total: {{ $totalTasks }}</div>
                </div>
                <i data-lucide="clock" class="w-8 h-8 text-emerald-600"></i>
            </div>
        </div>

        <!-- Tareas Completadas -->
        <div class="border border-emerald-200 rounded p-4 bg-emerald-50">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-emerald-800/80">Tareas Completadas</div>
                    <div class="text-2xl font-semibold text-emerald-700">{{ $completedTasks }}</div>
                    <div class="text-xs text-emerald-600/70">Este período</div>
                </div>
                <i data-lucide="check-circle" class="w-8 h-8 text-emerald-600"></i>
            </div>
        </div>

        <!-- Herramientas Prestadas -->
        <div class="border border-emerald-200 rounded p-4 bg-emerald-50">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-emerald-800/80">Herramientas Prestadas</div>
                    <div class="text-2xl font-semibold text-emerald-700">{{ $activeLoans }}</div>
                    <div class="text-xs text-emerald-600/70">Activas</div>
                </div>
                <i data-lucide="wrench" class="w-8 h-8 text-emerald-600"></i>
            </div>
        </div>

        <!-- Progreso General -->
        <div class="border border-emerald-200 rounded p-4 bg-emerald-50">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-emerald-800/80">Progreso</div>
                    <div class="text-2xl font-semibold text-emerald-700">
                        {{ $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0 }}%
                    </div>
                    <div class="text-xs text-emerald-600/70">Completado</div>
                </div>
                <i data-lucide="trending-up" class="w-8 h-8 text-emerald-600"></i>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Tareas Pendientes -->
        <div class="bg-white border rounded p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-emerald-700">Tareas Pendientes</h3>
                <a href="{{ route('worker.tasks') }}" class="text-sm text-emerald-600 hover:text-emerald-700">
                    Ver todas
                </a>
            </div>
            @if($myPendingTasks->count() > 0)
                <div class="space-y-3">
                    @foreach($myPendingTasks as $task)
                        <div class="flex items-center justify-between p-3 bg-yellow-50 rounded border border-yellow-200">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-emerald-800">{{ $task->description }}</div>
                                <div class="text-xs text-emerald-600">
                                    {{ $task->plot->name ?? 'Sin lote' }} • 
                                    {{ $task->crop->name ?? 'Sin cultivo' }}
                                </div>
                                @if($task->scheduled_for)
                                    <div class="text-xs text-yellow-600">
                                        Programada: {{ $task->scheduled_for->format('d/m/Y') }}
                                    </div>
                                @endif
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                Pendiente
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-emerald-600/70">No tienes tareas pendientes</p>
            @endif
        </div>

        <!-- Tareas Completadas Recientes -->
        <div class="bg-white border rounded p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-emerald-700">Tareas Completadas</h3>
                <a href="{{ route('worker.reports') }}" class="text-sm text-emerald-600 hover:text-emerald-700">
                    Ver reportes
                </a>
            </div>
            @if($recentCompletedTasks->count() > 0)
                <div class="space-y-3">
                    @foreach($recentCompletedTasks as $task)
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded border border-green-200">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-emerald-800">{{ $task->description }}</div>
                                <div class="text-xs text-emerald-600">
                                    {{ $task->plot->name ?? 'Sin lote' }} • 
                                    {{ $task->crop->name ?? 'Sin cultivo' }}
                                </div>
                                @if($task->updated_at)
                                    <div class="text-xs text-green-600">
                                        Completada: {{ $task->updated_at->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                Completada
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-emerald-600/70">No hay tareas completadas recientemente</p>
            @endif
        </div>
    </div>

    <!-- Herramientas Prestadas -->
    <div class="bg-white border rounded p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-emerald-700">Herramientas Prestadas</h3>
            <a href="{{ route('worker.tools') }}" class="text-sm text-emerald-600 hover:text-emerald-700">
                Gestionar herramientas
            </a>
        </div>
        @if($myLoans->count() > 0)
            <div class="space-y-3">
                @foreach($myLoans as $loan)
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded border border-blue-200">
                        <div class="flex-1">
                            <div class="text-sm font-medium text-emerald-800">{{ $loan->tool->name }}</div>
                            <div class="text-xs text-emerald-600">
                                Prestada: {{ $loan->out_at->format('d/m/Y') }}
                            </div>
                            @if($loan->due_at)
                                <div class="text-xs text-blue-600">Vence: {{ $loan->due_at->format('d/m/Y') }}</div>
                            @endif
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ $loan->status === 'out' ? 'bg-blue-100 text-blue-800' : 
                               ($loan->status === 'returned' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ $loan->status === 'out' ? 'Prestada' : ucfirst($loan->status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-emerald-600/70">No tienes herramientas prestadas</p>
        @endif
    </div>
</div>
@endsection
