@extends('admin.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Detalles de la Tarea</h2>
    <a href="{{ route('admin.tasks.index') }}" class="inline-flex items-center gap-2 px-3 py-2 border border-emerald-300 rounded text-emerald-700 hover:bg-emerald-100">
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
            <!-- Detalles básicos -->
            <div class="border rounded p-4">
                <h3 class="text-lg font-medium text-emerald-800 mb-4">Información General</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Tipo de Tarea</label>
                        <p class="mt-1 text-sm text-gray-900">{{ ucfirst($task->type) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Estado</label>
                        <div class="mt-1">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'in_progress' => 'bg-blue-100 text-blue-700',
                                    'completed' => 'bg-green-100 text-green-700',
                                    'approved' => 'bg-emerald-100 text-emerald-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                    'invalid' => 'bg-gray-100 text-gray-700',
                                ];
                                $statusLabels = [
                                    'pending' => 'Pendiente',
                                    'in_progress' => 'En Progreso',
                                    'completed' => 'Completada',
                                    'approved' => 'Aprobada',
                                    'rejected' => 'Rechazada',
                                    'invalid' => 'Inválida',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs rounded {{ $statusColors[$task->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $statusLabels[$task->status] ?? ucfirst($task->status) }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Trabajador Asignado</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $task->assignee->name ?? 'No asignado' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Fecha Programada</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $task->scheduled_for?->format('d/m/Y') ?? 'No programada' }}</p>
                    </div>
                </div>
            </div>

            <!-- Descripción -->
            <div class="border rounded p-4">
                <h3 class="text-lg font-medium text-emerald-800 mb-4">Descripción</h3>
                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $task->description }}</p>
            </div>

            <!-- Ubicación y cultivo -->
            <div class="border rounded p-4">
                <h3 class="text-lg font-medium text-emerald-800 mb-4">Ubicación y Cultivo</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Lote</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($task->plot)
                                {{ $task->plot->name }} ({{ $task->plot->location }})
                            @else
                                No asignado
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Cultivo</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $task->crop->name ?? 'No asignado' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel lateral -->
        <div class="space-y-6">
            <!-- Información de pago -->
            <div class="border rounded p-4">
                <h3 class="text-lg font-medium text-emerald-800 mb-4">Información de Pago</h3>
                <div class="space-y-3">
                    @if($task->hours > 0)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Horas</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $task->hours }} horas</p>
                        </div>
                    @endif
                    @if($task->kilos > 0)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Cantidad</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $task->kilos }} kg</p>
                        </div>
                    @endif
                    @if($task->hours > 0 && $task->hours % 8 === 0)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Días</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $task->hours / 8 }} días</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Información de aprobación -->
            @if($task->approved_at)
                <div class="border rounded p-4">
                    <h3 class="text-lg font-medium text-emerald-800 mb-4">Información de Aprobación</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Aprobado por</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $task->approver->name ?? 'Usuario eliminado' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Fecha de aprobación</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $task->approved_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Fechas de creación y actualización -->
            <div class="border rounded p-4">
                <h3 class="text-lg font-medium text-emerald-800 mb-4">Información del Sistema</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Creada</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $task->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Última actualización</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $task->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="border rounded p-4">
                <h3 class="text-lg font-medium text-emerald-800 mb-4">Acciones</h3>
                <div class="space-y-2">
                    <!-- Editar (solo si está pendiente o en progreso) -->
                    @if(in_array($task->status, ['pending', 'in_progress']))
                        <a href="{{ route('admin.tasks.edit', $task) }}" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 border border-emerald-300 rounded text-emerald-700 hover:bg-emerald-50">
                            <i data-lucide="pencil" class="w-4 h-4"></i>
                            <span>Editar Tarea</span>
                        </a>
                    @endif
                    
                    <!-- Aprobar (solo si está completada) -->
                    @if($task->status === 'completed')
                        <form method="POST" action="{{ route('admin.tasks.approve', $task) }}" class="w-full" data-confirm="true" data-message="¿Aprobar esta tarea?">
                            @csrf
                            <button class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded">
                                <i data-lucide="check" class="w-4 h-4"></i>
                                <span>Aprobar Tarea</span>
                            </button>
                        </form>
                    @endif
                    
                    <!-- Invalidar (solo si está completada) -->
                    @if($task->status === 'completed')
                        <form method="POST" action="{{ route('admin.tasks.invalidate', $task) }}" class="w-full" data-confirm="true" data-message="¿Marcar esta tarea como inválida?">
                            @csrf
                            <button class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded">
                                <i data-lucide="x" class="w-4 h-4"></i>
                                <span>Invalidar Tarea</span>
                            </button>
                        </form>
                    @endif
                    
                    <!-- Eliminar (solo si está pendiente o en progreso) -->
                    @if(in_array($task->status, ['pending', 'in_progress']))
                        <form method="POST" action="{{ route('admin.tasks.destroy', $task) }}" class="w-full" data-confirm="true" data-message="¿Eliminar esta tarea?">
                            @csrf
                            @method('DELETE')
                            <button class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 border border-red-300 rounded text-red-700 hover:bg-red-50">
                                <i data-lucide="trash" class="w-4 h-4"></i>
                                <span>Eliminar Tarea</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
