@extends('admin.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Dashboard Administrador</h2>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
<div class="bg-white border rounded p-6">
    <p class="text-sm text-emerald-800/80">Bienvenido, {{ auth()->user()->name ?? 'Admin' }}.</p>
    </div>

    <!-- Main Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Usuarios Activos -->
        <div class="border border-emerald-200 rounded p-4 bg-emerald-50">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-emerald-800/80">Usuarios Activos</div>
                    <div class="text-2xl font-semibold text-emerald-700">{{ $activeUsers }}</div>
                    <div class="text-xs text-emerald-600/70">Total: {{ $totalUsers }}</div>
                </div>
                <i data-lucide="users" class="w-8 h-8 text-emerald-600"></i>
            </div>
        </div>

        <!-- Lotes Registrados -->
        <div class="border border-emerald-200 rounded p-4 bg-emerald-50">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-emerald-800/80">Lotes Registrados</div>
                    <div class="text-2xl font-semibold text-emerald-700">{{ $totalPlots }}</div>
                    <div class="text-xs text-emerald-600/70">Activos: {{ $activePlots }}</div>
                </div>
                <i data-lucide="grid-3x3" class="w-8 h-8 text-emerald-600"></i>
            </div>
        </div>

        <!-- Cultivos Activos -->
        <div class="border border-emerald-200 rounded p-4 bg-emerald-50">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-emerald-800/80">Cultivos Activos</div>
                    <div class="text-2xl font-semibold text-emerald-700">{{ $activeCrops }}</div>
                    <div class="text-xs text-emerald-600/70">Total: {{ $totalCrops }}</div>
                </div>
                <i data-lucide="sprout" class="w-8 h-8 text-emerald-600"></i>
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
    </div>

    <!-- Secondary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Préstamos Activos -->
        <div class="border border-emerald-200 rounded p-4 bg-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-emerald-800/80">Préstamos Activos</div>
                    <div class="text-xl font-semibold text-emerald-700">{{ $activeLoans }}</div>
                </div>
                <i data-lucide="arrow-left-right" class="w-6 h-6 text-emerald-600"></i>
            </div>
        </div>

        <!-- Insumos Disponibles -->
        <div class="border border-emerald-200 rounded p-4 bg-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-emerald-800/80">Insumos Disponibles</div>
                    <div class="text-xl font-semibold text-emerald-700">{{ $availableSupplies }}</div>
                </div>
                <i data-lucide="flask-round" class="w-6 h-6 text-emerald-600"></i>
            </div>
        </div>

        <!-- Balance Neto -->
        <div class="border border-emerald-200 rounded p-4 bg-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-emerald-800/80">Balance Neto</div>
                    <div class="text-xl font-semibold {{ $netProfit >= 0 ? 'text-emerald-700' : 'text-red-600' }}">
                        ${{ number_format($netProfit, 2) }}
                    </div>
                </div>
                <i data-lucide="banknote" class="w-6 h-6 text-emerald-600"></i>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
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
                                   ($task->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($task->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-emerald-600/70">No hay tareas recientes</p>
            @endif
        </div>

        <!-- Movimientos Recientes -->
        <div class="bg-white border rounded p-6">
            <h3 class="text-lg font-semibold text-emerald-700 mb-4">Movimientos Recientes</h3>
            @if($recentLedgerEntries->count() > 0)
                <div class="space-y-3">
                    @foreach($recentLedgerEntries as $entry)
                        <div class="flex items-center justify-between p-3 bg-emerald-50 rounded">
                            <div>
                                <div class="text-sm font-medium text-emerald-800">{{ $entry->category }}</div>
                                <div class="text-xs text-emerald-600">
                                    {{ $entry->crop->name ?? 'Sin cultivo' }} • 
                                    {{ $entry->occurred_at->format('d/m/Y') }}
                                </div>
                            </div>
                            <span class="text-sm font-semibold {{ $entry->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $entry->type === 'income' ? '+' : '-' }}${{ number_format($entry->amount, 2) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-emerald-600/70">No hay movimientos recientes</p>
            @endif
        </div>
    </div>
  </div>
@endsection


