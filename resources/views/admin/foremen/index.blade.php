@extends('admin.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Gestión de Mayordomos</h2>
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
            <br><small>Comparta esta contraseña con el mayordomo de forma segura.</small>
        </div>
    @endif

    <!-- Botón para agregar nuevo mayordomo -->
    <div class="flex justify-between items-center mb-4">
        <div></div>
        <a href="{{ route('admin.foremen.create') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Nuevo Mayordomo</span>
        </a>
    </div>

    <!-- Filtros de búsqueda -->
    <form method="GET" class="mb-4 flex gap-2 items-end">
        <div class="flex-1">
            <label class="block text-sm mb-1 text-emerald-800">Buscar por nombre o email</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar mayordomos..." class="w-full border border-emerald-200 rounded px-3 py-2" />
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

    <!-- Tabla de mayordomos -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-emerald-800 border-b">
                    <th class="py-3 pr-4">Nombre</th>
                    <th class="py-3 pr-4">Email</th>
                    <th class="py-3 pr-4">Estado</th>
                    <th class="py-3 pr-4">Fecha de Registro</th>
                    <th class="py-3 pr-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($foremen as $foreman)
                <tr class="border-b hover:bg-gray-50" data-foreman-id="{{ $foreman->id }}">
                    <td class="py-3 pr-4">
                        <div class="font-medium text-gray-900 foreman-name">{{ $foreman->name }}</div>
                    </td>
                    <td class="py-3 pr-4 foreman-email">
                        {{ $foreman->email }}
                    </td>
                    <td class="py-3 pr-4 status-badge">
                        @if($foreman->email_verified_at)
                            <span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Activo</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Inactivo</span>
                        @endif
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-900">{{ $foreman->created_at->format('d/m/Y H:i') }}</div>
                    </td>
                    <td class="py-3 pr-4 text-right">
                        <div class="flex items-center gap-1 justify-end">
                            <!-- Ver detalles -->
                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 border border-blue-200 rounded hover:bg-blue-50 text-blue-600 view-foreman-btn" 
                                    data-foreman-id="{{ $foreman->id }}"
                                    data-foreman-name="{{ $foreman->name }}"
                                    data-foreman-email="{{ $foreman->email }}"
                                    data-foreman-status="{{ $foreman->email_verified_at ? 'Activo' : 'Inactivo' }}"
                                    data-foreman-created="{{ $foreman->created_at->format('d/m/Y H:i') }}"
                                    data-foreman-updated="{{ $foreman->updated_at->format('d/m/Y H:i') }}"
                                    title="Ver detalles">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                            
                            <!-- Editar -->
                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 border border-emerald-200 rounded hover:bg-emerald-50 text-emerald-600 edit-foreman-btn" 
                                    data-foreman-id="{{ $foreman->id }}"
                                    data-foreman-name="{{ $foreman->name }}"
                                    data-foreman-email="{{ $foreman->email }}"
                                    data-foreman-status="{{ $foreman->email_verified_at ? 'active' : 'inactive' }}"
                                    data-foreman-created="{{ $foreman->created_at->format('d/m/Y H:i') }}"
                                    data-foreman-updated="{{ $foreman->updated_at->format('d/m/Y H:i') }}"
                                    title="Editar">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </button>
                            
                            <!-- Eliminar -->
                            <form method="POST" action="{{ route('admin.foremen.destroy', $foreman) }}" class="inline" data-confirm="true" data-message="¿Eliminar este mayordomo? Esta acción no se puede deshacer.">
                                @csrf
                                @method('DELETE')
                                <button class="inline-flex items-center justify-center w-8 h-8 border border-red-200 rounded hover:bg-red-50 text-red-600" title="Eliminar">
                                    <i data-lucide="trash" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-6 text-center text-emerald-800/70">No hay mayordomos registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $foremen->links() }}</div>
</div>

<!-- Modal de detalles -->
<div id="viewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-6 w-full max-w-2xl mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-emerald-700">Detalles del Mayordomo</h3>
            <button type="button" onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <div class="space-y-6">
            <!-- Información Básica -->
            <div class="bg-gray-50 rounded p-4">
                <h4 class="text-md font-semibold text-emerald-700 mb-3">Información Básica</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre</label>
                        <p id="viewName" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <p id="viewEmail" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estado</label>
                        <p id="viewStatus" class="text-sm mt-1"></p>
                    </div>
                </div>
            </div>
            
            <!-- Información del Sistema -->
            <div class="bg-gray-50 rounded p-4">
                <h4 class="text-md font-semibold text-emerald-700 mb-3">Información del Sistema</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha de Registro</label>
                        <p id="viewCreated" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Última actualización</label>
                        <p id="viewUpdated" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Botón de cerrar -->
        <div class="mt-6 flex justify-end">
            <button type="button" onclick="closeViewModal()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded">
                <i data-lucide="x" class="w-4 h-4 inline mr-2"></i>
                Cerrar
            </button>
        </div>
    </div>
</div>

<!-- Modal de edición -->
<div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-emerald-700">Editar Mayordomo</h3>
            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="editStatus" name="status" value="">
            
            <div class="space-y-6">
                <!-- Información Básica -->
                <div class="bg-gray-50 rounded p-4">
                    <h4 class="text-md font-semibold text-emerald-700 mb-3">Información Básica</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nombre -->
                        <div>
                            <label for="editName" class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                            <input type="text" id="editName" name="name" 
                                   class="w-full border border-emerald-200 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" 
                                   placeholder="Ingrese el nombre completo" required>
                            <div id="editNameError" class="text-red-500 text-xs mt-1" style="display: none;"></div>
                        </div>
                        
                        <!-- Email -->
                        <div>
                            <label for="editEmail" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                            <input type="email" id="editEmail" name="email" 
                                   class="w-full border border-emerald-200 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" 
                                   placeholder="correo@ejemplo.com" required>
                            <div id="editEmailError" class="text-red-500 text-xs mt-1" style="display: none;"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Información del Sistema -->
                <div class="bg-gray-50 rounded p-4">
                    <h4 class="text-md font-semibold text-emerald-700 mb-3">Información del Sistema</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Estado -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado Actual</label>
                            <div class="flex items-center gap-2">
                                <span id="editStatusBadge" class="px-2 py-1 text-xs rounded"></span>
                            </div>
                        </div>
                        
                        <!-- Fecha de Registro -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Registro</label>
                            <p id="editCreated" class="text-sm text-gray-900"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Acciones Adicionales -->
                <div class="bg-gray-50 rounded p-4">
                    <h4 class="text-md font-semibold text-emerald-700 mb-4">Acciones Adicionales</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <!-- Cambiar Estado -->
                        <div class="bg-white border border-emerald-200 rounded p-4">
                            <label class="block text-sm font-medium text-emerald-700 mb-2">Estado</label>
                            <select id="statusSelect" class="w-full border border-emerald-200 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                                <option value="active">Activo</option>
                                <option value="inactive">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Botones de acción -->
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeEditModal()" 
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 inline-flex items-center gap-2">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    <span>Cancelar</span>
                </button>
                
                <button type="submit" id="saveBtn"
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded inline-flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Guardar Cambios</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentForemanId = null;

// Función para abrir el modal de detalles
function openViewModal(id, name, email, status, created, updated) {
    // Llenar los campos del modal de detalles
    document.getElementById('viewName').textContent = name;
    document.getElementById('viewEmail').textContent = email;
    document.getElementById('viewCreated').textContent = created;
    document.getElementById('viewUpdated').textContent = updated;
    
    // Configurar el estado con el badge apropiado
    const statusElement = document.getElementById('viewStatus');
    if (status === 'Activo') {
        statusElement.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Activo</span>';
    } else {
        statusElement.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Inactivo</span>';
    }
    
    // Establecer el ID del mayordomo actual en el modal
    const viewModal = document.getElementById('viewModal');
    viewModal.setAttribute('data-current-foreman-id', id);
    
    // Mostrar el modal
    viewModal.style.display = 'flex';
}

// Función para cerrar el modal de detalles
function closeViewModal() {
    document.getElementById('viewModal').style.display = 'none';
}

// Función para abrir el modal de edición
function openEditModal(id, name, email, status, created, updated) {
    currentForemanId = id;
    
    // Llenar los campos del formulario
    document.getElementById('editName').value = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editCreated').textContent = created;
    
    // Configurar el estado con el badge apropiado
    const statusBadge = document.getElementById('editStatusBadge');
    const statusSelect = document.getElementById('statusSelect');
    
    if (status === 'active') {
        statusBadge.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Activo</span>';
        statusSelect.value = 'active';
    } else {
        statusBadge.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Inactivo</span>';
        statusSelect.value = 'inactive';
    }
    
    // Asegurar que el select esté habilitado y limpio
    statusSelect.disabled = false;
    statusSelect.style.opacity = '1';
    
    // Configurar el campo oculto de estado
    document.getElementById('editStatus').value = status;
    
    // Configurar la acción del formulario
    document.getElementById('editForm').action = `/admin/foremen/${id}`;
    
    // Limpiar errores anteriores
    clearEditErrors();
    
    // Mostrar el modal
    document.getElementById('editModal').style.display = 'flex';
}

// Función para cerrar el modal de edición
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
    currentForemanId = null;
    clearEditErrors();
}

