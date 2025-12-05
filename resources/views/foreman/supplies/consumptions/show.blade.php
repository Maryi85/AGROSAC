@extends('foreman.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Detalles de Consumo de Insumo</h2>
    <a href="{{ route('foreman.supply-consumptions.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-emerald-300 rounded text-emerald-700 hover:bg-emerald-100">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Volver</span>
    </a>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Información del consumo -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Información del Consumo</h3>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Insumo</label>
                <p class="mt-1 text-sm text-gray-900">{{ $supplyConsumption->supply->name }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cantidad</label>
                    <p class="mt-1 text-sm text-gray-900 font-semibold">{{ number_format($supplyConsumption->qty, 2) }} {{ $supplyConsumption->supply->unit }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Costo Total</label>
                    <p class="mt-1 text-2xl font-bold text-emerald-600">${{ number_format($supplyConsumption->total_cost, 2) }}</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Fecha de Uso</label>
                <p class="mt-1 text-sm text-gray-900">{{ $supplyConsumption->used_at->format('d/m/Y') }}</p>
            </div>

            @if($supplyConsumption->crop)
            <div>
                <label class="block text-sm font-medium text-gray-700">Cultivo</label>
                <p class="mt-1 text-sm text-gray-900">{{ $supplyConsumption->crop->name }}</p>
            </div>
            @endif

            @if($supplyConsumption->plot)
            <div>
                <label class="block text-sm font-medium text-gray-700">Lote</label>
                <p class="mt-1 text-sm text-gray-900">{{ $supplyConsumption->plot->name }}</p>
            </div>
            @endif

            @if($supplyConsumption->task)
            <div>
                <label class="block text-sm font-medium text-gray-700">Tarea</label>
                <p class="mt-1 text-sm text-gray-900">{{ $supplyConsumption->task->description }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Información adicional -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Información Adicional</h3>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Costo Unitario</label>
                <p class="mt-1 text-sm text-gray-900 font-semibold">${{ number_format($supplyConsumption->supply->unit_cost, 2) }} / {{ $supplyConsumption->supply->unit }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Fecha de Registro</label>
                <p class="mt-1 text-sm text-gray-900">{{ $supplyConsumption->created_at->format('d/m/Y H:i') }}</p>
            </div>

            @if($supplyConsumption->updated_at != $supplyConsumption->created_at)
            <div>
                <label class="block text-sm font-medium text-gray-700">Última Actualización</label>
                <p class="mt-1 text-sm text-gray-900">{{ $supplyConsumption->updated_at->format('d/m/Y H:i') }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Acciones -->
<div class="flex justify-end gap-4 pt-6 border-t mt-6">
    <a href="{{ route('foreman.supply-consumptions.index') }}" 
       class="px-6 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition-colors">
        Volver al Listado
    </a>
    <a href="{{ route('foreman.supply-consumptions.edit', $supplyConsumption) }}" 
       class="px-6 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded transition-colors">
        <i data-lucide="pencil" class="w-4 h-4 inline mr-2"></i>
        Editar Consumo
    </a>
</div>
@endsection




