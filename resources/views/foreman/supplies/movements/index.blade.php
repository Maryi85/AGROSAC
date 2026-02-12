@extends('foreman.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Movimientos de Inventario</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-4">
    <!-- Botones de acción dentro del contenido -->
    <div class="mb-6 flex justify-start gap-4">
        <a href="{{ route('foreman.supply-movements.create', ['type' => 'entry']) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-100 hover:bg-green-200 text-green-700 border border-green-200 rounded">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Entrada de Insumo</span>
        </a>
        <a href="{{ route('foreman.supply-movements.create', ['type' => 'exit']) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 border border-red-200 rounded">
            <i data-lucide="minus-circle" class="w-4 h-4"></i>
            <span>Salida de Insumo</span>
        </a>
    </div>

    <!-- Tabla de movimientos -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-emerald-800 border-b">
                    <th class="py-3 pr-4">Fecha</th>
                    <th class="py-3 pr-4">Tipo</th>
                    <th class="py-3 pr-4">Insumo</th>
                    <th class="py-3 pr-4">Stock Disponible</th>
                    <th class="py-3 pr-4">Cantidad</th>
                    <th class="py-3 pr-4">Costo Unitario</th>
                    <th class="py-3 pr-4">Total</th>
                    <th class="py-3 pr-4">Motivo</th>
                    <th class="py-3 pr-4">Registrado por</th>
                    <th class="py-3 pr-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($movements as $movement)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 pr-4">
                        <div class="font-medium text-gray-900">{{ $movement->movement_date->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $movement->created_at->format('H:i') }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="px-2 py-1 text-xs rounded {{ $movement->isEntry() ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $movement->isEntry() ? 'Entrada' : 'Salida' }}
                        </span>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="font-medium text-gray-900">{{ $movement->supply->name }}</div>
                        <div class="text-xs text-gray-500">{{ $movement->supply->unit }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold {{ $movement->supply->isLowStock() ? 'text-red-600' : 'text-gray-900' }}">
                                {{ rtrim(rtrim(number_format($movement->supply->current_stock, 2, '.', ','), '0'), '.') }}
                            </span>
                            @if($movement->supply->isLowStock())
                                <i data-lucide="alert-triangle" class="w-4 h-4 text-red-500" title="Stock bajo"></i>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500">Mín: {{ rtrim(rtrim(number_format($movement->supply->min_stock, 2, '.', ','), '0'), '.') }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="font-medium text-gray-900">{{ rtrim(rtrim(number_format($movement->quantity, 2, '.', ','), '0'), '.') }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-600">${{ number_format($movement->unit_cost, 2) }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="font-semibold text-gray-900">${{ number_format($movement->total_cost, 2) }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-600 max-w-xs truncate" title="{{ $movement->reason }}">
                            {{ $movement->reason ?? '—' }}
                        </div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-600">{{ $movement->createdBy->name }}</div>
                    </td>
                    <td class="py-3 pr-4 text-right">
                        <div class="flex items-center gap-1 justify-end">
                            <!-- Editar -->
                            <a href="{{ route('foreman.supply-movements.edit', $movement) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 border border-emerald-200 rounded hover:bg-emerald-50 text-emerald-600" 
                               title="Editar">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="py-6 text-center text-emerald-800/70">No hay movimientos registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-6">
        {{ $movements->links() }}
    </div>
</div>
@endsection