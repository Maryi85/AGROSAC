@extends('foreman.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Detalles del Trabajador</h2>
    <div class="flex space-x-2">
        <a href="{{ route('foreman.workers.edit', $worker) }}" class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700 transition-colors">
            <i data-lucide="edit" class="w-4 h-4 inline mr-2"></i>
            Editar
        </a>
        <a href="{{ route('foreman.workers.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors">
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
            <h3 class="text-lg font-semibold text-emerald-700">Información Personal</h3>
            <span class="px-3 py-1 text-sm rounded-full 
                {{ $worker->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $worker->email_verified_at ? 'Activo' : 'Inactivo' }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-emerald-700 mb-1">Nombre Completo</label>
                    <p class="text-emerald-800">{{ $worker->name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-emerald-700 mb-1">Email</label>
                    <p class="text-emerald-800">{{ $worker->email }}</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-emerald-700 mb-1">Fecha de Registro</label>
                    <p class="text-emerald-800">{{ $worker->created_at->format('d/m/Y H:i') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-emerald-700 mb-1">Última Actualización</label>
                    <p class="text-emerald-800">{{ $worker->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tareas Recientes -->
    <div class="bg-white border rounded p-6">
        <h3 class="text-lg font-semibold text-emerald-700 mb-4">Tareas Recientes</h3>
        
        @if($tasks->count() > 0)
            <div class="space-y-3">
                @foreach($tasks as $task)
                    <div class="flex items-center justify-between p-3 bg-emerald-50 rounded">
                        <div>
                            <div class="text-sm font-medium text-emerald-800">{{ $task->description }}</div>
                            <div class="text-xs text-emerald-600">
                                {{ $task->plot->name ?? 'Sin lote' }} • 
                                {{ $task->scheduled_for ? $task->scheduled_for->format('d/m/Y') : 'Sin fecha' }}
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ $task->status === 'approved' ? 'bg-green-100 text-green-800' : 
                               ($task->status === 'completed' ? 'bg-blue-100 text-blue-800' : 
                               ($task->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 
                               ($task->status === 'pending' ? 'bg-gray-100 text-gray-800' : 
                               ($task->status === 'invalid' ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800')))) }}">
                            {{ ucfirst($task->status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4">
                <i data-lucide="clipboard-list" class="w-8 h-8 text-emerald-300 mx-auto mb-2"></i>
                <p class="text-emerald-600 text-sm">No hay tareas asignadas</p>
            </div>
        @endif
    </div>

    <!-- Acciones -->
    <div class="bg-white border rounded p-6">
        <h3 class="text-lg font-semibold text-emerald-700 mb-4">Acciones</h3>
        <div class="flex flex-wrap gap-3">
            <form method="POST" action="{{ route('foreman.workers.toggle-status', $worker) }}" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 rounded transition-colors
                    {{ $worker->email_verified_at ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-green-600 text-white hover:bg-green-700' }}"
                    onclick="return confirm('¿Estás seguro de {{ $worker->email_verified_at ? 'desactivar' : 'activar' }} este trabajador?')">
                    <i data-lucide="{{ $worker->email_verified_at ? 'user-x' : 'user-check' }}" class="w-4 h-4 inline mr-2"></i>
                    {{ $worker->email_verified_at ? 'Desactivar Trabajador' : 'Activar Trabajador' }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
