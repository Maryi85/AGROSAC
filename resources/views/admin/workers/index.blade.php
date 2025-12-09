@extends('admin.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Gestión de Trabajadores</h2>
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

    @if (session('temp_password'))
        <div class="mb-4 p-3 bg-blue-100 border border-blue-300 text-blue-700 rounded">
            <strong>Contraseña temporal generada:</strong> {{ session('temp_password') }}
            <br><small>Comparta esta contraseña con el trabajador de forma segura.</small>
        </div>
    @endif

    <!-- Botones de acción principales -->
    <div class="mb-6 flex justify-between items-center">
        <div class="flex gap-4">
            <a href="{{ route('admin.workers.daily-tasks') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 border border-blue-200 rounded transition-colors">
                <i data-lucide="clock" class="w-4 h-4"></i>
                <span>Control Tareas Diarias</span>
            </a>
            <a href="{{ route('admin.workers.harvest-tasks') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-100 hover:bg-orange-200 text-orange-700 border border-orange-200 rounded transition-colors">
                <i data-lucide="wheat" class="w-4 h-4"></i>
                <span>Control Recolecta</span>
            </a>
            <a href="{{ route('admin.workers.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded-lg font-medium transition-colors">
                <i data-lucide="plus" class="w-5 h-5"></i>
                <span>Nuevo Trabajador</span>
            </a>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.workers.pdf', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 border border-red-200 rounded-lg font-medium transition-colors">
                <i data-lucide="file-text" class="w-5 h-5"></i>
                <span>Descargar PDF</span>
            </a>
        </div>
    </div>

    <!-- Filtros de búsqueda -->
    <form method="GET" class="mb-4 flex gap-2 items-end">
        <div class="flex-1">
            <label class="block text-sm mb-1 text-emerald-800">Buscar por nombre o email</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar trabajadores..." class="w-full border border-emerald-200 rounded px-3 py-2" />
        </div>
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Estado</label>
            <select name="status" class="border border-emerald-200 rounded px-3 py-2">
                <option value="all">Todos los estados</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activos</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivos</option>
            </select>
        </div>
        <button type="submit" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded inline-flex items-center gap-2">
            <i data-lucide="search" class="w-4 h-4"></i>
            <span>Filtrar</span>
        </button>
    </form>

    <!-- Tabla de trabajadores -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-emerald-800 border-b">
                    <th class="py-3 pr-4">Foto</th>
                    <th class="py-3 pr-4">Nombre</th>
                    <th class="py-3 pr-4">Email</th>
                    <th class="py-3 pr-4">Teléfono</th>
                    <th class="py-3 pr-4">Estado</th>
                    <th class="py-3 pr-4">Tareas Completadas</th>
                    <th class="py-3 pr-4">Fecha de Registro</th>
                    <th class="py-3 pr-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($workers as $worker)
                <tr class="border-b hover:bg-gray-50" data-worker-id="{{ $worker->id }}">
                    <td class="py-3 pr-4">
                        @if($worker->photo)
                            <img src="{{ asset('storage/' . $worker->photo) }}" alt="Foto" class="h-10 w-10 rounded-full object-cover border border-gray-200">
                        @else
                            <div class="h-10 w-10 rounded-full border border-dashed border-gray-200 bg-gray-50 flex items-center justify-center text-xs text-gray-400">—</div>
                        @endif
                    </td>
                    <td class="py-3 pr-4">
                        <div class="font-medium text-gray-900 worker-name">{{ $worker->name }}</div>
                    </td>
                    <td class="py-3 pr-4 worker-email">
                        {{ $worker->email }}
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-800">{{ $worker->phone ?? '—' }}</div>
                    </td>
                    <td class="py-3 pr-4 status-badge">
                        @if($worker->email_verified_at)
                            <span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Activo</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Inactivo</span>
                        @endif
                    </td>
                    <td class="py-3 pr-4">
                        @php
                            $completedTasks = \App\Models\Task::where('assigned_to', $worker->id)->where('status', 'completed')->count();
                            $approvedTasks = \App\Models\Task::where('assigned_to', $worker->id)->where('status', 'approved')->count();
                        @endphp
                        <div class="text-sm">
                            <div class="text-gray-900">{{ $completedTasks }} completadas</div>
                            <div class="text-emerald-600">{{ $approvedTasks }} aprobadas</div>
                        </div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-900">{{ $worker->created_at->format('d/m/Y H:i') }}</div>
                    </td>
                    <td class="py-3 pr-4 text-right">
                        <div class="flex items-center gap-1 justify-end">
                            <!-- Ver detalles -->
                            <a href="{{ route('admin.workers.show', $worker) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 border border-blue-200 rounded hover:bg-blue-50 text-blue-600" 
                               title="Ver detalles">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            
                            <!-- Ver tareas -->
                            <a href="{{ route('admin.workers.tasks', $worker) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 border border-purple-200 rounded hover:bg-purple-50 text-purple-600" 
                               title="Ver tareas">
                                <i data-lucide="clipboard-list" class="w-4 h-4"></i>
                            </a>
                            
                            <!-- Generar Reporte -->
                            <a href="{{ route('admin.workers.report', $worker) }}" 
                               class="inline-flex items-center justify-center w-9 h-9 border-2 border-green-500 rounded-lg hover:bg-green-100 text-green-700 bg-green-50 shadow-sm" 
                               title="Generar Reporte Automático">
                                <i data-lucide="file-text" class="w-5 h-5"></i>
                            </a>
                            
                            <!-- Editar -->
                            <button class="edit-worker-btn inline-flex items-center justify-center w-8 h-8 border border-emerald-200 rounded hover:bg-emerald-50 text-emerald-600" 
                                    title="Editar"
                                    data-worker-id="{{ $worker->id }}"
                                    data-worker-name="{{ $worker->name }}"
                                    data-worker-email="{{ $worker->email }}"
                                    data-worker-status="{{ $worker->email_verified_at ? 'active' : 'inactive' }}">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </button>
                            
                            <!-- Recuperar credenciales -->
                            <form method="POST" action="{{ route('admin.workers.reset-password', $worker) }}" class="inline" data-confirm="true" data-message="¿Restablecer la contraseña de este trabajador? Se generará una nueva contraseña temporal.">
                                @csrf
                                <button class="inline-flex items-center justify-center w-8 h-8 border border-orange-200 rounded hover:bg-orange-50 text-orange-600" title="Recuperar credenciales">
                                    <i data-lucide="key" class="w-4 h-4"></i>
                                </button>
                            </form>
                            
                            
                            <!-- Eliminar -->
                            @if($worker->email_verified_at)
                                <button disabled class="inline-flex items-center justify-center w-8 h-8 border border-gray-200 rounded bg-gray-100 text-gray-400 cursor-not-allowed opacity-60" title="No se puede eliminar un trabajador activo. Debe desactivarlo primero.">
                                    <i data-lucide="trash" class="w-4 h-4"></i>
                                </button>
                            @else
                                <form method="POST" action="{{ route('admin.workers.destroy', $worker) }}" class="inline" data-confirm="true" data-message="¿Eliminar este trabajador? Esta acción no se puede deshacer.">
                                    @csrf
                                    @method('DELETE')
                                    <button class="inline-flex items-center justify-center w-8 h-8 border border-red-200 rounded hover:bg-red-50 text-red-600" title="Eliminar">
                                        <i data-lucide="trash" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-6 text-center text-emerald-800/70">No hay trabajadores registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $workers->links() }}</div>
</div>

<!-- Modal de edición -->
<div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-6 w-full max-w-md mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-emerald-700">Editar Trabajador</h3>
            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form id="editWorkerForm" class="space-y-4">
            @csrf
            @method('PUT')
            
            <!-- Nombre -->
            <div>
                <label for="editName" class="block text-sm font-medium text-emerald-700 mb-2">Nombre Completo *</label>
                <input type="text" name="name" id="editName" required 
                       class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('name') border-red-500 @enderror">
                <div id="editNameError" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>

            <!-- Email -->
            <div>
                <label for="editEmail" class="block text-sm font-medium text-emerald-700 mb-2">Email *</label>
                <input type="email" name="email" id="editEmail" required 
                       class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('email') border-red-500 @enderror">
                <div id="editEmailError" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>

            <!-- Información del Sistema -->
            <div class="bg-gray-50 rounded p-4">
                <h4 class="text-sm font-semibold text-emerald-700 mb-2">Información del Sistema</h4>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Estado:</span>
                        <select name="status" id="editStatusSelect" class="w-auto border border-emerald-200 rounded px-2 py-1 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="active">Activo</option>
                            <option value="inactive">Inactivo</option>
                        </select>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Registrado:</span>
                        <span id="editRegistered" class="text-sm text-gray-900"></span>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors">
                    <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                    Actualizar Trabajador
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentWorkerId = null;

function openEditModal(id, name, email, status) {
    currentWorkerId = id;
    
    // Llenar el formulario
    document.getElementById('editName').value = name;
    document.getElementById('editEmail').value = email;
    
    // Actualizar el select de estado
    const statusSelect = document.getElementById('editStatusSelect');
    statusSelect.value = status;
    
    // Obtener fecha de registro del trabajador actual
    const workerRow = document.querySelector(`tr[data-worker-id="${id}"]`);
    const registeredDate = workerRow.querySelector('td:nth-child(5) div').textContent;
    document.getElementById('editRegistered').textContent = registeredDate;
    
    // Limpiar errores anteriores
    clearEditErrors();
    
    // Mostrar modal
    document.getElementById('editModal').style.display = 'flex';
}

// Agregar event listeners para los botones de editar
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.edit-worker-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-worker-id');
            const name = this.getAttribute('data-worker-name');
            const email = this.getAttribute('data-worker-email');
            const status = this.getAttribute('data-worker-status');
            
            openEditModal(id, name, email, status);
        });
    });
});

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
    currentWorkerId = null;
    clearEditErrors();
}

