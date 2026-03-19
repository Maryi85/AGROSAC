@extends('admin.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Gestión de Mayordomos</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-4">
    <!-- Botón para agregar nuevo mayordomo -->
    {{-- Responsive: stacks on mobile, side-by-side on sm+ --}}
    <div class="mb-6 flex flex-wrap gap-2 justify-between items-center">
        <a href="{{ route('admin.foremen.create') }}" class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-5 py-2.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded-lg font-medium transition-colors">
            <i data-lucide="plus" class="w-5 h-5"></i>
            <span>Nuevo Mayordomo</span>
        </a>
        <a href="{{ route('admin.foremen.pdf', request()->query()) }}" class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-4 py-2.5 bg-red-100 hover:bg-red-200 text-red-700 border border-red-200 rounded-lg font-medium transition-colors">
            <i data-lucide="file-text" class="w-5 h-5"></i>
            <span>Descargar PDF</span>
        </a>
    </div>

    <!-- Filtros de búsqueda -->
    {{-- Responsive: columna en móvil, fila en sm+ --}}
    <form method="GET" class="mb-4 flex flex-col sm:flex-row gap-2 sm:items-end">
        <div class="flex-1">
            <label class="block text-sm mb-1 text-emerald-800">Buscar por nombre o email</label>
            <x-search-bar placeholder="Buscar mayordomos..." :with-form="false" />
        </div>
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Estado</label>
            <select name="status" class="w-full sm:w-auto border border-emerald-200 rounded px-3 py-2">
                <option value="all">Todos los estados</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activos</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivos</option>
            </select>
        </div>
        <button type="submit" class="w-full sm:w-auto px-3 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded inline-flex items-center justify-center gap-2 transition-colors">
            <i data-lucide="search" class="w-4 h-4"></i>
            <span>Filtrar</span>
        </button>
    </form>

    {{-- Tabla desktop (se oculta en móvil) --}}
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-emerald-800 border-b">
                    <th class="py-3 pr-4">Foto</th>
                    <th class="py-3 pr-4">Nombre</th>
                    <th class="py-3 pr-4">Email</th>
                    <th class="py-3 pr-4">Teléfono</th>
                    <th class="py-3 pr-4">Estado</th>
                    <th class="py-3 pr-4">Fecha de Registro</th>
                    <th class="py-3 pr-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($foremen as $foreman)
                <tr class="border-b hover:bg-gray-50" data-foreman-id="{{ $foreman->id }}">
                    <td class="py-3 pr-4">
                        @if($foreman->photo)
                            <img src="{{ storage_asset($foreman->photo) }}" alt="Foto" class="h-10 w-10 rounded-full object-cover border border-gray-200 foreman-photo">
                        @else
                            <div class="h-10 w-10 rounded-full border border-dashed border-gray-200 bg-gray-50 flex items-center justify-center text-xs text-gray-400 foreman-photo-placeholder">—</div>
                        @endif
                    </td>
                    <td class="py-3 pr-4">
                        <div class="font-medium text-gray-900 foreman-name">{{ $foreman->name }}</div>
                    </td>
                    <td class="py-3 pr-4 foreman-email">{{ $foreman->email }}</td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-800 foreman-phone">{{ $foreman->phone ?? '—' }}</div>
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
                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 border border-blue-200 rounded hover:bg-blue-50 text-blue-600 view-foreman-btn"
                                    data-foreman-id="{{ $foreman->id }}"
                                    data-foreman-name="{{ $foreman->name }}"
                                    data-foreman-email="{{ $foreman->email }}"
                                    data-foreman-status="{{ $foreman->email_verified_at ? 'Activo' : 'Inactivo' }}"
                                    data-foreman-created="{{ $foreman->created_at->format('d/m/Y H:i') }}"
                                    data-foreman-updated="{{ $foreman->updated_at->format('d/m/Y H:i') }}"
                                    data-foreman-photo="{{ $foreman->photo ? storage_asset($foreman->photo) : '' }}"
                                    title="Ver detalles">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 border border-emerald-200 rounded hover:bg-emerald-50 text-emerald-600 edit-foreman-btn"
                                    data-foreman-id="{{ $foreman->id }}"
                                    data-foreman-name="{{ $foreman->name }}"
                                    data-foreman-email="{{ $foreman->email }}"
                                    data-foreman-phone="{{ $foreman->phone }}"
                                    data-foreman-status="{{ $foreman->email_verified_at ? 'active' : 'inactive' }}"
                                    data-foreman-created="{{ $foreman->created_at->format('d/m/Y H:i') }}"
                                    data-foreman-updated="{{ $foreman->updated_at->format('d/m/Y H:i') }}"
                                    title="Editar">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </button>
                            <div class="delete-action-container">
                                @if($foreman->email_verified_at)
                                    <button disabled class="inline-flex items-center justify-center w-8 h-8 border border-gray-200 rounded bg-gray-100 text-gray-400 cursor-not-allowed opacity-60" title="Desactive al mayordomo antes de eliminar">
                                        <i data-lucide="trash" class="w-4 h-4"></i>
                                    </button>
                                @else
                                    <form method="POST" action="{{ route('admin.foremen.destroy', $foreman) }}" class="inline" data-confirm="true" data-message="¿Eliminar este mayordomo? Esta acción no se puede deshacer.">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex items-center justify-center w-8 h-8 border border-red-200 rounded hover:bg-red-50 text-red-600" title="Eliminar">
                                            <i data-lucide="trash" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-6 text-center text-emerald-800/70">No hay mayordomos registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Tarjetas móvil (se ocultan en md+) --}}
    <div class="md:hidden space-y-3" id="foremen-list">
        @forelse ($foremen as $foreman)
        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-shadow" data-foreman-id="{{ $foreman->id }}">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                    @if($foreman->photo)
                        <img src="{{ storage_asset($foreman->photo) }}" alt="Foto"
                             class="h-12 w-12 rounded-full object-cover border border-gray-200 foreman-photo">
                    @else
                        <div class="h-12 w-12 rounded-full border border-dashed border-gray-200 bg-gray-50 flex items-center justify-center text-gray-400 foreman-photo-placeholder">
                            <i data-lucide="user" class="w-5 h-5"></i>
                        </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <p class="font-semibold text-gray-900 truncate foreman-name">{{ $foreman->name }}</p>
                        <span class="status-badge flex-shrink-0">
                            @if($foreman->email_verified_at)
                                <span class="px-2 py-0.5 text-xs rounded-full bg-emerald-100 text-emerald-700 font-medium">Activo</span>
                            @else
                                <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-500 font-medium">Inactivo</span>
                            @endif
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 truncate foreman-email">{{ $foreman->email }}</p>
                </div>
            </div>
            <div class="mt-2 flex items-center gap-1.5 text-xs text-gray-500">
                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                <span>{{ $foreman->created_at->format('d/m/Y') }}</span>
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
                <button type="button"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-blue-200 text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors view-foreman-btn"
                        data-foreman-id="{{ $foreman->id }}"
                        data-foreman-name="{{ $foreman->name }}"
                        data-foreman-email="{{ $foreman->email }}"
                        data-foreman-status="{{ $foreman->email_verified_at ? 'Activo' : 'Inactivo' }}"
                        data-foreman-created="{{ $foreman->created_at->format('d/m/Y H:i') }}"
                        data-foreman-updated="{{ $foreman->updated_at->format('d/m/Y H:i') }}"
                        data-foreman-photo="{{ $foreman->photo ? storage_asset($foreman->photo) : '' }}">
                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                    Ver
                </button>
                <button type="button"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-emerald-200 text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors edit-foreman-btn"
                        data-foreman-id="{{ $foreman->id }}"
                        data-foreman-name="{{ $foreman->name }}"
                        data-foreman-email="{{ $foreman->email }}"
                        data-foreman-phone="{{ $foreman->phone }}"
                        data-foreman-status="{{ $foreman->email_verified_at ? 'active' : 'inactive' }}"
                        data-foreman-created="{{ $foreman->created_at->format('d/m/Y H:i') }}"
                        data-foreman-updated="{{ $foreman->updated_at->format('d/m/Y H:i') }}">
                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                    Editar
                </button>
                <div class="delete-action-container">
                    @if($foreman->email_verified_at)
                        <button disabled
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-gray-200 text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed opacity-60"
                                title="Desactive al mayordomo antes de eliminar">
                            <i data-lucide="trash" class="w-3.5 h-3.5"></i>
                            Eliminar
                        </button>
                    @else
                        <form method="POST" action="{{ route('admin.foremen.destroy', $foreman) }}"
                              class="inline" data-confirm="true"
                              data-message="¿Eliminar este mayordomo? Esta acción no se puede deshacer.">
                            @csrf
                            @method('DELETE')
                            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-200 text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                                <i data-lucide="trash" class="w-3.5 h-3.5"></i>
                                Eliminar
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-10 text-gray-400">
            <i data-lucide="users" class="w-10 h-10 mx-auto mb-2 opacity-40"></i>
            <p class="text-sm">No hay mayordomos registrados</p>
        </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $foremen->links() }}</div>
