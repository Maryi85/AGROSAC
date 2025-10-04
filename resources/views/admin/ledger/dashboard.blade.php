@extends('admin.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Dashboard Contable</h2>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Resumen General -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Ingresos -->
        <div class="bg-white border rounded p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Ingresos</p>
                    <p class="text-2xl font-bold text-emerald-600">${{ number_format($totalIncome, 2) }}</p>
                </div>
                <div class="p-3 bg-emerald-100 rounded-full">
                    <i data-lucide="trending-up" class="w-6 h-6 text-emerald-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Gastos -->
        <div class="bg-white border rounded p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Gastos</p>
                    <p class="text-2xl font-bold text-red-600">${{ number_format($totalExpenses, 2) }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <i data-lucide="trending-down" class="w-6 h-6 text-red-600"></i>
                </div>
            </div>
        </div>

        <!-- Ganancia/Prdida Neta -->
        <div class="bg-white border rounded p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Resultado Neto</p>
                    <p class="text-2xl font-bold {{ $netProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ $netProfit >= 0 ? '+' : '' }}${{ number_format($netProfit, 2) }}
                    </p>
                </div>
                <div class="p-3 {{ $netProfit >= 0 ? 'bg-emerald-100' : 'bg-red-100' }} rounded-full">
                    <i data-lucide="{{ $netProfit >= 0 ? 'check-circle' : 'x-circle' }}" class="w-6 h-6 {{ $netProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }}"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos y Análisis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Ingresos por Categoría -->
        <div class="bg-white border rounded p-6">
            <h3 class="text-lg font-semibold text-emerald-700 mb-4">Ingresos por Categoría</h3>
            @if($incomeByCategory->count() > 0)
                <div class="space-y-3">
                    @foreach($incomeByCategory as $item)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                                <span class="text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $item->category)) }}</span>
                            </div>
                            <span class="text-sm font-medium text-emerald-600">${{ number_format($item->total, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No hay ingresos registrados</p>
            @endif
        </div>

        <!-- Gastos por Categoría -->
        <div class="bg-white border rounded p-6">
            <h3 class="text-lg font-semibold text-emerald-700 mb-4">Gastos por Categoría</h3>
            @if($expensesByCategory->count() > 0)
                <div class="space-y-3">
                    @foreach($expensesByCategory as $item)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                <span class="text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $item->category)) }}</span>
                            </div>
                            <span class="text-sm font-medium text-red-600">${{ number_format($item->total, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No hay gastos registrados</p>
            @endif
        </div>
    </div>

    <!-- Análisis por Cultivo -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Ingresos por Cultivo -->
        <div class="bg-white border rounded p-6">
            <h3 class="text-lg font-semibold text-emerald-700 mb-4">Ingresos por Cultivo</h3>
            @if($incomeByCrop->count() > 0)
                <div class="space-y-3">
                    @foreach($incomeByCrop as $item)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                                <span class="text-sm text-gray-700">{{ $item->crop->name }}</span>
                            </div>
                            <span class="text-sm font-medium text-emerald-600">${{ number_format($item->total, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No hay ingresos por cultivo registrados</p>
            @endif
        </div>

        <!-- Gastos por Cultivo -->
        <div class="bg-white border rounded p-6">
            <h3 class="text-lg font-semibold text-emerald-700 mb-4">Gastos por Cultivo</h3>
            @if($expensesByCrop->count() > 0)
                <div class="space-y-3">
                    @foreach($expensesByCrop as $item)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                <span class="text-sm text-gray-700">{{ $item->crop->name }}</span>
                            </div>
                            <span class="text-sm font-medium text-red-600">${{ number_format($item->total, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No hay gastos por cultivo registrados</p>
            @endif
        </div>
    </div>

    <!-- Movimientos Recientes -->
    <div class="bg-white border rounded p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-emerald-700">Movimientos Recientes</h3>
            <a href="{{ route('admin.ledger.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700">Ver todos</a>
        </div>
        
        @if($recentEntries->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-emerald-800 border-b">
                            <th class="py-2 pr-4">Fecha</th>
                            <th class="py-2 pr-4">Tipo</th>
                            <th class="py-2 pr-4">Categoría</th>
                            <th class="py-2 pr-4">Monto</th>
                            <th class="py-2 pr-4">Cultivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentEntries as $entry)
                        <tr class="border-b">
                            <td class="py-2 pr-4">{{ $entry->occurred_at->format('d/m/Y') }}</td>
                            <td class="py-2 pr-4">
                                <span class="px-2 py-1 text-xs rounded {{ $entry->type === 'income' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $entry->type === 'income' ? 'Ingreso' : 'Gasto' }}
                                </span>
                            </td>
                            <td class="py-2 pr-4">{{ ucfirst(str_replace('_', ' ', $entry->category)) }}</td>
                            <td class="py-2 pr-4">
                                <span class="font-medium {{ $entry->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $entry->type === 'income' ? '+' : '-' }}${{ number_format($entry->amount, 2) }}
                                </span>
                            </td>
                            <td class="py-2 pr-4">{{ $entry->crop ? $entry->crop->name : '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center py-4">No hay movimientos recientes</p>
        @endif
    </div>

    <!-- Botones de Acción -->
    <div class="flex justify-center gap-4">
        <a href="{{ route('admin.ledger.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Nuevo Movimiento</span>
        </a>
        <a href="{{ route('admin.ledger.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
            <i data-lucide="list" class="w-4 h-4"></i>
            <span>Ver Todos los Movimientos</span>
        </a>
    </div>
</div>
@endsection
