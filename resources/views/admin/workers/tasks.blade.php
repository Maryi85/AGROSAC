@extends('admin.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Tareas de {{ $worker->name }}</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-4">
    @if (session('status'))
        <div class="mb-4 p-3 bg-emerald-100 border border-emerald-300 text-emerald-700 rounded">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Botón de regreso -->
    <div class="mb-4">
        <a href="{{ route('admin.workers.show', $worker) }}" class="inline-flex items-center gap-2 px-3 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Volver a {{ $worker->name }}</span>
        </a>
    </div>

    <!-- Información del trabajador -->
    <div class="mb-6 bg-gray-50 rounded p-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center">
                <i data-lucide="user" class="w-6 h-6 text-emerald-600"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ $worker->name }}</h3>
                <p class="text-sm text-gray-600">{{ $worker->email }}</p>
                <div class="flex items-center gap-2 mt-1">
                    @if($worker->email_verified_at)
                        <span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Activo</span>
                    @else
                        <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Inactivo</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <form method="GET" class="mb-6 bg-gray-50 rounded p-4">
        <h3 class="text-md font-semibold text-emerald-700 mb-4">Filtros de Búsqueda</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Estado -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="status" class="w-full border border-emerald-200 rounded px-3 py-2">
                    <option value="all">Todos los estados</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendiente</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completada</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Aprobada</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rechazada</option>
                </select>
            </div>
            
            <!-- Tipo de tarea -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Tarea</label>
                <select name="type" class="w-full border border-emerald-200 rounded px-3 py-2">
                    <option value="all">Todos los tipos</option>
                    <option value="sowing" {{ request('type') === 'sowing' ? 'selected' : '' }}>Siembra</option>
                    <option value="irrigation" {{ request('type') === 'irrigation' ? 'selected' : '' }}>Riego</option>
                    <option value="fertilization" {{ request('type') === 'fertilization' ? 'selected' : '' }}>Fertilización</option>
                    <option value="harvest" {{ request('type') === 'harvest' ? 'selected' : '' }}>Cosecha</option>
                    <option value="maintenance" {{ request('type') === 'maintenance' ? 'selected' : '' }}>Mantenimiento</option>
                    <option value="cleaning" {{ request('type') === 'cleaning' ? 'selected' : '' }}>Limpieza</option>
                    <option value="other" {{ request('type') === 'other' ? 'selected' : '' }}>Otro</option>
                </select>
            </div>
            
            <!-- Fecha desde -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Desde</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border border-emerald-200 rounded px-3 py-2">
            </div>
            
            <!-- Fecha hasta -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Hasta</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full border border-emerald-200 rounded px-3 py-2">
            </div>
        </div>
        
        <div class="mt-4 flex gap-2">
            <button type="submit" class="px-4 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded inline-flex items-center gap-2 transition-colors">
                <i data-lucide="search" class="w-4 h-4"></i>
                <span>Filtrar</span>
            </button>
            <a href="{{ route('admin.workers.tasks', $worker) }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 inline-flex items-center gap-2">
                <i data-lucide="x" class="w-4 h-4"></i>
                <span>Limpiar</span>
            </a>
        </div>
    </form>

    <!-- Tabla de tareas -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-emerald-800 border-b">
                    <th class="py-3 pr-4">Tipo</th>
                    <th class="py-3 pr-4">Descripción</th>
                    <th class="py-3 pr-4">Lote/Cultivo</th>
                    <th class="py-3 pr-4">Fecha</th>
                    <th class="py-3 pr-4">Horas/Kilos</th>
                    <th class="py-3 pr-4">Estado</th>
                    <th class="py-3 pr-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tasks as $task)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 pr-4">
                        <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">
                            {{ ucfirst(str_replace('_', ' ', $task->type)) }}
                        </span>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-900">{{ $task->description ?: 'Sin descripción' }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm">
                            @if($task->plot)
                                <div class="text-gray-900">{{ $task->plot->name }}</div>
                            @endif
                            @if($task->crop)
                                <div class="text-xs text-gray-500">{{ $task->crop->name }}</div>
                            @endif
                        </div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-900">{{ $task->scheduled_for->format('d/m/Y') }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm">
                            @if($task->hours > 0)
                                <div class="text-gray-900">{{ $task->hours }} hrs</div>
                            @endif
                            @if($task->kilos > 0)
                                <div class="text-gray-900">{{ number_format($task->kilos, 1) }} kg</div>
                            @endif
                            @if($task->hours == 0 && $task->kilos == 0)
                                <div class="text-gray-500">—</div>
                            @endif
                        </div>
                    </td>
                    <td class="py-3 pr-4">
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
                    </td>
                    <td class="py-3 pr-4 text-right">
                        <div class="flex items-center gap-1 justify-end">
                            @if($task->status === 'completed')
                                <!-- Aprobar -->
                                <form method="POST" action="{{ route('admin.workers.approve-task', $task) }}" class="inline" data-confirm="true" data-message="¿Aprobar esta tarea?">
                                    @csrf
                                    <button class="inline-flex items-center justify-center w-8 h-8 border border-emerald-200 rounded hover:bg-emerald-50 text-emerald-600" title="Aprobar">
                                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                
                                <!-- Rechazar -->
                                <form method="POST" action="{{ route('admin.workers.reject-task', $task) }}" class="inline" data-confirm="true" data-message="¿Rechazar esta tarea?">
                                    @csrf
                                    <button class="inline-flex items-center justify-center w-8 h-8 border border-red-200 rounded hover:bg-red-50 text-red-600" title="Rechazar">
                                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            @elseif($task->status === 'approved')
                                <span class="text-xs text-emerald-600">Aprobada</span>
                            @elseif($task->status === 'rejected')
                                <span class="text-xs text-red-600">Rechazada</span>
                            @else
                                <span class="text-xs text-gray-500">Pendiente</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-6 text-center text-emerald-800/70">No hay tareas registradas para este trabajador</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $tasks->links() }}</div>
</div>
@endsection
