@extends('admin.layout')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h2 class="text-3xl font-extrabold text-gray-900">Dashboard Contable</h2>
        <p class="text-sm text-gray-600 mt-1">Panel de control financiero de AGROSAC</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.ledger.dashboard.pdf') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium transition-all hover:shadow-lg">
            <i data-lucide="file-text" class="w-4 h-4"></i>
            <span>Descargar PDF</span>
        </a>
        <div class="flex items-center gap-2 px-4 py-2.5 bg-gray-100 rounded-xl text-sm text-gray-600">
            <i data-lucide="calendar" class="w-4 h-4"></i>
            <span>{{ now()->format('d/m/Y') }}</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <style>
        /* Modern Card Styles */
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
        
        .kpi-card.income {
            --gradient-from: #10b981;
            --gradient-to: #059669;
            background: linear-gradient(135deg, #f0fdf9 0%, #ecfdf5 100%);
        }
        
        .kpi-card.expense {
            --gradient-from: #ef4444;
            --gradient-to: #dc2626;
            background: linear-gradient(135deg, #fffafa 0%, #fef2f2 100%);
        }
        
        .kpi-card.profit {
            --gradient-from: #10b981;
            --gradient-to: #059669;
            background: linear-gradient(135deg, #fdfffe 0%, #f0fdf9 100%);
        }
        
        .kpi-card.costs {
            --gradient-from: #f59e0b;
            --gradient-to: #d97706;
            background: linear-gradient(135deg, #fffef8 0%, #fffbf0 100%);
        }
        
        .kpi-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
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
        
        .kpi-icon.income-icon {
            --icon-from: #10b981;
            --icon-to: #059669;
            background: linear-gradient(135deg, #10b981, #059669);
        }
        
        .kpi-icon.expense-icon {
            --icon-from: #ef4444;
            --icon-to: #dc2626;
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }
        
        .kpi-icon.profit-icon {
            --icon-from: #10b981;
            --icon-to: #059669;
            background: linear-gradient(135deg, #10b981, #059669);
        }
        
        .kpi-icon.costs-icon {
            --icon-from: #f59e0b;
            --icon-to: #d97706;
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }
        
        .chart-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }
        
        .chart-card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .data-item {
            padding: 14px 16px;
            border-radius: 12px;
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }
        
        .data-item:hover {
            background: linear-gradient(90deg, rgba(16, 185, 129, 0.05) 0%, rgba(16, 185, 129, 0.01) 100%);
            border-left-color: #10b981;
            transform: translateX(4px);
        }
        
        .data-item.expense-item:hover {
            background: linear-gradient(90deg, rgba(239, 68, 68, 0.05) 0%, rgba(239, 68, 68, 0.01) 100%);
            border-left-color: #ef4444;
        }
        
        .data-item.cost-item:hover {
            background: linear-gradient(90deg, rgba(245, 158, 11, 0.05) 0%, rgba(245, 158, 11, 0.01) 100%);
            border-left-color: #f59e0b;
        }
        
        .section-header {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            border-left: 5px solid #10b981;
            border-radius: 12px;
        }
        
        .table-modern {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        
        .table-header-modern {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        
        .table-row-modern {
            transition: all 0.2s ease;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .table-row-modern:hover {
            background: linear-gradient(90deg, rgba(16, 185, 129, 0.03) 0%, rgba(16, 185, 129, 0.01) 100%);
        }
        
        .badge-modern {
            padding: 6px 14px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .badge-income {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #065f46;
        }
        
        .badge-expense {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #991b1b;
        }
        
        .action-button-modern {
            background: linear-gradient(135deg, #10b981, #059669);
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px 0 rgba(16, 185, 129, 0.3);
        }
        
        .action-button-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -8px rgba(16, 185, 129, 0.4);
        }
        
        .pulse-dot {
            animation:pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: .5;
            }
        }
    </style>

    <!-- KPI Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Ingresos -->
        <div class="kpi-card income p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="kpi-icon income-icon">
                    <i data-lucide="trending-up" class="w-8 h-8 text-white"></i>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full pulse-dot"></div>
                    <span class="text-xs font-medium text-emerald-700">Activo</span>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Total Ingresos</p>
                <p class="text-3xl font-black text-emerald-700">${{ number_format($totalIncome, 2) }}</p>
            </div>
        </div>
        
        <!-- Total Gastos -->
        <div class="kpi-card expense p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="kpi-icon expense-icon">
                    <i data-lucide="trending-down" class="w-8 h-8 text-white"></i>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 bg-red-500 rounded-full pulse-dot"></div>
                    <span class="text-xs font-medium text-red-700">Activo</span>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Total Gastos</p>
                <p class="text-3xl font-black text-red-700">${{ number_format($totalExpenses, 2) }}</p>
            </div>
        </div>
        
        <!-- Total Costos -->
        <div class="kpi-card costs p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="kpi-icon costs-icon">
                    <i data-lucide="wallet" class="w-8 h-8 text-white"></i>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 bg-orange-500 rounded-full pulse-dot"></div>
                    <span class="text-xs font-medium text-orange-700">Activo</span>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Total Costos</p>
                <p class="text-3xl font-black text-orange-700">${{ number_format($totalSupplyCosts + $totalToolCosts + $totalTaskCosts, 2) }}</p>
            </div>
        </div>
        
        <!-- Resultado Final -->
        <div class="kpi-card profit p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="kpi-icon profit-icon">
                    <i data-lucide="{{ $totalProfit >= 0 ? 'dollar-sign' : 'alert-triangle' }}" class="w-8 h-8 text-white"></i>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 {{ $totalProfit >= 0 ? 'bg-purple-500' : 'bg-red-500' }} rounded-full pulse-dot"></div>
                    <span class="text-xs font-medium {{ $totalProfit >= 0 ? 'text-purple-700' : 'text-red-700' }}">
                        {{ $totalProfit >= 0 ? 'Ganancia' : 'Pérdida' }}
                    </span>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Resultado Final</p>
                <p class="text-3xl font-black {{ $totalProfit >= 0 ? 'text-purple-700' : 'text-red-700' }}">
                    {{ $totalProfit >= 0 ? '+' : '' }}${{ number_format($totalProfit, 2) }}
                </p>
            </div>
        </div>
    </div>

    {{-- SECCIÓN SUPERIOR: Tendencia y Rentabilidad por Cultivo --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Gráfico de Tendencia Mensual --}}
        <div class="chart-card p-6">
            <div class="section-header px-4 py-3 mb-6">
                <div class="flex items-center gap-3">
                    <i data-lucide="trending-up" class="w-5 h-5 text-emerald-600"></i>
                    <h3 class="text-lg font-bold text-gray-800">Tendencia Financiera (6 Meses)</h3>
                </div>
            </div>
            <div id="monthlyTrendChart" style="height: 350px;"></div>
        </div>

        {{-- Gráfico de Rentabilidad por Cultivo --}}
        @if(isset($cropAnalysis) && count($cropAnalysis) > 0)
        <div class="chart-card p-6">
            <div class="section-header px-4 py-3 mb-6">
                <div class="flex items-center gap-3">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 text-emerald-600"></i>
                    <h3 class="text-lg font-bold text-gray-800">Comparativa Rentabilidad Cultivos</h3>
                </div>
            </div>
            <div id="cropProfitabilityChart" style="height: 350px;"></div>
        </div>
        @else
        <div class="chart-card p-6 flex flex-col items-center justify-center text-center">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                <i data-lucide="sprout" class="w-8 h-8 text-gray-400"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">Sin Análisis de Cultivos</h3>
            <p class="text-sm text-gray-500 max-w-xs mx-auto">Registra movimientos asociados a cultivos para ver la comparativa de rentabilidad aquí.</p>
        </div>
        @endif
    </div>

    {{-- MEJORADO: Distribución por Categorías (Donut Charts) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Ingresos por Categoría (Donut) --}}
        <div class="chart-card p-6">
            <div class="section-header px-4 py-3 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i data-lucide="pie-chart" class="w-5 h-5 text-emerald-600"></i>
                        <h3 class="text-lg font-bold text-gray-800">Ingresos por Categoría</h3>
                    </div>
                    <span class="text-xs font-semibold text-emerald-600 bg-emerald-100 px-3 py-1 rounded-full">
                        {{ $incomeByCategory->count() }} categorías
                    </span>
                </div>
            </div>
            @if($incomeByCategory->count() > 0)
                <div id="incomeCategoryChart" style="height: 300px;"></div>
            @else
                <div class="text-center py-12">
                    <i data-lucide="trending-up" class="w-16 h-16 text-gray-300 mx-auto mb-3"></i>
                    <p class="text-gray-400 font-medium">Sin ingresos registrados</p>
                </div>
            @endif
        </div>

        {{-- Gastos por Categoría (Donut) --}}
        <div class="chart-card p-6">
            <div class="section-header px-4 py-3 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i data-lucide="bar-chart" class="w-5 h-5 text-red-600"></i>
                        <h3 class="text-lg font-bold text-gray-800">Gastos por Categoría</h3>
                    </div>
                    <span class="text-xs font-semibold text-red-600 bg-red-100 px-3 py-1 rounded-full">
                        {{ $expensesByCategory->count() }} categorías
                    </span>
                </div>
            </div>
            @if($expensesByCategory->count() > 0)
                <div id="expenseCategoryChart" style="height: 300px;"></div>
            @else
                <div class="text-center py-12">
                    <i data-lucide="trending-down" class="w-16 h-16 text-gray-300 mx-auto mb-3"></i>
                    <p class="text-gray-400 font-medium">Sin gastos registrados</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Desglose de Costos -->
    <div class="chart-card p-6">
        <div class="section-header px-4 py-3 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i data-lucide="layers" class="w-5 h-5 text-orange-600"></i>
                    <h3 class="text-lg font-bold text-gray-800">Desglose de Costos Operativos</h3>
                </div>
                <span class="text-xs font-semibold text-orange-600 bg-orange-100 px-3 py-1 rounded-full">
                    ${{ number_format($totalSupplyCosts + $totalToolCosts + $totalTaskCosts, 2) }}
                </span>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="data-item cost-item">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-gradient-to-r from-orange-500 to-orange-600 rounded-full shadow-lg"></div>
                        <span class="text-sm font-semibold text-gray-700">Insumos</span>
                    </div>
                    <span class="text-sm font-bold text-orange-600">${{ number_format($totalSupplyCosts, 2) }}</span>
                </div>
            </div>
            <div class="data-item cost-item">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-gradient-to-r from-orange-500 to-orange-600 rounded-full shadow-lg"></div>
                        <span class="text-sm font-semibold text-gray-700">Herramientas</span>
                    </div>
                    <span class="text-sm font-bold text-orange-600">${{ number_format($totalToolCosts, 2) }}</span>
                </div>
            </div>
            <div class="data-item cost-item">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-gradient-to-r from-orange-500 to-orange-600 rounded-full shadow-lg"></div>
                        <span class="text-sm font-semibold text-gray-700">Trabajadores</span>
                    </div>
                    <span class="text-sm font-bold text-orange-600">${{ number_format($totalTaskCosts, 2) }}</span>
                </div>
            </div>
        </div>
    </div>


    <!-- Análisis de Rentabilidad por Cultivo (Tabla Detallada) -->
    <div class="chart-card p-6">
        <div class="section-header px-4 py-3 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i data-lucide="sprout" class="w-5 h-5 text-emerald-600"></i>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Análisis de Rentabilidad por Cultivo</h3>
                        <p class="text-xs text-gray-500 mt-1">Ingresos vs Gastos y Costos totales</p>
                    </div>
                </div>
                <a href="{{ route('admin.ledger.crop-analysis.pdf') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium transition-all">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    PDF
                </a>
            </div>
        </div>
        
        @if(isset($cropAnalysis) && count($cropAnalysis) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full table-modern">
                    <thead>
                        <tr class="table-header-modern">
                            <th class="py-4 px-6 text-left font-bold">Cultivo</th>
                            <th class="py-4 px-6 text-right font-bold">Ingresos</th>
                            <th class="py-4 px-6 text-right font-bold">Gastos</th>
                            <th class="py-4 px-6 text-right font-bold">Costos</th>
                            <th class="py-4 px-6 text-right font-bold">Total</th>
                            <th class="py-4 px-6 text-right font-bold">Resultado</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @foreach($cropAnalysis as $analysis)
                        @php
                            $totalCosts = $analysis['expenses']['supply_consumption'] + $analysis['expenses']['supply_movement'] + $analysis['expenses']['tasks'] + ($analysis['expenses']['tools'] ?? 0);
                            $totalGeneral = $analysis['expenses']['ledger'] + $totalCosts;
                            $cropProfit = $analysis['income'] - $totalGeneral;
                        @endphp
                        <tr class="table-row-modern">
                            <td class="py-4 px-6">
                                <div class="font-bold text-gray-900">{{ $analysis['crop']->name }}</div>
                                @if($analysis['crop']->plot)
                                    <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                        <i data-lucide="map-pin" class="w-3 h-3"></i>
                                        {{ $analysis['crop']->plot->name }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <span class="font-bold text-emerald-600">
                                    ${{ number_format($analysis['income'], 2) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <span class="font-semibold text-red-600">
                                    ${{ number_format($analysis['expenses']['ledger'], 2) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <span class="font-semibold text-orange-600">
                                    ${{ number_format($totalCosts, 2) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <span class="font-bold text-gray-900">
                                    ${{ number_format($totalGeneral, 2) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                @if($cropProfit >= 0)
                                    <span class="badge-modern badge-income">
                                        <i data-lucide="trending-up" class="w-3 h-3"></i>
                                        +${{ number_format($cropProfit, 2) }}
                                    </span>
                                @else
                                    <span class="badge-modern badge-expense">
                                        <i data-lucide="trending-down" class="w-3 h-3"></i>
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
            <div class="text-center py-16">
                <i data-lucide="leaf" class="w-20 h-20 text-gray-300 mx-auto mb-4"></i>
                <p class="text-gray-400 font-semibold text-lg">Sin cultivos activos para analizar</p>
                <p class="text-sm text-gray-400 mt-2">El análisis aparecerá cuando haya cultivos activos</p>
            </div>
        @endif
    </div>

    <!-- Movimientos Recientes -->
    <div class="chart-card p-6">
        <div class="section-header px-4 py-3 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i data-lucide="activity" class="w-5 h-5 text-emerald-600"></i>
                    <h3 class="text-lg font-bold text-gray-800">Movimientos Recientes</h3>
                </div>
                <a href="{{ route('admin.ledger.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-emerald-600 hover:text-white hover:bg-emerald-600 bg-emerald-50 rounded-xl transition-all">
                    <i data-lucide="external-link" class="w-4 h-4"></i>
                    Ver todos
                </a>
            </div>
        </div>
        
        @if($recentEntries->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b-2 border-gray-200">
                            <th class="py-3 px-4 text-left text-xs font-bold text-gray-600 uppercase">Fecha</th>
                            <th class="py-3 px-4 text-left text-xs font-bold text-gray-600 uppercase">Tipo</th>
                            <th class="py-3 px-4 text-left text-xs font-bold text-gray-600 uppercase">Categoría</th>
                            <th class="py-3 px-4 text-right text-xs font-bold text-gray-600 uppercase">Monto</th>
                            <th class="py-3 px-4 text-left text-xs font-bold text-gray-600 uppercase">Cultivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentEntries as $entry)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-4 font-medium text-gray-700 text-sm">{{ $entry->occurred_at->format('d/m/Y') }}</td>
                            <td class="py-3 px-4">
                                <span class="badge-modern {{ $entry->type === 'income' ? 'badge-income' : 'badge-expense' }}">
                                    {{ $entry->type === 'income' ? 'Ingreso' : 'Gasto' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-gray-700 text-sm">{{ ucfirst(str_replace('_', ' ', $entry->category)) }}</td>
                            <td class="py-3 px-4 text-right">
                                <span class="font-bold {{ $entry->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $entry->type === 'income' ? '+' : '-' }}${{ number_format($entry->amount, 2) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-gray-700 text-sm">{{ $entry->crop ? $entry->crop->name : '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-16">
                <i data-lucide="activity" class="w-20 h-20 text-gray-300 mx-auto mb-4"></i>
                <p class="text-gray-400 font-semibold text-lg">Sin movimientos recientes</p>
                <p class="text-sm text-gray-400 mt-2">Los movimientos financieros aparecerán aquí</p>
            </div>
        @endif
    </div>


</div>
@endsection

{{-- ApexCharts Scripts --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.0/dist/apexcharts.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // ============================================
        // 1. GRÁFICO DE TENDENCIA MENSUAL (Area Chart)
        // ============================================
        const monthlyTrendOptions = {
            series: [{
                name: 'Ingresos',
                data: @json($monthlyTrendData->pluck('income'))
            }, {
                name: 'Egresos',
                data: @json($monthlyTrendData->pluck('expenses'))
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            colors: ['#10b981', '#ef4444'],
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    opacityFrom: 0.6,
                    opacityTo: 0.1
                }
            },
            xaxis: {
                categories: @json($monthlyTrendData->pluck('month_label')),
                labels: { style: { fontSize: '12px' } }
            },
            yaxis: {
                labels: {
                    formatter: function(value) {
                        return '$ ' + value.toLocaleString();
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right'
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return '$ ' + value.toLocaleString('es-PE', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    }
                }
            }
        };

        const monthlyTrendChart = new ApexCharts(
            document.querySelector("#monthlyTrendChart"), 
            monthlyTrendOptions
        );
        monthlyTrendChart.render();

        // ============================================
        // 2. INGRESOS POR CATEGORÍA (Donut Chart)
        // ============================================
        @if($incomeByCategory->count() > 0)
        const incomeCategoryOptions = {
            series: @json($incomeByCategory->pluck('total')),
            chart: {
                type: 'donut',
                height: 300
            },
            labels: @json($incomeByCategory->map(function($item) {
                return ucfirst(str_replace('_', ' ', $item->category));
            })),
            colors: ['#10b981', '#059669', '#34d399', '#6ee7b7', '#a7f3d0'],
            legend: {
                position: 'bottom'
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total Ingresos',
                                fontSize: '14px',
                                fontWeight: 600,
                                color: '#6b7280', // gray-500
                                formatter: function (w) {
                                    const total = w.globals.seriesTotals.reduce((a, b) => a + parseFloat(b || 0), 0);
                                    return '$' + total.toLocaleString('en-US', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            },
                            value: {
                                fontSize: '24px',
                                fontWeight: 700,
                                color: '#10b981', // emerald-500
                                offsetY: 8,
                                formatter: function (val) {
                                    return val;
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val.toFixed(1) + '%';
                }
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return '$ ' + value.toLocaleString('es-PE', {minimumFractionDigits: 2});
                    }
                }
            }
        };

        const incomeCategoryChart = new ApexCharts(
            document.querySelector("#incomeCategoryChart"), 
            incomeCategoryOptions
        );
        incomeCategoryChart.render();
        @endif

        // ============================================
        // 3. GASTOS POR CATEGORÍA (Donut Chart)
        // ============================================
        @if($expensesByCategory->count() > 0)
        const expenseCategoryOptions = {
            series: @json($expensesByCategory->pluck('total')),
            chart: {
                type: 'donut',
                height: 300
            },
            labels: @json($expensesByCategory->map(function($item) {
                return ucfirst(str_replace('_', ' ', $item->category));
            })),
            colors: ['#ef4444', '#dc2626', '#f87171', '#fca5a5', '#fecaca'],
            legend: {
                position: 'bottom'
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total Gastos',
                                fontSize: '14px',
                                fontWeight: 600,
                                color: '#6b7280', // gray-500
                                formatter: function (w) {
                                    const total = w.globals.seriesTotals.reduce((a, b) => a + parseFloat(b || 0), 0);
                                    return '$' + total.toLocaleString('en-US', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            },
                            value: {
                                fontSize: '24px',
                                fontWeight: 700,
                                color: '#ef4444', // red-500
                                offsetY: 8,
                                formatter: function (val) {
                                    return val;
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val.toFixed(1) + '%';
                }
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return '$ ' + value.toLocaleString('es-PE', {minimumFractionDigits: 2});
                    }
                }
            }
        };

        const expenseCategoryChart = new ApexCharts(
            document.querySelector("#expenseCategoryChart"), 
            expenseCategoryOptions
        );
        expenseCategoryChart.render();
        @endif

        // ============================================
        // 4. RENTABILIDAD POR CULTIVO (Bar Chart)
        // ============================================
        @if(isset($cropAnalysis) && count($cropAnalysis) > 0)
        const cropProfitabilityOptions = {
            series: [{
                name: 'Ingresos',
                data: @json(collect($cropAnalysis)->map(function($item) {
                    return $item['income'];
                }))
            }, {
                name: 'Costos/Gastos',
                data: @json(collect($cropAnalysis)->map(function($item) {
                    $totalCosts = $item['expenses']['supply_consumption'] + 
                                  $item['expenses']['supply_movement'] + 
                                  $item['expenses']['tasks'] + 
                                  ($item['expenses']['tools'] ?? 0);
                    return $item['expenses']['ledger'] + $totalCosts;
                }))
            }],
            chart: {
                type: 'bar',
                height: 350,
                stacked: false,
                toolbar: { show: false }
            },
            colors: ['#10b981', '#f59e0b'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '70%',
                    borderRadius: 8
                }
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: @json(collect($cropAnalysis)->map(function($item) {
                    return $item['crop']->name;
                })),
                labels: {
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function(value) {
                        return '$ ' + (value / 1000).toFixed(1) + 'K';
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right'
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return '$ ' + value.toLocaleString('es-PE', {minimumFractionDigits: 2});
                    }
                }
            },
            grid: {
                borderColor: '#f1f1f1'
            }
        };

        const cropProfitabilityChart = new ApexCharts(
            document.querySelector("#cropProfitabilityChart"), 
            cropProfitabilityOptions
        );
        cropProfitabilityChart.render();
        @endif
    });
</script>
@endpush