</div>

<!-- Modal de detalles -->
<div id="viewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    {{-- max-h + overflow-y para pantallas pequeñas --}}
    <div class="bg-white border rounded p-5 sm:p-6 w-full max-w-2xl mx-4 max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-emerald-700">Detalles del Mayordomo</h3>
            <button type="button" onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <div class="space-y-6">
            <!-- Foto y Datos Personales -->
            {{-- Apila en móvil, lado a lado en sm+ --}}
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 sm:gap-6">
                <!-- Foto -->
                <div class="flex-shrink-0">
                    <div id="viewPhotoContainer" class="w-32 h-32 rounded-lg bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center overflow-hidden">
                        <img id="viewPhoto" src="" alt="Foto" class="w-full h-full object-cover" style="display: none;">
                        <i id="viewPhotoIcon" data-lucide="user" class="w-16 h-16 text-emerald-600"></i>
                    </div>
                </div>
                
                <!-- Información Personal -->
                <div class="flex-1">
                    <h4 id="viewName" class="text-2xl font-bold text-gray-900 mb-2"></h4>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <i data-lucide="mail" class="w-4 h-4 text-gray-500"></i>
                            <span id="viewEmail" class="text-gray-700"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="calendar" class="w-4 h-4 text-gray-500"></i>
                            <div class="text-sm">
                            <span class="text-gray-600">Registrado: </span>
                            <span id="viewCreated" class="text-gray-700"></span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="clock" class="w-4 h-4 text-gray-500"></i>
                            <div class="text-sm">
                            <span class="text-gray-600">Actualizado: </span>
                            <span id="viewUpdated" class="text-gray-700"></span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="circle-check" class="w-4 h-4 text-gray-500"></i>
                            <span class="text-gray-600 text-sm">Estado: </span>
                            <span id="viewStatus" class="px-2 py-1 rounded text-xs font-medium"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Botón de cerrar -->
        <div class="mt-6 flex justify-end">
            <button type="button" onclick="closeViewModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-200 rounded transition-colors">
                <i data-lucide="x" class="w-4 h-4 inline mr-2"></i>
                Cerrar
            </button>
        </div>
    </div>
