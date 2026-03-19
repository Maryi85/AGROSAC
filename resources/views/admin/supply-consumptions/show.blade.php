@extends('admin.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Detalles del Consumo</h2>
    <a href="{{ route('admin.supply-consumptions.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-emerald-300 rounded text-emerald-700 hover:bg-emerald-100 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Volver</span>
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Información Principal -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h3 class="text-xl font-bold text-gray-900">{{ $supplyConsumption->supply->name }}</h3>
                <p class="text-sm text-gray-500">Registrado el {{ $supplyConsumption->used_at->format('d/m/Y') }}</p>
            </div>
            <div class="px-4 py-2 bg-emerald-50 rounded-lg border border-emerald-100">
                <span class="block text-xs text-emerald-600 font-medium uppercase tracking-wider">Costo Total</span>
                <span class="text-xl font-bold text-emerald-700">${{ number_format((int)$supplyConsumption->total_cost, 0) }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                <span class="block text-xs text-gray-500 font-medium uppercase mb-1">Cantidad Utilizada</span>
                <span class="text-lg font-semibold text-gray-900">{{ rtrim(rtrim(number_format((float)$supplyConsumption->qty, 3, '.', ','), '0'), '.') }} {{ $supplyConsumption->supply->unit }}</span>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                <span class="block text-xs text-gray-500 font-medium uppercase mb-1">Costo Unitario</span>
                <span class="text-lg font-semibold text-gray-900">${{ number_format($supplyConsumption->supply->unit_cost, 0) }}</span>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                <span class="block text-xs text-gray-500 font-medium uppercase mb-1">Unidad</span>
                <span class="text-lg font-semibold text-gray-900">{{ $supplyConsumption->supply->unit }}</span>
            </div>
        </div>
    </div>

    <!-- Información Relacionada -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h4 class="text-sm font-semibold text-emerald-700 uppercase tracking-wider mb-4 border-b border-emerald-50 pb-2">Ubicación y Cultivo</h4>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Lote:</span>
                    <span class="text-sm font-medium text-gray-900">{{ $supplyConsumption->plot->name ?? 'No especificado' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Cultivo:</span>
                    <span class="text-sm font-medium text-gray-900">{{ $supplyConsumption->crop->name ?? 'No especificado' }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h4 class="text-sm font-semibold text-emerald-700 uppercase tracking-wider mb-4 border-b border-emerald-50 pb-2">Contexto de la Tarea</h4>
            <div class="space-y-4">
                <div class="flex justify-between items-start">
                    <span class="text-sm text-gray-500">Tarea Asociada:</span>
                    <div class="text-right">
                        @if($supplyConsumption->task)
                            <span class="block text-sm font-medium text-gray-900">{{ $supplyConsumption->task->type }}</span>
                            <span class="text-xs text-gray-500">{{ $supplyConsumption->task->scheduled_for->format('d/m/Y') }}</span>
                        @else
                            <span class="text-sm font-medium text-gray-400 italic">Sin tarea vinculada</span>
                        @endif
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Estado de Tarea:</span>
                    @if($supplyConsumption->task)
                        <span class="px-2 py-0.5 text-xs rounded-full font-medium 
                            {{ $supplyConsumption->task->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 
                               ($supplyConsumption->task->status === 'completed' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                            {{ ucfirst($supplyConsumption->task->status) }}
                        </span>
                    @else
                        <span class="text-sm font-medium text-gray-400">-</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="flex justify-end gap-3 pt-4">
        <a href="{{ route('admin.supply-consumptions.edit', $supplyConsumption) }}" 
           class="inline-flex items-center gap-2 px-6 py-2 bg-emerald-100 text-emerald-700 border border-emerald-200 rounded font-medium hover:bg-emerald-200 transition-colors">
            <i data-lucide="pencil" class="w-4 h-4"></i>
            <span>Editar Consumo</span>
        </a>
        <form method="POST" action="{{ route('admin.supply-consumptions.destroy', $supplyConsumption) }}" 
              data-confirm="true" data-message="¿Estás seguro de eliminar este registro de consumo?">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-red-100 text-red-700 border border-red-200 rounded font-medium hover:bg-red-200 transition-colors">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
                <span>Eliminar Registro</span>
            </button>
        </form>
    </div>
</div>
@endsection