// Función para limpiar errores del formulario de edición
function clearEditErrors() {
    document.getElementById('editNameError').style.display = 'none';
    document.getElementById('editEmailError').style.display = 'none';
    document.getElementById('editName').classList.remove('border-red-500');
    document.getElementById('editEmail').classList.remove('border-red-500');
}

// Función para mostrar errores en el formulario de edición
function showEditErrors(errors) {
    clearEditErrors();
    
    if (errors.name) {
        document.getElementById('editNameError').textContent = errors.name[0];
        document.getElementById('editNameError').style.display = 'block';
        document.getElementById('editName').classList.add('border-red-500');
    }
    
    if (errors.email) {
        document.getElementById('editEmailError').textContent = errors.email[0];
        document.getElementById('editEmailError').style.display = 'block';
        document.getElementById('editEmail').classList.add('border-red-500');
    }
}

// Función para actualizar la fila en la tabla
function updateTableRow(id, name, email, status) {
    const row = document.querySelector(`tr[data-foreman-id="${id}"]`);
    
    if (row) {
        // Actualizar nombre
        const nameCell = row.querySelector('.foreman-name');
        if (nameCell) {
            nameCell.textContent = name;
        }
        
        // Actualizar email
        const emailCell = row.querySelector('.foreman-email');
        if (emailCell) {
            emailCell.textContent = email;
        }
        
        // Actualizar estado con animación suave
        const statusBadge = row.querySelector('.status-badge');
        
        if (statusBadge) {
            // Agregar clase de transición
            statusBadge.style.transition = 'all 0.3s ease';
            
            if (status === 'active') {
                statusBadge.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Activo</span>';
            } else {
                statusBadge.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Inactivo</span>';
            }
            
            // Efecto de parpadeo para indicar cambio
            statusBadge.style.opacity = '0.5';
            setTimeout(() => {
                statusBadge.style.opacity = '1';
            }, 150);
        }
        
        // Actualizar también el atributo data-status si existe
        row.setAttribute('data-status', status);
        
        // Actualizar los atributos data de los botones de acción
        const viewBtn = row.querySelector('.view-foreman-btn');
        const editBtn = row.querySelector('.edit-foreman-btn');
        
        if (viewBtn) {
            viewBtn.setAttribute('data-foreman-status', status === 'active' ? 'Activo' : 'Inactivo');
        }
        
        if (editBtn) {
            editBtn.setAttribute('data-foreman-status', status);
        }
        
        // Si el modal de vista está abierto para este mayordomo, actualizarlo también
        const viewModal = document.getElementById('viewModal');
        if (viewModal && viewModal.style.display !== 'none') {
            const viewModalId = viewModal.getAttribute('data-current-foreman-id');
            if (viewModalId === id.toString()) {
                updateViewModalStatus(status);
            }
        }
    }
}

