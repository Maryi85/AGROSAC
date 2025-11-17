@extends('admin.layout')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Dashboard Contable</h2>
        <p class="text-sm text-gray-600 mt-1">Panel de control financiero de AGROSAC</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.ledger.dashboard.pdf') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
            <i data-lucide="file-text" class="w-4 h-4"></i>
            <span>Descargar PDF</span>
        </a>
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <i data-lucide="calendar" class="w-4 h-4"></i>
            <span>{{ now()->format('d/m/Y') }}</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-8">
    <style>
        .dashboard-card {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            border: 1px solid rgba(16, 185, 129, 0.1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }
        
        .dashboard-card:hover {
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
        
        .chart-card {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            border: 1px solid rgba(16, 185, 129, 0.15);
            position: relative;
        }
        
        .chart-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #10b981, #059669);
        }
        
        .data-row {
            transition: all 0.2s ease;
            border-radius: 8px;
            padding: 12px;
        }
        
        .data-row:hover {
            background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
            transform: translateX(4px);
        }
        
        .status-badge {
            font-weight: 600;
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .income-badge {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        
        .expense-badge {
            background: linear-gradient(135deg, #fef2f2, #fecaca);
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        .action-button {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 4px 14px 0 rgba(16, 185, 129, 0.3);
            transition: all 0.3s ease;
        }
        
        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px -3px rgba(16, 185, 129, 0.4);
        }
        
        .secondary-button {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            box-shadow: 0 4px 14px 0 rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
        }
        
        .secondary-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px -3px rgba(59, 130, 246, 0.4);
        }
        
        .table-header {
            background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
            border-bottom: 2px solid #10b981;
        }
        
        .table-row {
            transition: all 0.2s ease;
        }
        
        .table-row:hover {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        }
        
        .amount-positive {
            color: #059669;
            font-weight: 600;
        }
        
        .amount-negative {
            color: #dc2626;
            font-weight: 600;
        }
        
        .empty-state {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
        }
    </style>
    <!-- Gráficos y Análisis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Ingresos por Categoría -->
        <div class="chart-card rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">Ingresos por Categoría</h3>
                <div class="p-2 bg-emerald-100 rounded-lg">
                    <i data-lucide="pie-chart" class="w-5 h-5 text-emerald-600"></i>
                </div>
            </div>
            @if($incomeByCategory->count() > 0)
                <div class="space-y-4">
                    @foreach($incomeByCategory as $item)
                        <div class="data-row flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-4 h-4 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-full shadow-sm"></div>
                                <span class="text-sm font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $item->category)) }}</span>
                            </div>
                            <span class="text-sm font-bold text-emerald-600">${{ number_format($item->total, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state text-center py-8">
                    <i data-lucide="trending-up" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                    <p class="text-gray-500 font-medium">No hay ingresos registrados</p>
                    <p class="text-sm text-gray-400 mt-1">Los ingresos aparecerán aquí cuando se registren</p>
                </div>
            @endif
        </div>

        <!-- Gastos por Categoría -->
        <div class="chart-card rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">Gastos por Categoría</h3>
                <div class="p-2 bg-red-100 rounded-lg">
                    <i data-lucide="bar-chart" class="w-5 h-5 text-red-600"></i>
                </div>
            </div>
            @if($expensesByCategory->count() > 0)
                <div class="space-y-4">
                    @foreach($expensesByCategory as $item)
                        <div class="data-row flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-4 h-4 bg-gradient-to-r from-red-500 to-red-600 rounded-full shadow-sm"></div>
                                <span class="text-sm font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $item->category)) }}</span>
                            </div>
                            <span class="text-sm font-bold text-red-600">${{ number_format($item->total, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state text-center py-8">
                    <i data-lucide="trending-down" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                    <p class="text-gray-500 font-medium">No hay gastos registrados</p>
                    <p class="text-sm text-gray-400 mt-1">Los gastos aparecerán aquí cuando se registren</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Costos Operativos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Costos por Tipo -->
        <div class="chart-card rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">Costos Operativos</h3>
                <div class="p-2 bg-orange-100 rounded-lg">
                    <i data-lucide="dollar-sign" class="w-5 h-5 text-orange-600"></i>
                </div>
            </div>
            <div class="space-y-4">
                <!-- Costos de Insumos -->
                @if($totalSupplyCosts > 0)
                    <div class="data-row flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-4 h-4 bg-gradient-to-r from-orange-500 to-orange-600 rounded-full shadow-sm"></div>
                            <span class="text-sm font-medium text-gray-700">Insumos</span>
                        </div>
                        <span class="text-sm font-bold text-orange-600">${{ number_format($totalSupplyCosts, 2) }}</span>
                    </div>
                @endif
                
                <!-- Costos de Herramientas -->
                @if($totalToolCosts > 0)
                    <div class="data-row flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-4 h-4 bg-gradient-to-r from-orange-500 to-orange-600 rounded-full shadow-sm"></div>
                            <span class="text-sm font-medium text-gray-700">Herramientas</span>
                        </div>
                        <span class="text-sm font-bold text-orange-600">${{ number_format($totalToolCosts, 2) }}</span>
                    </div>
                @endif
                
                <!-- Costos de Trabajadores -->
                @if($totalTaskCosts > 0)
                    <div class="data-row flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-4 h-4 bg-gradient-to-r from-orange-500 to-orange-600 rounded-full shadow-sm"></div>
                            <span class="text-sm font-medium text-gray-700">Trabajadores</span>
                        </div>
                        <span class="text-sm font-bold text-orange-600">${{ number_format($totalTaskCosts, 2) }}</span>
                    </div>
                @endif
                
                @if($totalSupplyCosts == 0 && $totalToolCosts == 0 && $totalTaskCosts == 0)
                    <div class="empty-state text-center py-8">
                        <i data-lucide="dollar-sign" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                        <p class="text-gray-500 font-medium">No hay costos registrados</p>
                        <p class="text-sm text-gray-400 mt-1">Los costos aparecerán aquí cuando se registren</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Resultado Financiero -->
        <div class="chart-card rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">Resultado Financiero</h3>
                <div class="p-2 {{ $totalProfit >= 0 ? 'bg-emerald-100' : 'bg-red-100' }} rounded-lg">
                    <i data-lucide="{{ $totalProfit >= 0 ? 'trending-up' : 'trending-down' }}" class="w-5 h-5 {{ $totalProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }}"></i>
                </div>
            </div>
            <div class="space-y-4">
                <div class="data-row flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-4 h-4 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-full shadow-sm"></div>
                        <span class="text-sm font-medium text-gray-700">Total Ingresos</span>
                    </div>
                    <span class="text-sm font-bold text-emerald-600">${{ number_format($totalIncome, 2) }}</span>
                </div>
                
                <div class="data-row flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-4 h-4 bg-gradient-to-r from-red-500 to-red-600 rounded-full shadow-sm"></div>
                        <span class="text-sm font-medium text-gray-700">Total Gastos</span>
                    </div>
                    <span class="text-sm font-bold text-red-600">${{ number_format($totalExpenses, 2) }}</span>
                </div>
                
                <div class="data-row flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-4 h-4 bg-gradient-to-r from-orange-500 to-orange-600 rounded-full shadow-sm"></div>
                        <span class="text-sm font-medium text-gray-700">Total Costos</span>
                    </div>
                    <span class="text-sm font-bold text-orange-600">${{ number_format($totalSupplyCosts + $totalToolCosts + $totalTaskCosts, 2) }}</span>
                </div>
                
                <div class="mt-6 pt-4 border-t-2 {{ $totalProfit >= 0 ? 'border-emerald-200' : 'border-red-200' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-base font-bold text-gray-800">Resultado Final</span>
                        <span class="text-xl font-bold {{ $totalProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $totalProfit >= 0 ? '+' : '' }}${{ number_format($totalProfit, 2) }}
                        </span>
                    </div>
                    <div class="mt-2 text-center">
                        @if($totalProfit >= 0)
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-emerald-100 text-emerald-700 font-semibold text-sm">
                                <i data-lucide="trending-up" class="w-4 h-4"></i>
                                Ganancia
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-red-100 text-red-700 font-semibold text-sm">
                                <i data-lucide="trending-down" class="w-4 h-4"></i>
                                Pérdida
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Análisis de Rentabilidad por Cultivo -->
    <div class="dashboard-card rounded-xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg">
                    <i data-lucide="trending-up" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Análisis de Rentabilidad por Cultivo</h3>
                    <p class="text-sm text-gray-500 mt-1">Ingresos vs Gastos y Costos por cultivo</p>
                </div>
            </div>
            <a href="{{ route('admin.ledger.crop-analysis.pdf') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition-colors">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>PDF</span>
            </a>
        </div>
        
        @if(isset($cropAnalysis) && count($cropAnalysis) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="table-header text-left text-emerald-800">
                            <th class="py-3 px-4 font-semibold">Cultivo</th>
                            <th class="py-3 px-4 font-semibold text-right">Ingresos</th>
                            <th class="py-3 px-4 font-semibold text-right">Gastos</th>
                            <th class="py-3 px-4 font-semibold text-right">Costos</th>
                            <th class="py-3 px-4 font-semibold text-right">Total</th>
                            <th class="py-3 px-4 font-semibold text-right">Ganancia/Pérdida</th>
                        </tr>
                        <tr class="bg-emerald-50 text-xs text-emerald-700">
                            <th></th>
                            <th class="py-2 px-4 text-right font-normal"></th>
                            <th class="py-2 px-4 text-right font-normal">Contables</th>
                            <th class="py-2 px-4 text-right font-normal">Insumos | Trabajadores | Herramientas</th>
                            <th class="py-2 px-4 text-right font-normal">Gastos + Costos</th>
                            <th class="py-2 px-4 text-right font-normal"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cropAnalysis as $analysis)
                        @php
                            $totalCosts = $analysis['expenses']['supply_consumption'] + $analysis['expenses']['supply_movement'] + $analysis['expenses']['tasks'] + ($analysis['expenses']['tools'] ?? 0);
                            $totalGeneral = $analysis['expenses']['ledger'] + $totalCosts;
                            $cropProfit = $analysis['income'] - $totalGeneral;
                        @endphp
                        <tr class="table-row border-b border-gray-100">
                            <td class="py-4 px-4">
                                <div class="font-medium text-gray-800">{{ $analysis['crop']->name }}</div>
                                @if($analysis['crop']->plot)
                                    <div class="text-xs text-gray-500">{{ $analysis['crop']->plot->name }}</div>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-right">
                                <span class="font-semibold text-emerald-600">
                                    ${{ number_format($analysis['income'], 2) }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <span class="text-red-600">
                                    ${{ number_format($analysis['expenses']['ledger'], 2) }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <div class="text-orange-600">
                                    <div>Insumos: ${{ number_format($analysis['expenses']['supply_consumption'] + $analysis['expenses']['supply_movement'], 2) }}</div>
                                    <div class="text-xs mt-1">Trabajadores: ${{ number_format($analysis['expenses']['tasks'], 2) }}</div>
                                    <div class="text-xs mt-1">Herramientas: ${{ number_format($analysis['expenses']['tools'] ?? 0, 2) }}</div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <span class="font-semibold text-red-600">
                                    ${{ number_format($totalGeneral, 2) }}
                                </span>
                                <div class="text-xs text-gray-500 mt-1">
                                    Gastos: ${{ number_format($analysis['expenses']['ledger'], 2) }}<br>
                                    Costos: ${{ number_format($totalCosts, 2) }}
                                </div>
                            </td>
                            <td class="py-4 px-4 text-right">
                                @if($cropProfit >= 0)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-emerald-100 text-emerald-700 font-bold">
                                        <i data-lucide="trending-up" class="w-4 h-4"></i>
                                        +${{ number_format($cropProfit, 2) }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-red-100 text-red-700 font-bold">
                                        <i data-lucide="trending-down" class="w-4 h-4"></i>
                                        ${{ number_format($cropProfit, 2) }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state text-center py-12">
                <i data-lucide="leaf" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                <p class="text-gray-500 font-medium text-lg">No hay cultivos activos para analizar</p>
                <p class="text-sm text-gray-400 mt-2">El análisis de rentabilidad aparecerá aquí cuando haya cultivos activos</p>
            </div>
        @endif
    </div>

    <!-- Movimientos Recientes -->
    <div class="dashboard-card rounded-xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-emerald-100 rounded-lg">
                    <i data-lucide="activity" class="w-5 h-5 text-emerald-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800">Movimientos Recientes</h3>
            </div>
            <a href="{{ route('admin.ledger.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-emerald-600 hover:text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors">
                <i data-lucide="external-link" class="w-4 h-4"></i>
                Ver todos
            </a>
        </div>
        
        @if($recentEntries->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="table-header text-left text-emerald-800">
                            <th class="py-3 px-4 font-semibold">Fecha</th>
                            <th class="py-3 px-4 font-semibold">Tipo</th>
                            <th class="py-3 px-4 font-semibold">Categoría</th>
                            <th class="py-3 px-4 font-semibold">Monto</th>
                            <th class="py-3 px-4 font-semibold">Cultivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentEntries as $entry)
                        <tr class="table-row border-b border-gray-100">
                            <td class="py-4 px-4 font-medium text-gray-700">{{ $entry->occurred_at->format('d/m/Y') }}</td>
                            <td class="py-4 px-4">
                                <span class="status-badge {{ $entry->type === 'income' ? 'income-badge' : 'expense-badge' }}">
                                    {{ $entry->type === 'income' ? 'Ingreso' : 'Gasto' }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-gray-700">{{ ucfirst(str_replace('_', ' ', $entry->category)) }}</td>
                            <td class="py-4 px-4">
                                <span class="font-bold {{ $entry->type === 'income' ? 'amount-positive' : 'amount-negative' }}">
                                    {{ $entry->type === 'income' ? '+' : '-' }}${{ number_format($entry->amount, 2) }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-gray-700">{{ $entry->crop ? $entry->crop->name : '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state text-center py-12">
                <i data-lucide="activity" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                <p class="text-gray-500 font-medium text-lg">No hay movimientos recientes</p>
                <p class="text-sm text-gray-400 mt-2">Los movimientos financieros aparecerán aquí</p>
            </div>
        @endif
    </div>

    <!-- Botones de Acción -->
    <div class="flex flex-col sm:flex-row justify-center gap-4">
        <a href="{{ route('admin.ledger.create') }}" class="action-button inline-flex items-center gap-3 px-6 py-3 text-white rounded-xl font-semibold">
            <i data-lucide="plus" class="w-5 h-5"></i>
            <span>Nuevo Movimiento</span>
        </a>
        <a href="{{ route('admin.ledger.index') }}" class="secondary-button inline-flex items-center gap-3 px-6 py-3 text-white rounded-xl font-semibold">
            <i data-lucide="list" class="w-5 h-5"></i>
            <span>Ver Todos los Movimientos</span>
        </a>
    </div>
</div>
@endsection
