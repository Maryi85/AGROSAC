@extends('admin.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Detalles del Trabajador</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-4">
    <div class="space-y-6">
        <!-- Información Básica -->
        <div class="bg-gray-50 rounded p-4">
            <h3 class="text-md font-semibold text-emerald-700 mb-4">Información Básica</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center gap-3">
                    @if($worker->photo)
                        <img src="{{ asset('storage/' . $worker->photo) }}" alt="Foto" class="h-16 w-16 rounded-full object-cover border border-gray-200">
                    @else
                        <div class="h-16 w-16 rounded-full border border-dashed border-gray-200 bg-gray-50 flex items-center justify-center text-sm text-gray-400">Sin foto</div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                        <p class="text-sm text-gray-900 mt-1">{{ $worker->name }}</p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                    <p class="text-sm text-gray-900 mt-1">{{ $worker->email }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <p class="text-sm text-gray-900 mt-1">{{ $worker->phone ?? '—' }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                    <div class="mt-1">
                        @if($worker->email_verified_at)
                            <span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Activo</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Inactivo</span>
                        @endif
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Rol</label>
                    <p class="text-sm text-gray-900 mt-1">Trabajador</p>
                </div>
            </div>
        </div>
        
        <!-- Estadísticas del Trabajador -->
        <div class="bg-emerald-50 rounded p-4">
            <h3 class="text-md font-semibold text-emerald-700 mb-4">Estadísticas de Trabajo</h3>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded p-3 text-center">
                    <div class="text-2xl font-bold text-emerald-600">{{ $stats['total_tasks'] }}</div>
                    <div class="text-sm text-gray-600">Total Tareas</div>
                </div>
                
                <div class="bg-white rounded p-3 text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['completed_tasks'] }}</div>
                    <div class="text-sm text-gray-600">Completadas</div>
                </div>
                
                <div class="bg-white rounded p-3 text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['approved_tasks'] }}</div>
                    <div class="text-sm text-gray-600">Aprobadas</div>
                </div>
                
                <div class="bg-white rounded p-3 text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ $stats['pending_tasks'] }}</div>
                    <div class="text-sm text-gray-600">Pendientes</div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div class="bg-white rounded p-3">
                    <div class="flex items-center gap-2 mb-2">
                        <i data-lucide="clock" class="w-5 h-5 text-blue-600"></i>
                        <span class="font-medium text-gray-900">Total Horas Trabajadas</span>
                    </div>
                    <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_hours'], 1) }} hrs</div>
                </div>
                
                <div class="bg-white rounded p-3">
                    <div class="flex items-center gap-2 mb-2">
                        <i data-lucide="wheat" class="w-5 h-5 text-orange-600"></i>
                        <span class="font-medium text-gray-900">Total Kilos Cosechados</span>
                    </div>
                    <div class="text-2xl font-bold text-orange-600">{{ number_format($stats['total_kilos'], 1) }} kg</div>
                </div>
            </div>
        </div>
        
        <!-- Tareas Recientes -->
        <div class="bg-gray-50 rounded p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-md font-semibold text-emerald-700">Tareas Recientes</h3>
                <a href="{{ route('admin.workers.tasks', $worker) }}" class="text-sm text-emerald-600 hover:text-emerald-700">Ver todas</a>
            </div>
            
            @if($recentTasks->count() > 0)
                <div class="space-y-2">
                    @foreach($recentTasks as $task)
                    <div class="bg-white rounded p-3 border">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $task->type)) }}</div>
                                @if($task->description)
                                    <div class="text-sm text-gray-600">{{ $task->description }}</div>
                                @endif
                                <div class="text-xs text-gray-500 mt-1">
                                    @if($task->plot)
                                        Lote: {{ $task->plot->name }} • 
                                    @endif
                                    @if($task->crop)
                                        Cultivo: {{ $task->crop->name }} • 
                                    @endif
                                    Fecha: {{ $task->scheduled_for->format('d/m/Y') }}
                                </div>
                            </div>
                            <div class="text-right">
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-gray-100 text-gray-700',
                                        'in_progress' => 'bg-blue-100 text-blue-700',
                                        'completed' => 'bg-yellow-100 text-yellow-700',
                                        'approved' => 'bg-emerald-100 text-emerald-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                    ];
                                    $statusText = [
                                        'pending' => 'Pendiente',
                                        'in_progress' => 'En Progreso',
                                        'completed' => 'Completada',
                                        'approved' => 'Aprobada',
                                        'rejected' => 'Rechazada',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs rounded {{ $statusClasses[$task->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $statusText[$task->status] ?? $task->status }}
                                </span>
                                @if($task->hours > 0)
                                    <div class="text-xs text-gray-500 mt-1">{{ $task->hours }} hrs</div>
                                @endif
                                @if($task->kilos > 0)
                                    <div class="text-xs text-gray-500 mt-1">{{ $task->kilos }} kg</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4 text-gray-500">
                    <i data-lucide="clipboard-x" class="w-8 h-8 mx-auto mb-2"></i>
                    <p>No hay tareas registradas</p>
                </div>
            @endif
        </div>
        
        <!-- Acciones Disponibles -->
        <div class="bg-blue-50 rounded p-4">
            <h3 class="text-md font-semibold text-blue-700 mb-4">Acciones Disponibles</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Ver Tareas -->
                <div class="bg-white rounded p-4 border">
                    <div class="flex items-center gap-3 mb-2">
                        <i data-lucide="clipboard-list" class="w-5 h-5 text-purple-600"></i>
                        <h4 class="font-medium text-gray-900">Ver Tareas</h4>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">Ver todas las tareas asignadas a este trabajador</p>
                    <a href="{{ route('admin.workers.tasks', $worker) }}" 
                       class="inline-flex items-center gap-2 px-3 py-2 bg-purple-100 hover:bg-purple-200 text-purple-700 border border-purple-200 rounded text-sm transition-colors">
                        <i data-lucide="clipboard-list" class="w-4 h-4"></i>
                        <span>Ver Tareas</span>
                    </a>
                </div>
                
                <!-- Editar Información -->
                <div class="bg-white rounded p-4 border">
                    <div class="flex items-center gap-3 mb-2">
                        <i data-lucide="edit" class="w-5 h-5 text-emerald-600"></i>
                        <h4 class="font-medium text-gray-900">Editar Información</h4>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">Modificar los datos básicos del trabajador</p>
                    <a href="{{ route('admin.workers.edit', $worker) }}" 
                       class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded text-sm transition-colors">
                        <i data-lucide="pencil" class="w-4 h-4"></i>
                        <span>Editar</span>
                    </a>
                </div>
                
                <!-- Recuperar Credenciales -->
                <div class="bg-white rounded p-4 border">
                    <div class="flex items-center gap-3 mb-2">
                        <i data-lucide="key" class="w-5 h-5 text-orange-600"></i>
                        <h4 class="font-medium text-gray-900">Recuperar Credenciales</h4>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">Generar nueva contraseña temporal</p>
                    <form method="POST" action="{{ route('admin.workers.reset-password', $worker) }}" class="inline" data-confirm="true" data-message="¿Restablecer la contraseña de este trabajador? Se generará una nueva contraseña temporal.">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 bg-orange-100 hover:bg-orange-200 text-orange-700 border border-orange-200 rounded text-sm transition-colors">
                            <i data-lucide="key" class="w-4 h-4"></i>
                            <span>Restablecer</span>
                        </button>
                    </form>
                </div>
                
                <!-- Cambiar Estado -->
                <div class="bg-white rounded p-4 border">
                    <div class="flex items-center gap-3 mb-2">
                        <i data-lucide="{{ $worker->email_verified_at ? 'user-x' : 'user-check' }}" class="w-5 h-5 {{ $worker->email_verified_at ? 'text-red-600' : 'text-emerald-600' }}"></i>
                        <h4 class="font-medium text-gray-900">Cambiar Estado</h4>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">{{ $worker->email_verified_at ? 'Desactivar' : 'Activar' }} la cuenta del trabajador</p>
                    <form method="POST" action="{{ route('admin.workers.toggle-status', $worker) }}" class="inline" data-confirm="true" data-message="{{ $worker->email_verified_at ? '¿Desactivar este trabajador?' : '¿Activar este trabajador?' }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 {{ $worker->email_verified_at ? 'bg-red-600 hover:bg-red-700' : 'bg-emerald-600 hover:bg-emerald-700' }} text-white rounded text-sm">
                            <i data-lucide="{{ $worker->email_verified_at ? 'user-x' : 'user-check' }}" class="w-4 h-4"></i>
                            <span>{{ $worker->email_verified_at ? 'Desactivar' : 'Activar' }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Botones de navegación -->
    <div class="mt-6 flex justify-between">
        <a href="{{ route('admin.workers.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Volver al Listado</span>
        </a>
        
        <div class="flex gap-2">
            <a href="{{ route('admin.workers.tasks', $worker) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded">
                <i data-lucide="clipboard-list" class="w-4 h-4"></i>
                <span>Ver Tareas</span>
            </a>
            <a href="{{ route('admin.workers.edit', $worker) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded">
                <i data-lucide="edit" class="w-4 h-4"></i>
                <span>Editar Trabajador</span>
            </a>
        </div>
    </div>
</div>
@endsection
