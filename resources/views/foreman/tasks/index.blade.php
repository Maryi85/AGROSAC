@extends('foreman.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Gestión de Tareas</h2>
    <a href="{{ route('foreman.tasks.create') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded">
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
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por descripción o trabajador" class="border border-emerald-200 rounded px-3 py-2 flex-1" />
        <select name="status" class="border border-emerald-200 rounded px-3 py-2">
            <option value="">Todos los estados</option>
            @foreach($statuses as $status)
                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                    {{ ucfirst($status) }}
                </option>
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
                            {{ ucfirst($task->status) }}
                        </span>
                    </td>
                    <td class="py-3 pr-4 text-right">
                        <div class="flex items-center gap-1 justify-end">
                            <!-- Ver detalles -->
                            <button class="view-task-btn inline-flex items-center gap-1 px-2 py-1 border rounded hover:bg-blue-50 text-blue-600" 
                                    title="Ver detalles"
                                    data-task-id="{{ $task->id }}">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                            
                            <!-- Editar (solo si está pendiente o en progreso) -->
                            @if(in_array($task->status, ['pending', 'in_progress']))
                                <button class="edit-task-btn inline-flex items-center gap-1 px-2 py-1 border rounded hover:bg-emerald-50 text-emerald-600" 
                                        title="Editar"
                                        data-task-id="{{ $task->id }}">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </button>
                            @endif
                            
                            <!-- Aprobar (solo si está completada) -->
                            @if($task->status === 'completed')
                                <form method="POST" action="{{ route('foreman.tasks.approve', $task) }}" class="inline" data-confirm="true" data-message="¿Aprobar esta tarea?">
                                    @csrf
                                    <button class="inline-flex items-center gap-1 px-2 py-1 border rounded hover:bg-green-50 text-green-600" title="Aprobar">
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            @endif
                            
                            <!-- Invalidar (solo si está completada) -->
                            @if($task->status === 'completed')
                                <form method="POST" action="{{ route('foreman.tasks.invalidate', $task) }}" class="inline" data-confirm="true" data-message="¿Marcar esta tarea como inválida?">
                                    @csrf
                                    <button class="inline-flex items-center gap-1 px-2 py-1 border rounded hover:bg-red-50 text-red-600" title="Invalidar">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            @endif
                            
                            <!-- Eliminar (solo si está pendiente o en progreso) -->
                            @if(in_array($task->status, ['pending', 'in_progress']))
                                <form method="POST" action="{{ route('foreman.tasks.destroy', $task) }}" class="inline" data-confirm="true" data-message="¿Eliminar esta tarea?">
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

<!-- Modal de detalles -->
<div id="taskDetailsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-emerald-700">Detalles de la Tarea</h3>
            <button type="button" onclick="closeTaskDetailsModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <div id="taskDetailsContent" class="space-y-6">
            <!-- Contenido se carga dinámicamente -->
        </div>
    </div>
</div>

<!-- Modal para completar tarea -->
<div id="completeTaskModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-6 w-full max-w-md mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-emerald-700">Completar Tarea</h3>
            <button type="button" onclick="closeCompleteTaskModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form id="completeTaskForm" class="space-y-4">
            @csrf
            <div>
                <label for="modal_hours" class="block text-sm font-medium text-emerald-700 mb-1">Horas Trabajadas *</label>
                <input type="number" name="hours" id="modal_hours" step="0.5" min="0" max="24" required 
                       class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label for="modal_kilos" class="block text-sm font-medium text-emerald-700 mb-1">Kilos Producidos</label>
                <input type="number" name="kilos" id="modal_kilos" step="0.1" min="0" 
                       class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                <button type="button" onclick="closeCompleteTaskModal()" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                    <i data-lucide="check-circle" class="w-4 h-4 inline mr-2"></i>
                    Completar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de edición -->
<div id="editTaskModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-6 w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-emerald-700">Editar Tarea</h3>
            <button type="button" onclick="closeEditTaskModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form id="editTaskForm" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tipo de Tarea -->
                <div>
                    <label for="editType" class="block text-sm font-medium text-emerald-700 mb-2">Tipo de Tarea *</label>
                    <select name="type" id="editType" required 
                            class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Seleccionar tipo</option>
                    </select>
                    <div id="editTypeError" class="text-red-500 text-sm mt-1 hidden"></div>
                </div>

                <!-- Trabajador Asignado -->
                <div>
                    <label for="editAssignedTo" class="block text-sm font-medium text-emerald-700 mb-2">
                        Trabajador Asignado *
                        <button type="button" id="refresh-workers-edit" class="ml-2 text-emerald-600 hover:text-emerald-800" title="Actualizar lista de trabajadores">
                            <i data-lucide="refresh-cw" class="w-4 h-4 inline"></i>
                        </button>
                    </label>
                    <select name="assigned_to" id="editAssignedTo" required 
                            class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Seleccionar trabajador</option>
                    </select>
                    <div id="workers-loading-edit" class="hidden text-sm text-emerald-600 mt-1">
                        <i data-lucide="loader-2" class="w-4 h-4 inline animate-spin mr-1"></i>
                        Actualizando lista de trabajadores...
                    </div>
                    <div id="editAssignedToError" class="text-red-500 text-sm mt-1 hidden"></div>
                </div>

                <!-- Lote -->
                <div>
                    <label for="editPlotId" class="block text-sm font-medium text-emerald-700 mb-2">Lote</label>
                    <select name="plot_id" id="editPlotId" 
                            class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Seleccionar lote (opcional)</option>
                    </select>
                    <div id="editPlotIdError" class="text-red-500 text-sm mt-1 hidden"></div>
                </div>

                <!-- Cultivo -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="editCropId" class="block text-sm font-medium text-emerald-700">Cultivo</label>
                        <button type="button" id="refreshCropsEditBtn" class="text-emerald-600 hover:text-emerald-800 text-sm flex items-center gap-1" title="Actualizar cultivos">
                            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                            Actualizar
                        </button>
                    </div>
                    <div class="relative">
                        <select name="crop_id" id="editCropId" 
                                class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">Seleccionar cultivo (opcional)</option>
                        </select>
                        <div id="crops-loading-edit" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                            <i data-lucide="loader-2" class="w-4 h-4 animate-spin text-emerald-600"></i>
                        </div>
                    </div>
                    <div id="editCropIdError" class="text-red-500 text-sm mt-1 hidden"></div>
                </div>

                <!-- Fecha Programada -->
                <div>
                    <label for="editScheduledFor" class="block text-sm font-medium text-emerald-700 mb-2">Fecha Programada *</label>
                    <input type="date" name="scheduled_for" id="editScheduledFor" required 
                           class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <div id="editScheduledForError" class="text-red-500 text-sm mt-1 hidden"></div>
                </div>

                <!-- Estado -->
                <div>
                    <label for="editStatus" class="block text-sm font-medium text-emerald-700 mb-2">Estado</label>
                    <select name="status" id="editStatus" 
                            class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="pending">Pendiente</option>
                        <option value="in_progress">En Progreso</option>
                        <option value="completed">Completada</option>
                        <option value="approved">Aprobada</option>
                        <option value="rejected">Rechazada</option>
                        <option value="invalid">Inválida</option>
                    </select>
                    <div id="editStatusError" class="text-red-500 text-sm mt-1 hidden"></div>
                </div>
            </div>

            <!-- Descripción -->
            <div>
                <label for="editDescription" class="block text-sm font-medium text-emerald-700 mb-2">Descripción de la Tarea *</label>
                <textarea name="description" id="editDescription" rows="4" required 
                          placeholder="Describe detalladamente la tarea a realizar..."
                          class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                <div id="editDescriptionError" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>

            <!-- Información Adicional -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Horas Estimadas -->
                <div>
                    <label for="editHours" class="block text-sm font-medium text-emerald-700 mb-2">Horas Estimadas</label>
                    <input type="number" name="hours" id="editHours" step="0.5" min="0" max="24" 
                           placeholder="Ej: 2.5"
                           class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <div id="editHoursError" class="text-red-500 text-sm mt-1 hidden"></div>
                </div>

                <!-- Kilos Estimados -->
                <div>
                    <label for="editKilos" class="block text-sm font-medium text-emerald-700 mb-2">Kilos Estimados</label>
                    <input type="number" name="kilos" id="editKilos" step="0.1" min="0" 
                           placeholder="Ej: 50.5"
                           class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <div id="editKilosError" class="text-red-500 text-sm mt-1 hidden"></div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                <button type="button" onclick="closeEditTaskModal()" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="bg-emerald-600 text-white px-6 py-2 rounded hover:bg-emerald-700 transition-colors">
                    <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                    Actualizar Tarea
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentTaskId = null;

// Agregar event listeners para los botones de ver detalles y editar
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.view-task-btn').forEach(button => {
        button.addEventListener('click', function() {
            const taskId = this.getAttribute('data-task-id');
            openTaskDetailsModal(taskId);
        });
    });

    document.querySelectorAll('.edit-task-btn').forEach(button => {
        button.addEventListener('click', function() {
            const taskId = this.getAttribute('data-task-id');
            openEditTaskModal(taskId);
        });
    });
});