function clearEditErrors() {
    document.getElementById('editNameError').classList.add('hidden');
    document.getElementById('editEmailError').classList.add('hidden');
    document.getElementById('editNameError').textContent = '';
    document.getElementById('editEmailError').textContent = '';
}

// Manejar envío del formulario
document.getElementById('editWorkerForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!currentWorkerId) return;
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    try {
        // Mostrar estado de carga
        submitButton.disabled = true;
        submitButton.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 inline mr-2 animate-spin"></i>Actualizando...';
        
        const response = await fetch(`/admin/workers/${currentWorkerId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                _method: 'PUT',
                name: formData.get('name'),
                email: formData.get('email'),
                status: document.getElementById('editStatusSelect').value
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Actualizar la fila en la tabla
            updateWorkerRow(currentWorkerId, data.worker);
            
            // Cerrar modal
            closeEditModal();
            
            // Mostrar notificación de éxito
            showNotification(data.message, 'success');
        } else {
            // Mostrar errores de validación
            if (data.errors) {
                if (data.errors.name) {
                    showEditError('editNameError', data.errors.name[0]);
                }
                if (data.errors.email) {
                    showEditError('editEmailError', data.errors.email[0]);
                }
            } else {
                showNotification(data.message || 'Error al actualizar el trabajador', 'error');
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

function showEditError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    errorElement.textContent = message;
    errorElement.classList.remove('hidden');
}

function updateWorkerRow(workerId, workerData) {
    const workerRow = document.querySelector(`tr[data-worker-id="${workerId}"]`);
    if (workerRow) {
        // Actualizar nombre
        workerRow.querySelector('.worker-name').textContent = workerData.name;
        
        // Actualizar email
        workerRow.querySelector('.worker-email').textContent = workerData.email;
        
        // Actualizar estado
        const statusBadge = workerRow.querySelector('.status-badge span');
        if (workerData.status === 'active') {
            statusBadge.className = 'px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700';
            statusBadge.textContent = 'Activo';
        } else {
            statusBadge.className = 'px-2 py-1 text-xs rounded bg-gray-100 text-gray-700';
            statusBadge.textContent = 'Inactivo';
        }
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

// Cerrar modal al hacer clic fuera de él
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>
@endsection
