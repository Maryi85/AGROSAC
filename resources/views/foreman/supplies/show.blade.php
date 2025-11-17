@extends('layouts.app')

@section('content')
<div class="bg-white border rounded p-4">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-emerald-700">Detalles del Insumo</h3>
            <div class="flex items-center gap-2">
                <a href="{{ route('foreman.supplies.edit', $supply) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded transition-colors">
                    <i data-lucide="pencil" class="w-4 h-4"></i>
                    <span>Editar</span>
                </a>
                <a href="{{ route('foreman.supplies.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-200 rounded transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Volver</span>
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Información básica -->
        <div class="bg-gray-50 rounded p-4">
            <h4 class="text-md font-semibold text-emerald-700 mb-4">Información Básica</h4>
            <div class="space-y-4">
                <!-- Foto -->
                <div>
                    @if($supply->photo)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $supply->photo) }}" alt="{{ $supply->name }}" class="w-48 h-48 object-cover rounded border border-emerald-200">
                        </div>
                    @else
                        <div class="w-48 h-48 bg-gray-100 rounded border border-gray-200 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path></svg>
                        </div>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <p class="text-sm text-gray-900 mt-1">{{ $supply->name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Unidad de Medida</label>
                    <p class="text-sm text-gray-900 mt-1">{{ $supply->unit }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                    <span class="inline-flex mt-1 px-2 py-1 text-xs rounded {{ $supply->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                        {{ $supply->status === 'active' ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Información de stock -->
        <div class="bg-gray-50 rounded p-4">
            <h4 class="text-md font-semibold text-emerald-700 mb-4">Información de Stock</h4>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Stock Actual</label>
                    <p class="text-sm text-gray-900 mt-1">{{ number_format($supply->current_stock, 3) }} {{ $supply->unit }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Stock Mínimo</label>
                    <p class="text-sm text-gray-900 mt-1">{{ number_format($supply->min_stock, 3) }} {{ $supply->unit }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Costo por Unidad</label>
                    <p class="text-sm text-gray-900 mt-1">${{ number_format($supply->unit_cost, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Consumos -->
    @if($supply->consumptions->isNotEmpty())
    <div class="mt-6">
        <h4 class="text-md font-semibold text-emerald-700 mb-4">Historial de Consumos</h4>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-emerald-800 border-b">
                        <th class="py-3 pr-4">Fecha</th>
                        <th class="py-3 pr-4">Cantidad</th>
                        <th class="py-3 pr-4">Cultivo</th>
                        <th class="py-3 pr-4">Lote</th>
                        <th class="py-3 pr-4">Tarea</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($supply->consumptions as $consumption)
                    <tr class="border-b">
                        <td class="py-3 pr-4">{{ $consumption->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 pr-4">{{ number_format($consumption->quantity, 3) }} {{ $supply->unit }}</td>
                        <td class="py-3 pr-4">{{ $consumption->crop->name ?? 'N/A' }}</td>
                        <td class="py-3 pr-4">{{ $consumption->plot->name ?? 'N/A' }}</td>
                        <td class="py-3 pr-4">{{ $consumption->task->name ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection