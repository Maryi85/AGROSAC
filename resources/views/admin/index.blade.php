@extends('admin.layout')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Dashboard Administrador</h2>
        <p class="text-sm text-gray-600 mt-1">Panel de control general del sistema AGROSAC</p>
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
        .admin-card {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            border: 1px solid rgba(16, 185, 129, 0.1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }
        
        .admin-card:hover {
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
        
        .status-badge {
            font-weight: 600;
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .status-completed {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        
        .status-pending {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #92400e;
            border: 1px solid #fde68a;
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
    </style>
    <!-- Welcome Section -->
    <div class="admin-card rounded-xl p-6">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl">
                <i data-lucide="shield-check" class="w-6 h-6 text-white"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Bienvenido, {{ auth()->user()->name ?? 'Admin' }}</h3>
                <p class="text-sm text-gray-600">Panel de control general del sistema AGROSAC</p>
            </div>
        </div>
    </div>

    <!-- Main Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Usuarios Activos -->
        <div class="metric-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Usuarios Activos</p>
                    <p class="text-3xl font-bold text-emerald-600 mb-2">{{ $activeUsers }}</p>
                    <p class="text-xs text-gray-500">👥 Total: {{ $totalUsers }}</p>
                </div>
                <div class="p-4 metric-icon rounded-2xl">
                    <i data-lucide="users" class="w-7 h-7 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Lotes Registrados -->
        <div class="metric-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Lotes Registrados</p>
                    <p class="text-3xl font-bold text-emerald-600 mb-2">{{ $totalPlots }}</p>
                    <p class="text-xs text-gray-500">🌱 Activos: {{ $activePlots }}</p>
                </div>
                <div class="p-4 metric-icon rounded-2xl">
                    <i data-lucide="grid-3x3" class="w-7 h-7 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Cultivos Activos -->
        <div class="metric-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Cultivos Activos</p>
                    <p class="text-3xl font-bold text-emerald-600 mb-2">{{ $activeCrops }}</p>
                    <p class="text-xs text-gray-500">🌾 Total: {{ $totalCrops }}</p>
                </div>
                <div class="p-4 metric-icon rounded-2xl">
                    <i data-lucide="sprout" class="w-7 h-7 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Tareas Pendientes -->
        <div class="metric-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Tareas Pendientes</p>
                    <p class="text-3xl font-bold text-emerald-600 mb-2">{{ $pendingTasks }}</p>
                    <p class="text-xs text-gray-500">✅ Completadas: {{ $completedTasks }}</p>
                </div>
                <div class="p-4 metric-icon rounded-2xl">
                    <i data-lucide="clipboard-check" class="w-7 h-7 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Préstamos Activos -->
        <div class="metric-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Préstamos Activos</p>
                    <p class="text-2xl font-bold text-emerald-600 mb-2">{{ $activeLoans }}</p>
                    <p class="text-xs text-gray-500">🔄 En curso</p>
                </div>
                <div class="p-3 metric-icon rounded-xl">
                    <i data-lucide="arrow-left-right" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Insumos Disponibles -->
        <div class="metric-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Insumos Disponibles</p>
                    <p class="text-2xl font-bold text-emerald-600 mb-2">{{ $availableSupplies }}</p>
                    <p class="text-xs text-gray-500">🧪 En stock</p>
                </div>
                <div class="p-3 metric-icon rounded-xl">
                    <i data-lucide="flask-round" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Balance Neto -->
        <div class="metric-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Balance Neto</p>
                    <p class="text-2xl font-bold {{ $netProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }} mb-2">
                        ${{ number_format($netProfit, 2) }}
                    </p>
                    <p class="text-xs text-gray-500">{{ $netProfit >= 0 ? '💰 Ganancia' : '📉 Pérdida' }}</p>
                </div>
                <div class="p-3 metric-icon rounded-xl">
                    <i data-lucide="banknote" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Tareas Recientes -->
        <div class="activity-card rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 rounded-lg">
                        <i data-lucide="clipboard-check" class="w-5 h-5 text-emerald-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Tareas Recientes</h3>
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
                                   ($task->status === 'pending' ? 'status-pending' : 'status-inactive') }}">
                                {{ ucfirst($task->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state text-center py-8">
                    <i data-lucide="clipboard-check" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                    <p class="text-gray-500 font-medium">No hay tareas recientes</p>
                    <p class="text-sm text-gray-400 mt-1">Las tareas aparecerán aquí cuando se creen</p>
                </div>
            @endif
        </div>

        <!-- Movimientos Recientes -->
        <div class="activity-card rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 rounded-lg">
                        <i data-lucide="banknote" class="w-5 h-5 text-emerald-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Movimientos Recientes</h3>
                </div>
            </div>
            @if($recentLedgerEntries->count() > 0)
                <div class="space-y-4">
                    @foreach($recentLedgerEntries as $entry)
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-emerald-50 to-emerald-100 rounded-xl transition-all duration-200 hover:shadow-md">
                            <div>
                                <div class="text-sm font-semibold text-gray-800 mb-1">{{ $entry->category }}</div>
                                <div class="text-xs text-gray-600">
                                    {{ $entry->crop->name ?? 'Sin cultivo' }} • 
                                    {{ $entry->occurred_at->format('d/m/Y') }}
                                </div>
                            </div>
                            <span class="text-sm font-bold {{ $entry->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $entry->type === 'income' ? '+' : '-' }}${{ number_format($entry->amount, 2) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state text-center py-8">
                    <i data-lucide="banknote" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                    <p class="text-gray-500 font-medium">No hay movimientos recientes</p>
                    <p class="text-sm text-gray-400 mt-1">Los movimientos financieros aparecerán aquí</p>
                </div>
            @endif
        </div>
    </div>
  </div>
@endsection