// Funciones para el modal de detalles de tarea
async function openTaskDetailsModal(taskId) {
    try {
        // Mostrar loading
        document.getElementById('taskDetailsContent').innerHTML = `
            <div class="flex items-center justify-center py-8">
                <i data-lucide="loader-2" class="w-6 h-6 animate-spin text-emerald-600"></i>
                <span class="ml-2 text-emerald-600">Cargando detalles...</span>
            </div>
        `;
        
        document.getElementById('taskDetailsModal').style.display = 'flex';
        
        const response = await fetch(`/foreman/tasks/${taskId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            renderTaskDetails(data.task);
        } else {
            document.getElementById('taskDetailsContent').innerHTML = `
                <div class="text-center py-8 text-red-600">
                    <i data-lucide="alert-circle" class="w-8 h-8 mx-auto mb-2"></i>
                    <p>Error al cargar los detalles de la tarea</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('taskDetailsContent').innerHTML = `
            <div class="text-center py-8 text-red-600">
                <i data-lucide="alert-circle" class="w-8 h-8 mx-auto mb-2"></i>
                <p>Error de conexión. Inténtalo de nuevo.</p>
            </div>
        `;
    }
}

function renderTaskDetails(task) {
    const statusClass = getTaskStatusClass(task.status);
    const statusText = task.status.charAt(0).toUpperCase() + task.status.slice(1);
    
    let approvalSection = '';
    if (task.status === 'approved' && task.approver_name) {
        approvalSection = `
            <!-- Información de Aprobación -->
            <div class="bg-white border rounded p-6">
                <h3 class="text-lg font-semibold text-emerald-700 mb-4">Información de Aprobación</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-emerald-700 mb-1">Aprobado por</label>
                        <p class="text-emerald-800">${task.approver_name}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-emerald-700 mb-1">Fecha de Aprobación</label>
                        <p class="text-emerald-800">${task.approved_at}</p>
                    </div>
                </div>
            </div>
        `;
    }
    
    let actionsHtml = '';
    if (task.status === 'completed') {
        actionsHtml += `
            <button onclick="approveTask(${task.id})" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition-colors">
                <i data-lucide="check" class="w-4 h-4 inline mr-2"></i>
                Aprobar Tarea
            </button>
        `;
    }
    
    if (task.status !== 'approved' && task.status !== 'invalid') {
        actionsHtml += `
            <button onclick="invalidateTask(${task.id})" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition-colors">
                <i data-lucide="x" class="w-4 h-4 inline mr-2"></i>
                Marcar como Inválida
            </button>
        `;
    }
    
    if (task.status === 'pending' || task.status === 'in_progress') {
        actionsHtml += `
            <button onclick="showCompleteTaskModal(${task.id})" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                <i data-lucide="check-circle" class="w-4 h-4 inline mr-2"></i>
                Marcar como Completada
            </button>
        `;
    }
    
    actionsHtml += `
        <button onclick="deleteTask(${task.id})" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition-colors">
            <i data-lucide="trash-2" class="w-4 h-4 inline mr-2"></i>
            Eliminar Tarea
        </button>
    `;
    
    document.getElementById('taskDetailsContent').innerHTML = `
        <!-- Información General -->
        <div class="bg-white border rounded p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-emerald-700">Información General</h3>
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 text-sm rounded-full ${statusClass}">
                        ${statusText}
                    </span>
                    <span class="px-3 py-1 text-sm rounded-full bg-emerald-100 text-emerald-800">
                        ${task.type.charAt(0).toUpperCase() + task.type.slice(1)}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-emerald-700 mb-1">Descripción</label>
                        <p class="text-emerald-800">${task.description}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-emerald-700 mb-1">Trabajador Asignado</label>
                        <p class="text-emerald-800">${task.assignee_name}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-emerald-700 mb-1">Lote</label>
                        <p class="text-emerald-800">${task.plot_name}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-emerald-700 mb-1">Cultivo</label>
                        <p class="text-emerald-800">${task.crop_name}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-emerald-700 mb-1">Fecha Programada</label>
                        <p class="text-emerald-800">${task.scheduled_for}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-emerald-700 mb-1">Horas Trabajadas</label>
                        <p class="text-emerald-800">${task.hours}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-emerald-700 mb-1">Kilos Producidos</label>
                        <p class="text-emerald-800">${task.kilos}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-emerald-700 mb-1">Fecha de Creación</label>
                        <p class="text-emerald-800">${task.created_at}</p>
                    </div>
                </div>
            </div>
        </div>

        ${approvalSection}

        <!-- Acciones -->
        <div class="bg-white border rounded p-6">
            <h3 class="text-lg font-semibold text-emerald-700 mb-4">Acciones</h3>
            <div class="flex flex-wrap gap-3">
                ${actionsHtml}
            </div>
        </div>
    `;
    
    // Reinicializar iconos de Lucide
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function getTaskStatusClass(status) {
    switch(status) {
        case 'approved': return 'bg-green-100 text-green-800';
        case 'completed': return 'bg-blue-100 text-blue-800';
        case 'in_progress': return 'bg-yellow-100 text-yellow-800';
        case 'pending': return 'bg-gray-100 text-gray-800';
        case 'invalid': return 'bg-red-100 text-red-800';
        case 'rejected': return 'bg-red-100 text-red-800';
        default: return 'bg-orange-100 text-orange-800';
    }
}

function closeTaskDetailsModal() {
    document.getElementById('taskDetailsModal').style.display = 'none';
}

// Funciones para completar tarea
function showCompleteTaskModal(taskId) {
    currentTaskId = taskId;
    document.getElementById('completeTaskModal').style.display = 'flex';
}

function closeCompleteTaskModal() {
    document.getElementById('completeTaskModal').style.display = 'none';
    currentTaskId = null;
    document.getElementById('completeTaskForm').reset();
}

// Manejar envío del formulario de completar tarea
document.getElementById('completeTaskForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!currentTaskId) return;
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    try {
        // Mostrar estado de carga
        submitButton.disabled = true;
        submitButton.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 inline mr-2 animate-spin"></i>Completando...';
        
        const response = await fetch(`/foreman/tasks/${currentTaskId}/complete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                hours: formData.get('hours'),
                kilos: formData.get('kilos') || 0
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Cerrar modal
            closeCompleteTaskModal();
            
            // Recargar el modal de detalles con los nuevos datos
            await openTaskDetailsModal(currentTaskId);
            
            // Mostrar notificación de éxito
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message || 'Error al completar la tarea', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error de conexión. Inténtalo de nuevo.', 'error');
    } finally {
        // Restaurar botón
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    }
});

// Funciones para el modal de edición
async function openEditTaskModal(taskId) {
    try {
        currentTaskId = taskId;
        
        // Mostrar modal
        document.getElementById('editTaskModal').style.display = 'flex';
        
        // Cargar datos de la tarea
        const response = await fetch(`/foreman/tasks/${taskId}/edit`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            populateEditForm(data);
        } else {
            showNotification('Error al cargar los datos de la tarea', 'error');
            closeEditTaskModal();
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error de conexión. Inténtalo de nuevo.', 'error');
        closeEditTaskModal();
    }
}

function populateEditForm(data) {
    const { task, workers, plots, crops, taskTypes } = data;
    
    // Llenar tipo de tarea
    const typeSelect = document.getElementById('editType');
    typeSelect.innerHTML = '<option value="">Seleccionar tipo</option>';
    taskTypes.forEach(type => {
        const option = document.createElement('option');
        option.value = type;
        option.textContent = type.charAt(0).toUpperCase() + type.slice(1);
        if (type === task.type) option.selected = true;
        typeSelect.appendChild(option);
    });
    
    // Llenar trabajadores
    const workersSelect = document.getElementById('editAssignedTo');
    workersSelect.innerHTML = '<option value="">Seleccionar trabajador</option>';
    workers.forEach(worker => {
        const option = document.createElement('option');
        option.value = worker.id;
        option.textContent = worker.name;
        if (worker.id == task.assigned_to) option.selected = true;
        workersSelect.appendChild(option);
    });
    
    // Llenar lotes
    const plotsSelect = document.getElementById('editPlotId');
    plotsSelect.innerHTML = '<option value="">Seleccionar lote (opcional)</option>';
    plots.forEach(plot => {
        const option = document.createElement('option');
        option.value = plot.id;
        option.textContent = plot.name;
        if (plot.id == task.plot_id) option.selected = true;
        plotsSelect.appendChild(option);
    });
    
    // Llenar cultivos
    const cropsSelect = document.getElementById('editCropId');
    cropsSelect.innerHTML = '<option value="">Seleccionar cultivo (opcional)</option>';
    crops.forEach(crop => {
        const option = document.createElement('option');
        option.value = crop.id;
        option.textContent = crop.name;
        if (crop.id == task.crop_id) option.selected = true;
        cropsSelect.appendChild(option);
    });
    
    // Llenar otros campos
    document.getElementById('editScheduledFor').value = task.scheduled_for;
    document.getElementById('editStatus').value = task.status;
    document.getElementById('editDescription').value = task.description;
    document.getElementById('editHours').value = task.hours || '';
    document.getElementById('editKilos').value = task.kilos || '';
    
    // Limpiar errores
    clearEditErrors();
}

function closeEditTaskModal() {
    document.getElementById('editTaskModal').style.display = 'none';
    currentTaskId = null;
    clearEditErrors();
}

function clearEditErrors() {
    const errorElements = [
        'editTypeError', 'editAssignedToError', 'editPlotIdError', 'editCropIdError',
        'editScheduledForError', 'editStatusError', 'editDescriptionError', 'editHoursError', 'editKilosError'
    ];
    
    errorElements.forEach(elementId => {
        const element = document.getElementById(elementId);
        if (element) {
            element.classList.add('hidden');
            element.textContent = '';
        }
    });
}

// Manejar envío del formulario de edición
document.getElementById('editTaskForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!currentTaskId) return;
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    try {
        // Mostrar estado de carga
        submitButton.disabled = true;
        submitButton.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 inline mr-2 animate-spin"></i>Actualizando...';
        
        const response = await fetch(`/foreman/tasks/${currentTaskId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                _method: 'PUT',
                type: formData.get('type'),
                description: formData.get('description'),
                plot_id: formData.get('plot_id') || null,
                crop_id: formData.get('crop_id') || null,
                assigned_to: formData.get('assigned_to'),
                scheduled_for: formData.get('scheduled_for'),
                hours: formData.get('hours') || null,
                kilos: formData.get('kilos') || null,
                status: formData.get('status')
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Cerrar modal
            closeEditTaskModal();
            
            // Recargar la página para actualizar la tabla
            window.location.reload();
            
            // Mostrar notificación de éxito
            showNotification(data.message, 'success');
        } else {
            // Mostrar errores de validación
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const errorElement = document.getElementById(`edit${field.charAt(0).toUpperCase() + field.slice(1)}Error`);
                    if (errorElement) {
                        errorElement.textContent = data.errors[field][0];
                        errorElement.classList.remove('hidden');
                    }
                });
            } else {
                showNotification(data.message || 'Error al actualizar la tarea', 'error');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error de conexión. Inténtalo de nuevo.', 'error');
    } finally {
        // Restaurar botón
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    }
});

// Funciones para actualizar trabajadores y cultivos en el modal de edición
async function refreshWorkersEdit() {
    const workersSelect = document.getElementById('editAssignedTo');
    const workersLoading = document.getElementById('workers-loading-edit');
    const refreshBtn = document.getElementById('refresh-workers-edit');
    const currentValue = workersSelect.value;
    
    try {
        workersLoading.classList.remove('hidden');
        refreshBtn.disabled = true;
        
        const response = await fetch('/foreman/tasks/workers/list', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });
        
        if (!response.ok) {
            throw new Error('Error al cargar trabajadores');
        }
        
        const data = await response.json();
        
        if (data.success) {
            workersSelect.innerHTML = '<option value="">Seleccionar trabajador</option>';
            
            data.workers.forEach(worker => {
                const option = document.createElement('option');
                option.value = worker.id;
                option.textContent = worker.name;
                
                if (currentValue == worker.id) {
                    option.selected = true;
                }
                
                workersSelect.appendChild(option);
            });
            
            showNotification('Lista de trabajadores actualizada correctamente', 'success');
        } else {
            throw new Error('Error en la respuesta del servidor');
        }
    } catch (error) {
        console.error('Error al actualizar trabajadores:', error);
        showNotification('Error al actualizar la lista de trabajadores', 'error');
    } finally {
        workersLoading.classList.add('hidden');
        refreshBtn.disabled = false;
    }
}

