@extends('admin.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Gestión de Tareas</h2>
    <a href="{{ route('admin.tasks.create') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Asignar Tarea</span>
    </a>
</div>
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

    <!-- Filtros -->
    <form method="GET" class="mb-4 flex gap-2">
        <input type="text" name="q" value="{{ $search }}" placeholder="Buscar por descripción o trabajador" class="border border-emerald-200 rounded px-3 py-2 flex-1" />
        <select name="status" class="border border-emerald-200 rounded px-3 py-2">
            <option value="">Todos los estados</option>
            @foreach($statuses as $key => $label)
                <option value="{{ $key }}" {{ $status === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button class="px-3 py-2 border border-emerald-300 rounded text-emerald-700 hover:bg-emerald-100 inline-flex items-center gap-2">
            <i data-lucide="search" class="w-4 h-4"></i>
            <span>Filtrar</span>
        </button>
    </form>

    <!-- Tabla de tareas -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-emerald-800 border-b">
                    <th class="py-3 pr-4">Tipo</th>
                    <th class="py-3 pr-4">Descripción</th>
                    <th class="py-3 pr-4">Trabajador</th>
                    <th class="py-3 pr-4">Lote</th>
                    <th class="py-3 pr-4">Fecha</th>
                    <th class="py-3 pr-4">Pago</th>
                    <th class="py-3 pr-4">Estado</th>
                    <th class="py-3 pr-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tasks as $task)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 pr-4">
                        <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">
                            {{ ucfirst($task->type) }}
                        </span>
                    </td>
                    <td class="py-3 pr-4 max-w-xs">
                        <div class="truncate" title="{{ $task->description }}">
                            {{ $task->description }}
                        </div>
                    </td>
                    <td class="py-3 pr-4">{{ $task->assignee->name ?? '—' }}</td>
                    <td class="py-3 pr-4">{{ $task->plot->name ?? '—' }}</td>
                    <td class="py-3 pr-4">{{ $task->scheduled_for?->format('d/m/Y') ?? '—' }}</td>
                    <td class="py-3 pr-4">
                        @if($task->hours > 0)
                            {{ $task->hours }}h
                        @elseif($task->kilos > 0)
                            {{ $task->kilos }}kg
                        @else
                            —
                        @endif
                    </td>
                    <td class="py-3 pr-4">
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'in_progress' => 'bg-blue-100 text-blue-700',
                                'completed' => 'bg-green-100 text-green-700',
                                'approved' => 'bg-emerald-100 text-emerald-700',
                                'rejected' => 'bg-red-100 text-red-700',
                                'invalid' => 'bg-gray-100 text-gray-700',
                            ];
                        @endphp
                        <span class="px-2 py-1 text-xs rounded {{ $statusColors[$task->status] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ $statuses[$task->status] ?? ucfirst($task->status) }}
                        </span>
                    </td>
                    <td class="py-3 pr-4 text-right">
                        <div class="flex items-center gap-1 justify-end">
                            <!-- Ver detalles -->
                            <a href="{{ route('admin.tasks.show', $task) }}" class="inline-flex items-center gap-1 px-2 py-1 border rounded hover:bg-blue-50 text-blue-600" title="Ver detalles">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            
                            <!-- Editar (solo si está pendiente o en progreso) -->
                            @if(in_array($task->status, ['pending', 'in_progress']))
                                <a href="{{ route('admin.tasks.edit', $task) }}" class="inline-flex items-center gap-1 px-2 py-1 border rounded hover:bg-emerald-50 text-emerald-600" title="Editar">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </a>
                            @endif
                            
                            <!-- Aprobar (solo si está completada) -->
                            @if($task->status === 'completed')
                                <form method="POST" action="{{ route('admin.tasks.approve', $task) }}" class="inline" data-confirm="true" data-message="¿Aprobar esta tarea?">
                                    @csrf
                                    <button class="inline-flex items-center gap-1 px-2 py-1 border rounded hover:bg-green-50 text-green-600" title="Aprobar">
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            @endif
                            
                            <!-- Invalidar (solo si está completada) -->
                            @if($task->status === 'completed')
                                <form method="POST" action="{{ route('admin.tasks.invalidate', $task) }}" class="inline" data-confirm="true" data-message="¿Marcar esta tarea como inválida?">
                                    @csrf
                                    <button class="inline-flex items-center gap-1 px-2 py-1 border rounded hover:bg-red-50 text-red-600" title="Invalidar">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            @endif
                            
                            <!-- Eliminar (solo si está pendiente o en progreso) -->
                            @if(in_array($task->status, ['pending', 'in_progress']))
                                <form method="POST" action="{{ route('admin.tasks.destroy', $task) }}" class="inline" data-confirm="true" data-message="¿Eliminar esta tarea?">
                                    @csrf
                                    @method('DELETE')
                                    <button class="inline-flex items-center gap-1 px-2 py-1 border rounded hover:bg-red-50 text-red-600" title="Eliminar">
                                        <i data-lucide="trash" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-6 text-center text-emerald-800/70">No hay tareas registradas</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $tasks->links() }}</div>
</div>
@endsection