// Función para actualizar el estado en el modal de vista
function updateViewModalStatus(status) {
    const viewStatusElement = document.getElementById('viewStatus');
    if (viewStatusElement) {
        if (status === 'active') {
            viewStatusElement.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Activo</span>';
        } else {
            viewStatusElement.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Inactivo</span>';
        }
    }
}

// Inicialización cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    // Asegurar que los modales estén ocultos por defecto
    const viewModal = document.getElementById('viewModal');
    const editModal = document.getElementById('editModal');
    if (viewModal) viewModal.style.display = 'none';
    if (editModal) editModal.style.display = 'none';
    
    // Agregar eventos a los botones de ver detalles
    document.querySelectorAll('.view-foreman-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-foreman-id');
            const name = this.getAttribute('data-foreman-name');
            const email = this.getAttribute('data-foreman-email');
            const status = this.getAttribute('data-foreman-status');
            const created = this.getAttribute('data-foreman-created');
            const updated = this.getAttribute('data-foreman-updated');
            
            openViewModal(id, name, email, status, created, updated);
        });
    });
    
    // Agregar eventos a los botones de editar
    document.querySelectorAll('.edit-foreman-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-foreman-id');
            const name = this.getAttribute('data-foreman-name');
            const email = this.getAttribute('data-foreman-email');
            const status = this.getAttribute('data-foreman-status');
            const created = this.getAttribute('data-foreman-created');
            const updated = this.getAttribute('data-foreman-updated');
            
            openEditModal(id, name, email, status, created, updated);
        });
    });
    
    // Manejar envío del formulario de edición
    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Asegurar que el campo status esté siempre presente
        const statusSelectValue = document.getElementById('statusSelect').value;
        formData.set('status', statusSelectValue);
        
        // Debug temporal
        console.log('Sending status:', statusSelectValue);
        console.log('FormData contents:');
        for (let [key, value] of formData.entries()) {
            console.log(key + ':', value);
        }
        
        // Verificar que el campo status esté presente
        if (!formData.has('status')) {
            console.error('Status field is missing!');
            formData.append('status', statusSelectValue);
        }
        
        // Verificar una vez más
        console.log('Final check - FormData has status:', formData.has('status'));
        console.log('Final check - FormData status value:', formData.get('status'));
        
        // Forzar el envío del campo status si no está presente
        if (!formData.has('status')) {
            console.error('Status field is still missing! Forcing addition...');
            formData.append('status', statusSelectValue);
        }
        
        // Verificar una última vez
        console.log('Ultimate check - FormData has status:', formData.has('status'));
        console.log('Ultimate check - FormData status value:', formData.get('status'));
        
        // Forzar el envío del campo status si no está presente
        if (!formData.has('status')) {
            console.error('Status field is still missing! Forcing addition...');
            formData.append('status', statusSelectValue);
        }
        
        
        const saveBtn = document.getElementById('saveBtn');
        const originalText = saveBtn.innerHTML;
        
        // Mostrar loading
        saveBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i><span>Guardando...</span>';
        saveBtn.disabled = true;
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar la tabla
                updateTableRow(currentForemanId, data.foreman.name, data.foreman.email, data.foreman.status);
                
                // Cerrar modal
                closeEditModal();
                
                // Mostrar mensaje de éxito
                showSuccessMessage(data.message);
            } else {
                // Mostrar errores
                if (data.errors) {
                    showEditErrors(data.errors);
                } else {
                    showErrorMessage(data.message || 'Error al actualizar el mayordomo');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorMessage('Error de conexión. Intente nuevamente.');
        })
        .finally(() => {
            // Restaurar botón
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
        });
    });
    
    // Manejar botón de restablecer contraseña
    
    // Manejar cambio de estado en el select (solo visual, no hace petición)
    document.getElementById('statusSelect').addEventListener('change', function() {
        if (!currentForemanId) return;
        
        const newStatus = this.value;
        const statusBadge = document.getElementById('editStatusBadge');
        
        // Actualizar solo el badge visualmente
        if (newStatus === 'active') {
            statusBadge.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Activo</span>';
        } else {
            statusBadge.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Inactivo</span>';
        }
        
        // Actualizar el campo oculto para el formulario
        document.getElementById('editStatus').value = newStatus;
    });
    
    // Agregar evento de tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeViewModal();
            closeEditModal();
        }
    });
    
    // Agregar evento de clic fuera de los modales
    [viewModal, editModal].forEach(modal => {
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    if (modal.id === 'viewModal') closeViewModal();
                    if (modal.id === 'editModal') closeEditModal();
                }
            });
        }
    });
});

// Funciones de utilidad para mostrar mensajes
function showSuccessMessage(message) {
    // Crear toast de éxito
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 bg-emerald-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    toast.innerHTML = `<i data-lucide="check-circle" class="w-5 h-5 inline mr-2"></i>${message}`;
    document.body.appendChild(toast);
    
    // Remover después de 3 segundos
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

function showErrorMessage(message) {
    // Crear toast de error
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    toast.innerHTML = `<i data-lucide="x-circle" class="w-5 h-5 inline mr-2"></i>${message}`;
    document.body.appendChild(toast);
    
    // Remover después de 5 segundos
    setTimeout(() => {
        toast.remove();
    }, 5000);
}

</script>
@endsection