async function loadCropsEdit() {
    const cropsSelect = document.getElementById('editCropId');
    const cropsLoading = document.getElementById('crops-loading-edit');
    const refreshBtn = document.getElementById('refreshCropsEditBtn');
    const currentValue = cropsSelect.value;
    
    try {
        cropsLoading.classList.remove('hidden');
        refreshBtn.disabled = true;
        
        const response = await fetch('/foreman/tasks/crops/list', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });
        
        if (!response.ok) {
            throw new Error('Error al cargar cultivos');
        }
        
        const data = await response.json();
        
        if (data.success) {
            cropsSelect.innerHTML = '<option value="">Seleccionar cultivo (opcional)</option>';
            
            data.crops.forEach(crop => {
                const option = document.createElement('option');
                option.value = crop.id;
                option.textContent = crop.name;
                
                if (currentValue == crop.id) {
                    option.selected = true;
                }
                
                cropsSelect.appendChild(option);
            });
            
            showNotification('Lista de cultivos actualizada correctamente', 'success');
        } else {
            throw new Error('Error en la respuesta del servidor');
        }
    } catch (error) {
        console.error('Error al actualizar cultivos:', error);
        showNotification('Error al actualizar la lista de cultivos', 'error');
    } finally {
        cropsLoading.classList.add('hidden');
        refreshBtn.disabled = false;
    }
}

