@extends('foreman.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Detalles de la Herramienta</h2>
    <div class="flex space-x-2">
        <a href="{{ route('foreman.tools.edit', $tool) }}" class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700 transition-colors">
            <i data-lucide="edit" class="w-4 h-4 inline mr-2"></i>
            Editar
        </a>
        <a href="{{ route('foreman.tools.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
            Volver
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Información Principal -->
    <div class="bg-white border rounded p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-emerald-700">Información General</h3>
            <span class="px-3 py-1 text-sm rounded-full 
                {{ $tool->status === 'operational' ? 'bg-green-100 text-green-800' : 
                   ($tool->status === 'lost' ? 'bg-red-100 text-red-800' : 
                   ($tool->status === 'damaged' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                {{ ucfirst($tool->status) }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-emerald-700 mb-1">Nombre</label>
                    <p class="text-emerald-800">{{ $tool->name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-emerald-700 mb-1">Categoría</label>
                    <p class="text-emerald-800">{{ $tool->category ?? 'Sin categoría' }}</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-emerald-700 mb-1">Fecha de Registro</label>
                    <p class="text-emerald-800">{{ $tool->created_at->format('d/m/Y H:i') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-emerald-700 mb-1">Última Actualización</label>
                    <p class="text-emerald-800">{{ $tool->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Información de Cantidades -->
    <div class="bg-white border rounded p-6">
        <h3 class="text-lg font-semibold text-emerald-700 mb-4">Control de Inventario</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center p-4 bg-emerald-50 rounded">
                <div class="text-2xl font-bold text-emerald-700">{{ $tool->total_qty }}</div>
                <div class="text-sm text-emerald-600">Cantidad Total</div>
            </div>
            
            <div class="text-center p-4 bg-green-50 rounded">
                <div class="text-2xl font-bold text-green-700">{{ $tool->available_qty }}</div>
                <div class="text-sm text-green-600">Disponible</div>
            </div>
            
            <div class="text-center p-4 bg-blue-50 rounded">
                <div class="text-2xl font-bold text-blue-700">{{ $tool->total_qty - $tool->available_qty }}</div>
                <div class="text-sm text-blue-600">En Uso</div>
            </div>
        </div>
    </div>

    <!-- Acciones -->
    <div class="bg-white border rounded p-6">
        <h3 class="text-lg font-semibold text-emerald-700 mb-4">Acciones</h3>
        <div class="flex flex-wrap gap-3">
            <form method="POST" action="{{ route('foreman.tools.destroy', $tool) }}" class="inline" 
                  onsubmit="return confirm('¿Estás seguro de eliminar esta herramienta? Esta acción no se puede deshacer.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition-colors">
                    <i data-lucide="trash-2" class="w-4 h-4 inline mr-2"></i>
                    Eliminar Herramienta
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
