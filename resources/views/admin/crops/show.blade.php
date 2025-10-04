@extends('admin.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Detalles del Cultivo</h2>
    <a href="{{ route('admin.crops.index') }}" class="inline-flex items-center gap-2 px-3 py-2 border border-emerald-300 rounded text-emerald-700 hover:bg-emerald-100">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Volver</span>
    </a>
</div>
@endsection

@section('content')
<div class="bg-white border rounded p-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Información básica -->
            <div class="border rounded p-4">
                <h3 class="text-lg font-medium text-emerald-800 mb-4">Información Básica</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Nombre</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $crop->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Estado</label>
                        <div class="mt-1">
                            <span class="px-2 py-1 text-xs rounded {{ $crop->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $crop->status === 'active' ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Variedad</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $crop->variety ?? 'No especificada' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Rendimiento por Hectárea</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($crop->yield_per_hectare)
                                {{ $crop->yield_per_hectare }} kg/ha
                            @else
                                No especificado
                            @endif
                        </p>
                    </div>
                </div>
                @if($crop->description)
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-600">Descripción</label>
                        <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $crop->description }}</p>
                    </div>
                @endif
            </div>


            <!-- Tareas asociadas -->
            @if($crop->tasks->count() > 0)
                <div class="border rounded p-4">
                    <h3 class="text-lg font-medium text-emerald-800 mb-4">Tareas Asociadas ({{ $crop->tasks->count() }})</h3>
                    <div class="space-y-2">
                        @foreach($crop->tasks->take(5) as $task)
                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                <div>
                                    <span class="text-sm font-medium text-gray-900">{{ ucfirst($task->type) }}</span>
                                    <span class="text-xs text-gray-500 ml-2">{{ $task->scheduled_for?->format('d/m/Y') }}</span>
                                </div>
                                <span class="px-2 py-1 text-xs rounded
                                    @if($task->status === 'pending') bg-yellow-100 text-yellow-700
                                    @elseif($task->status === 'in_progress') bg-blue-100 text-blue-700
                                    @elseif($task->status === 'completed') bg-green-100 text-green-700
                                    @elseif($task->status === 'approved') bg-emerald-100 text-emerald-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ ucfirst($task->status) }}
                                </span>
                            </div>
                        @endforeach
                        @if($crop->tasks->count() > 5)
                            <p class="text-xs text-gray-500 text-center mt-2">
                                Y {{ $crop->tasks->count() - 5 }} tareas más...
                            </p>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Panel lateral -->
        <div class="space-y-6">
            <!-- Estadísticas -->
            <div class="border rounded p-4">
                <h3 class="text-lg font-medium text-emerald-800 mb-4">Estadísticas</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Total de Tareas</label>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $crop->tasks->count() }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Consumos de Insumos</label>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $crop->supplyConsumptions->count() }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Entradas Contables</label>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $crop->ledgerEntries->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Fechas del sistema -->
            <div class="border rounded p-4">
                <h3 class="text-lg font-medium text-emerald-800 mb-4">Información del Sistema</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Creado</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $crop->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Última actualización</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $crop->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="border rounded p-4">
                <h3 class="text-lg font-medium text-emerald-800 mb-4">Acciones</h3>
                <div class="space-y-2">
                    <!-- Editar -->
                    <a href="{{ route('admin.crops.edit', $crop) }}" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 border border-emerald-200 rounded text-emerald-600 hover:bg-emerald-50">
                        <i data-lucide="pencil" class="w-4 h-4"></i>
                        <span>Editar Cultivo</span>
                    </a>
                    
                    
                    <!-- Eliminar -->
                    @if($crop->status === 'active')
                        <button class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 border border-gray-200 rounded text-gray-400 bg-gray-100 cursor-not-allowed opacity-50" disabled title="No se puede eliminar un cultivo activo. Primero debe inhabilitarlo.">
                            <i data-lucide="trash" class="w-4 h-4"></i>
                            <span>Eliminar Cultivo</span>
                        </button>
                    @else
                        <form method="POST" action="{{ route('admin.crops.destroy', $crop) }}" class="w-full" data-confirm="true" data-message="¿Eliminar este cultivo? Esta acción no se puede deshacer.">
                            @csrf
                            @method('DELETE')
                            <button class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 border border-red-200 rounded text-red-600 hover:bg-red-50">
                                <i data-lucide="trash" class="w-4 h-4"></i>
                                <span>Eliminar Cultivo</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