// Event listeners para los botones de actualizar en el modal de edición
document.getElementById('refresh-workers-edit').addEventListener('click', refreshWorkersEdit);
document.getElementById('refreshCropsEditBtn').addEventListener('click', loadCropsEdit);

// Funciones para acciones de tarea
async function approveTask(taskId) {
    if (!confirm('¿Estás seguro de aprobar esta tarea?')) {
        return;
    }
    
    try {
        const response = await fetch(`/foreman/tasks/${taskId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Recargar el modal con los nuevos datos
            await openTaskDetailsModal(taskId);
            
            // Mostrar notificación
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message || 'Error al aprobar la tarea', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error de conexión. Inténtalo de nuevo.', 'error');
    }
}

async function invalidateTask(taskId) {
    if (!confirm('¿Estás seguro de marcar esta tarea como inválida?')) {
        return;
    }
    
    try {
        const response = await fetch(`/foreman/tasks/${taskId}/invalidate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Recargar el modal con los nuevos datos
            await openTaskDetailsModal(taskId);
            
            // Mostrar notificación
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message || 'Error al invalidar la tarea', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error de conexión. Inténtalo de nuevo.', 'error');
    }
}

async function deleteTask(taskId) {
    if (!confirm('¿Estás seguro de eliminar esta tarea? Esta acción no se puede deshacer.')) {
        return;
    }
    
    try {
        const response = await fetch(`/foreman/tasks/${taskId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });
        
        if (response.ok) {
            // Cerrar modal
            closeTaskDetailsModal();
            
            // Recargar la página para actualizar la tabla
            window.location.reload();
            
            // Mostrar notificación
            showNotification('Tarea eliminada correctamente', 'success');
        } else {
            showNotification('Error al eliminar la tarea', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error de conexión. Inténtalo de nuevo.', 'error');
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Cerrar modales al hacer clic fuera de ellos
document.getElementById('taskDetailsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeTaskDetailsModal();
    }
});

document.getElementById('completeTaskModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCompleteTaskModal();
    }
});

document.getElementById('editTaskModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditTaskModal();
    }
});
</script>
@endsection