</div>

<!-- Modal de edición -->
<div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-5 sm:p-6 w-full max-w-md mx-4 max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-emerald-700">Editar Mayordomo</h3>
            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form id="editForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Nombre -->
            <div>
                <label for="editName" class="block text-sm font-medium text-emerald-700 mb-2">Nombre Completo *</label>
                <input type="text" name="name" id="editName" required
                       class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <div id="editNameError" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>

            <!-- Email -->
            <div>
                <label for="editEmail" class="block text-sm font-medium text-emerald-700 mb-2">Email *</label>
                <input type="email" name="email" id="editEmail" required
                       class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <div id="editEmailError" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>

            <!-- Teléfono -->
            <div>
                <label for="editPhone" class="block text-sm font-medium text-emerald-700 mb-2">Teléfono</label>
                <input type="tel" name="phone" id="editPhone"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                       class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <div id="editPhoneError" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>

            <!-- Foto -->
            <div>
                <label for="editPhoto" class="block text-sm font-medium text-emerald-700 mb-2">Foto (Opcional)</label>
                <input type="file" name="photo" id="editPhoto" accept="image/*"
                       class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                <div id="editPhotoError" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>

            <!-- Información del Sistema -->
            <div class="bg-gray-50 rounded p-4">
                <h4 class="text-sm font-semibold text-emerald-700 mb-2">Información del Sistema</h4>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Estado:</span>
                        <select name="status" id="statusSelect" class="w-auto border border-emerald-200 rounded px-2 py-1 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="active">Activo</option>
                            <option value="inactive">Inactivo</option>
                        </select>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Registrado:</span>
                        <span id="editCreated" class="text-sm text-gray-900"></span>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit" id="saveBtn" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors">
                    <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                    Actualizar Mayordomo
                </button>
            </div>
        </form>
    </div>
</div>


<script>
let currentForemanId = null;

