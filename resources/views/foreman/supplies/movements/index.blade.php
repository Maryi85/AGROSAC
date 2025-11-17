@extends('foreman.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Movimientos de Insumos</h2>
    <a href="{{ route('foreman.supplies.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-emerald-300 rounded text-emerald-700 hover:bg-emerald-100">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Volver a Insumos</span>
    </a>
</div>
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
    <!-- Filtros -->
    <form method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
            <select name="type" class="w-full border border-gray-300 rounded px-3 py-2">
                <option value="">Todos</option>
                <option value="entry" {{ request('type') === 'entry' ? 'selected' : '' }}>Entradas</option>
                <option value="exit" {{ request('type') === 'exit' ? 'selected' : '' }}>Salidas</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Insumo</label>
            <select name="supply_id" class="w-full border border-gray-300 rounded px-3 py-2">
                <option value="">Todos</option>
                @foreach($movements->pluck('supply')->unique() as $supply)
                    <option value="{{ $supply->id }}" {{ request('supply_id') == $supply->id ? 'selected' : '' }}>
                        {{ $supply->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div class="md:col-span-4 flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded">
                <i data-lucide="filter" class="w-4 h-4"></i>
                <span>Filtrar</span>
            </button>
        </div>
    </form>

    <!-- Tabla de movimientos -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b text-left text-emerald-800">
                    <th class="py-3 pr-4">Fecha</th>
                    <th class="py-3 pr-4">Tipo</th>
                    <th class="py-3 pr-4">Insumo</th>
                    <th class="py-3 pr-4">Cantidad</th>
                    <th class="py-3 pr-4">Costo Unitario</th>
                    <th class="py-3 pr-4">Costo Total</th>
                    <th class="py-3 pr-4">Motivo</th>
                    <th class="py-3 pr-4">Registrado por</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($movements as $movement)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 pr-4">{{ $movement->movement_date->format('d/m/Y') }}</td>
                    <td class="py-3 pr-4">
                        <span class="px-2 py-1 text-xs rounded {{ $movement->type === 'entry' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $movement->type === 'entry' ? 'Entrada' : 'Salida' }}
                        </span>
                    </td>
                    <td class="py-3 pr-4">{{ $movement->supply->name }}</td>
                    <td class="py-3 pr-4">{{ number_format($movement->quantity, 3) }} {{ $movement->supply->unit }}</td>
                    <td class="py-3 pr-4">${{ number_format($movement->unit_cost, 2) }}</td>
                    <td class="py-3 pr-4">${{ number_format($movement->total_cost, 2) }}</td>
                    <td class="py-3 pr-4">{{ $movement->reason ?? '—' }}</td>
                    <td class="py-3 pr-4">{{ $movement->createdBy->name }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-6 text-center text-gray-500">No hay movimientos registrados</td>
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