// Función para abrir el modal de detalles
function openViewModal(id, name, email, status, created, updated, photo) {
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

    // Foto
    const photoImg = document.getElementById('viewPhoto');
    const photoIcon = document.getElementById('viewPhotoIcon');
    if (photo) {
        photoImg.src = photo;
        photoImg.style.display = 'block';
        photoIcon.style.display = 'none';
    } else {
        photoImg.style.display = 'none';
        photoIcon.style.display = 'block';
    }
    
    // Establecer el ID del mayordomo actual en el modal
    const viewModal = document.getElementById('viewModal');
    viewModal.setAttribute('data-current-foreman-id', id);
    
    // Mostrar el modal
    viewModal.style.display = 'flex';
    
    // Actualizar iconos de Lucide
    if (window.lucide) {
        window.lucide.createIcons();
    }
}

// Función para cerrar el modal de detalles
function closeViewModal() {
    document.getElementById('viewModal').style.display = 'none';
}

// Función para abrir el modal de edición
function openEditModal(id, name, email, phone, status, created, updated) {
    currentForemanId = id;
    
    // Llenar los campos del formulario
    document.getElementById('editName').value = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editPhone').value = phone || '';
    document.getElementById('editCreated').textContent = created;
    
    // Configurar el select de estado
    const statusSelect = document.getElementById('statusSelect');
    statusSelect.value = status;
    
    // Configurar la acción del formulario
    document.getElementById('editForm').action = `/admin/foremen/${id}`;
    
    // Limpiar errores anteriores
    clearEditErrors();
    
    // Mostrar el modal
    document.getElementById('editModal').style.display = 'flex';
    
    // Actualizar iconos de Lucide
    if (window.lucide) {
        window.lucide.createIcons();
    }
}

// Función para cerrar el modal de edición
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
    currentForemanId = null;
    clearEditErrors();
}

// Función para limpiar errores del formulario de edición
function clearEditErrors() {
    document.getElementById('editNameError').classList.add('hidden');
    document.getElementById('editEmailError').classList.add('hidden');
    document.getElementById('editNameError').textContent = '';
    document.getElementById('editEmailError').textContent = '';
}


// Función para mostrar errores en el formulario de edición
function showEditErrors(errors) {
    clearEditErrors();
    if (errors.name) {
        const el = document.getElementById('editNameError');
        el.textContent = errors.name[0];
        el.classList.remove('hidden');
    }
    if (errors.email) {
        const el = document.getElementById('editEmailError');
        el.textContent = errors.email[0];
        el.classList.remove('hidden');
    }
}

// Función para actualizar la TARJETA en la lista
function updateTableRow(id, name, email, phone, status, photoURL) {
    const card = document.querySelector(`[data-foreman-id="${id}"]`);
    if (!card) return;

    // Actualizar foto
    const photoImg = card.querySelector('.foreman-photo');
    const photoPlaceholder = card.querySelector('.foreman-photo-placeholder');
    if (photoURL) {
        if (photoImg) {
            photoImg.src = photoURL;
        } else if (photoPlaceholder) {
            const img = document.createElement('img');
            img.src = photoURL;
            img.alt = 'Foto';
            img.className = 'h-12 w-12 rounded-full object-cover border border-gray-200 foreman-photo';
            photoPlaceholder.replaceWith(img);
        }
    }

    // Actualizar nombre
    const nameEl = card.querySelector('.foreman-name');
    if (nameEl) nameEl.textContent = name;

    // Actualizar email
    const emailEl = card.querySelector('.foreman-email');
    if (emailEl) emailEl.textContent = email;

    // Actualizar estado (badge)
    const statusBadge = card.querySelector('.status-badge');
    if (statusBadge) {
        if (status === 'active') {
            statusBadge.innerHTML = '<span class="px-2 py-0.5 text-xs rounded-full bg-emerald-100 text-emerald-700 font-medium">Activo</span>';
        } else {
            statusBadge.innerHTML = '<span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-500 font-medium">Inactivo</span>';
        }
    }

    // Actualizar atributos data-* en botones
    const viewBtn = card.querySelector('.view-foreman-btn');
    const editBtn = card.querySelector('.edit-foreman-btn');

    if (viewBtn) {
        viewBtn.setAttribute('data-foreman-name', name);
        viewBtn.setAttribute('data-foreman-email', email);
        viewBtn.setAttribute('data-foreman-status', status === 'active' ? 'Activo' : 'Inactivo');
        if (photoURL) viewBtn.setAttribute('data-foreman-photo', photoURL);
    }

    if (editBtn) {
        editBtn.setAttribute('data-foreman-name', name);
        editBtn.setAttribute('data-foreman-email', email);
        editBtn.setAttribute('data-foreman-phone', phone || '');
        editBtn.setAttribute('data-foreman-status', status);
    }

    // Actualizar botón eliminar
    updateDeleteButton(card, status, `/admin/foremen/${id}`);

    // Sincronizar modal de vista si está abierto
    const viewModal = document.getElementById('viewModal');
    if (viewModal && viewModal.style.display !== 'none') {
        const viewModalId = viewModal.getAttribute('data-current-foreman-id');
        if (viewModalId === id.toString()) {
            document.getElementById('viewName').textContent = name;
            document.getElementById('viewEmail').textContent = email;
            updateViewModalStatus(status);
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
            const photo = this.getAttribute('data-foreman-photo');
            
            openViewModal(id, name, email, status, created, updated, photo);
        });
    });
    
    // Agregar eventos a los botones de editar
    document.querySelectorAll('.edit-foreman-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-foreman-id');
            const name = this.getAttribute('data-foreman-name');
            const email = this.getAttribute('data-foreman-email');
            const phone = this.getAttribute('data-foreman-phone'); // Need to ensure this attribute exists
            const status = this.getAttribute('data-foreman-status');
            const created = this.getAttribute('data-foreman-created');
            const updated = this.getAttribute('data-foreman-updated');
            
            openEditModal(id, name, email, phone, status, created, updated);
        });
    });
    
    // Manejar envío del formulario de edición
    document.getElementById('editForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        if (!currentForemanId) return;

        const formData = new FormData(this);
        formData.append('_method', 'PUT');
        formData.set('status', document.getElementById('statusSelect').value);

        const saveBtn = document.getElementById('saveBtn');
        const originalText = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 inline mr-2 animate-spin"></i>Actualizando...';

        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await response.json();

            if (data.success) {
                sessionStorage.setItem('toastMessage', data.message);
                window.location.reload();
            } else {
                if (data.errors) {
                    showEditErrors(data.errors);
                } else {
                    showErrorMessage(data.message || 'Error al actualizar el mayordomo');
                }
            }
        } catch (error) {
            showErrorMessage('Error de conexión. Intente nuevamente.');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
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
    // Restaurar Toast de sessionStorage
    if (sessionStorage.getItem('toastMessage')) {
        showSuccessMessage(sessionStorage.getItem('toastMessage'));
        sessionStorage.removeItem('toastMessage');
    }
});

// Funciones de utilidad para mostrar mensajes
function showSuccessMessage(message) {
    //Usar la función global de SweetAlert2 toast
    if (window.showSuccessAlert) {
        window.showSuccessAlert(message);
    }
}


function showErrorMessage(message) {
    // Usar la función global de SweetAlert2 toast
    if (window.showErrorAlert) {
        window.showErrorAlert(message);
    }
}

// Función para actualizar el botón de eliminar dinámicamente
function updateDeleteButton(row, status, destroyUrl) {
    const container = row.querySelector('.delete-action-container');
    if (!container) return;

    if (status === 'active') {
        container.innerHTML = `
            <button disabled class="inline-flex items-center justify-center w-8 h-8 border border-gray-200 rounded bg-gray-100 text-gray-400 cursor-not-allowed opacity-60" title="No se puede eliminar un mayordomo activo. Debe desactivarlo primero.">
                <i data-lucide="trash" class="w-4 h-4"></i>
            </button>
        `;
    } else {
        container.innerHTML = `
            <form method="POST" action="${destroyUrl}" class="inline" data-confirm="true" data-message="¿Eliminar este mayordomo? Esta acción no se puede deshacer.">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                <input type="hidden" name="_method" value="DELETE">
                <button class="inline-flex items-center justify-center w-8 h-8 border border-red-200 rounded hover:bg-red-50 text-red-600" title="Eliminar">
                    <i data-lucide="trash" class="w-4 h-4"></i>
                </button>
            </form>
        `;
    }
    
    // Reinicializar iconos de Lucide
    if (window.lucide) {
        window.lucide.createIcons();
    }
}

// Delegación de eventos para forms data-confirm generados dinámicamente (delete cards)
document.addEventListener('submit', function (e) {
    const form = e.target;
    if (form.getAttribute('data-confirm') === 'true') {
        e.preventDefault();
        const message = form.getAttribute('data-message') || '¿Confirmar acción?';
        Swal.fire({
            title: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
        }).then(result => {
            if (result.isConfirmed) form.submit();
        });
    }
}, true); // capture phase para interceptar antes del handler del layout
</script>
@endsection
