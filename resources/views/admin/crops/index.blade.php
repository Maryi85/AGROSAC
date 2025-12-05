@php
use Illuminate\Support\Facades\Storage;
@endphp

@extends('admin.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Gestión de Cultivos</h2>
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

    <!-- Botones de acción -->
    <div class="mb-6 flex justify-between items-center">
        <div class="flex gap-4">
            <a href="{{ route('admin.crops.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded-lg font-medium transition-colors">
                <i data-lucide="plus" class="w-5 h-5"></i>
                <span>Nuevo Cultivo</span>
            </a>
            <a href="{{ route('admin.crop-tracking.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-100 hover:bg-blue-200 text-blue-700 border border-blue-200 rounded-lg font-medium transition-colors">
                <i data-lucide="activity" class="w-5 h-5"></i>
                <span>Seguimiento de Cultivo</span>
            </a>
        </div>
        <a href="{{ route('admin.crops.pdf', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 border border-red-200 rounded-lg font-medium transition-colors">
            <i data-lucide="file-text" class="w-5 h-5"></i>
            <span>Descargar PDF</span>
        </a>
    </div>

    <!-- Filtros -->
    <form method="GET" class="mb-4 flex gap-2">
        <input type="text" name="q" value="{{ $search }}" placeholder="Buscar por nombre" class="border border-emerald-200 rounded px-3 py-2 flex-1" />
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

    <!-- Tabla de cultivos -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-emerald-800 border-b">
                    <th class="py-3 pr-4">Foto</th>
                    <th class="py-3 pr-4">Nombre</th>
                    <th class="py-3 pr-4">Variedad</th>
                    <th class="py-3 pr-4">Lote</th>
                    <th class="py-3 pr-4">Rendimiento</th>
                    <th class="py-3 pr-4">Estado</th>
                    <th class="py-3 pr-4">Tareas</th>
                    <th class="py-3 pr-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($crops as $crop)
                <tr class="border-b hover:bg-gray-50" data-crop-id="{{ $crop->id }}">
                    <td class="py-3 pr-4">
                        @if(!empty($crop->photo))
                            <img src="{{ asset('storage/' . $crop->photo) }}" alt="{{ $crop->name }}" class="w-16 h-16 object-cover rounded border border-emerald-200" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-16 h-16 bg-gray-100 rounded border border-gray-200 flex items-center justify-center hidden">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path></svg>
                            </div>
                        @else
                            <div class="w-16 h-16 bg-gray-100 rounded border border-gray-200 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path></svg>
                            </div>
                        @endif
                    </td>
                    <td class="py-3 pr-4">
                        <div>
                            <div class="font-medium text-gray-900 crop-name">{{ $crop->name }}</div>
                            @if($crop->description)
                                <div class="text-xs text-gray-500 truncate max-w-xs" title="{{ $crop->description }}">
                                    {{ $crop->description }}
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="py-3 pr-4 crop-variety">{{ $crop->variety ?? '—' }}</td>
                    <td class="py-3 pr-4">
                        @if($crop->plot)
                            <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">
                                {{ $crop->plot->name }}
                            </span>
                        @else
                            <span class="text-gray-400 text-sm">Sin lote</span>
                        @endif
                    </td>
                    <td class="py-3 pr-4 crop-yield">
                        @if($crop->yield_per_hectare)
                            {{ $crop->yield_per_hectare }} kg/ha
                        @else
                            —
                        @endif
                    </td>
                    <td class="py-3 pr-4 status-badge">
                        <span class="px-2 py-1 text-xs rounded {{ $crop->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $statuses[$crop->status] }}
                        </span>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="text-sm text-gray-600">{{ $crop->tasks_count ?? $crop->tasks()->count() }} tareas</span>
                    </td>
                    <td class="py-3 pr-4 text-right">
                        <div class="flex items-center gap-1 justify-end">
                            <!-- Ver detalles -->
                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 border border-blue-200 rounded hover:bg-blue-50 text-blue-600 view-crop-btn" 
                                    data-crop-id="{{ $crop->id }}"
                                    data-crop-name="{{ $crop->name }}"
                                    data-crop-description="{{ $crop->description ?? '' }}"
                                    data-crop-variety="{{ $crop->variety ?? '' }}"
                                    data-crop-yield="{{ $crop->yield_per_hectare ?? '' }}"
                                    data-crop-status="{{ $crop->status }}"
                                    data-crop-created="{{ $crop->created_at->format('d/m/Y H:i') }}"
                                    data-crop-updated="{{ $crop->updated_at->format('d/m/Y H:i') }}"
                                    title="Ver detalles">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                            
                            <!-- Editar -->
                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 border border-emerald-200 rounded hover:bg-emerald-50 text-emerald-600 edit-crop-btn" 
                                    data-crop-id="{{ $crop->id }}"
                                    data-crop-name="{{ $crop->name }}"
                                    data-crop-description="{{ $crop->description ?? '' }}"
                                    data-crop-variety="{{ $crop->variety ?? '' }}"
                                    data-crop-yield="{{ $crop->yield_per_hectare ?? '' }}"
                                    data-crop-status="{{ $crop->status }}"
                                    data-crop-plot-id="{{ $crop->plot_id ?? '' }}"
                                    data-crop-photo="{{ $crop->photo ? asset('storage/' . $crop->photo) : '' }}"
                                    title="Editar">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </button>
                            
                            
                            <!-- Eliminar -->
                            @if($crop->status === 'active')
                                <button class="inline-flex items-center justify-center w-8 h-8 border border-gray-200 rounded opacity-50 cursor-not-allowed bg-gray-100 text-gray-400" title="No se puede eliminar un cultivo activo. Primero debe inhabilitarlo." disabled>
                                    <i data-lucide="trash" class="w-4 h-4"></i>
                                </button>
                            @else
                                <button type="button" class="inline-flex items-center justify-center w-8 h-8 border border-red-200 rounded hover:bg-red-50 text-red-600 delete-crop-btn" 
                                        data-crop-id="{{ $crop->id }}"
                                        data-crop-name="{{ $crop->name }}"
                                        title="Eliminar">
                                    <i data-lucide="trash" class="w-4 h-4"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-6 text-center text-emerald-800/70">No hay cultivos registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

        <!-- Modal de edición -->
    <div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 overflow-y-auto" style="display: none;">
        <div class="bg-white border rounded p-6 w-full max-w-2xl mx-4 my-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-emerald-700">Editar Cultivo</h3>
                <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form id="editForm" class="space-y-4" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    
                    <!-- Nombre -->
                    <div>
                        <label class="block text-sm mb-1 text-emerald-800">Nombre</label>
                        <input type="text" name="name" id="editName" class="w-full border border-emerald-200 rounded px-3 py-2" required />
                    </div>
                    
                    <!-- Descripción -->
                    <div>
                        <label class="block text-sm mb-1 text-emerald-800">Descripción</label>
                        <textarea name="description" id="editDescription" rows="3" class="w-full border border-emerald-200 rounded px-3 py-2"></textarea>
                    </div>
                    
                    <!-- Variedad y Rendimiento -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1 text-emerald-800">Variedad</label>
                            <input type="text" name="variety" id="editVariety" class="w-full border border-emerald-200 rounded px-3 py-2" />
                        </div>
                        <div>
                            <label class="block text-sm mb-1 text-emerald-800">Rendimiento por Hectárea (kg)</label>
                            <input type="number" step="0.01" min="0" name="yield_per_hectare" id="editYield" class="w-full border border-emerald-200 rounded px-3 py-2" />
                        </div>
                    </div>
                    
                    <!-- Lote -->
                    <div>
                        <label class="block text-sm mb-1 text-emerald-800">Lote *</label>
                        <select name="plot_id" id="editPlotId" class="w-full border border-emerald-200 rounded px-3 py-2" required>
                            <option value="">Seleccionar lote...</option>
                            @foreach($plots ?? [] as $plot)
                                <option value="{{ $plot->id }}">{{ $plot->name }} - {{ $plot->location }} ({{ $plot->area }} ha)</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Foto -->
                    <div>
                        <label class="block text-sm mb-1 text-emerald-800">Foto del Cultivo</label>
                        <div id="editPhotoPreview" class="mb-3">
                            <p class="text-sm text-gray-600 mb-2">Foto actual:</p>
                            <img id="editCurrentPhoto" src="" alt="Foto actual" class="max-w-xs rounded border border-emerald-200" style="display: none;">
                        </div>
                        <input type="file" name="photo" id="editPhoto" accept="image/jpeg,image/png,image/gif,.jpg,.jpeg,.png,.gif,.JPG,.JPEG,.PNG,.GIF" 
                               class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <p class="text-xs text-gray-500 mt-1">Formatos permitidos: JPG, JPEG, PNG, GIF. Tamaño máximo: 2MB. Dejar vacío para mantener la foto actual.</p>
                        <div id="editPhotoNewPreview" class="mt-3 hidden">
                            <p class="text-sm text-gray-600 mb-2">Nueva foto:</p>
                            <img id="editPhotoNewPreviewImg" src="" alt="Vista previa" class="max-w-xs rounded border border-emerald-200">
                        </div>
                    </div>
                    
                    <!-- Estado -->
                    <div>
                        <label class="block text-sm mb-1 text-emerald-800">Estado</label>
                        <select name="status" id="editStatus" class="w-full border border-emerald-200 rounded px-3 py-2">
                            <option value="active">Activo</option>
                            <option value="inactive">Inactivo</option>
                        </select>
                    </div>
                    
                    <!-- Botones -->
                    <div class="flex items-center gap-2">
                        <button type="button" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50" onclick="closeEditModal()">
                            <i data-lucide="x" class="w-4 h-4 inline mr-2"></i>
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded transition-colors" id="updateButton">
                            <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                            <span>Actualizar</span>
                        </button>
                    </div>
                </form>
        </div>
    </div>
    
    <!-- Modal de confirmación para eliminar -->
    <div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
        <div class="bg-white border rounded p-6 w-full max-w-md mx-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-red-700">Confirmar Eliminación</h3>
                <button type="button" onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <div class="mb-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
                    </div>
                    <div>
                        <p class="text-gray-900 font-medium">¿Estás seguro de que quieres eliminar este cultivo?</p>
                        <p class="text-sm text-gray-600" id="deleteCropName"></p>
                    </div>
                </div>
                <div class="bg-red-50 border border-red-200 rounded p-3">
                    <p class="text-sm text-red-700">
                        <strong>Advertencia:</strong> Esta acción no se puede deshacer. Se eliminará permanentemente el cultivo y todos sus datos asociados.
                    </p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="button" onclick="confirmDelete()" class="flex-1 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 border border-red-200 rounded transition-colors">
                    <i data-lucide="trash" class="w-4 h-4 inline mr-2"></i>
                    Eliminar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de detalles -->
    <div id="viewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
        <div class="bg-white border rounded p-6 w-full max-w-2xl mx-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-emerald-700">Detalles del Cultivo</h3>
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
                            <label class="block text-sm font-medium text-gray-700">Variedad</label>
                            <p id="viewVariety" class="text-sm text-gray-900 mt-1"></p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Descripción</label>
                            <p id="viewDescription" class="text-sm text-gray-900 mt-1"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estado</label>
                            <p id="viewStatus" class="text-sm mt-1"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Rendimiento por Hectárea</label>
                            <p id="viewYield" class="text-sm text-gray-900 mt-1"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Estadísticas -->
                <div class="bg-gray-50 rounded p-4">
                    <h4 class="text-md font-semibold text-emerald-700 mb-3">Estadísticas</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-emerald-600" id="viewTasks">0</div>
                            <div class="text-sm text-gray-600">Tareas</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-emerald-600">0</div>
                            <div class="text-sm text-gray-600">Consumos</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-emerald-600">0</div>
                            <div class="text-sm text-gray-600">Entradas</div>
                        </div>
                    </div>
                </div>
                
                <!-- Información del Sistema -->
                <div class="bg-gray-50 rounded p-4">
                    <h4 class="text-md font-semibold text-emerald-700 mb-3">Información del Sistema</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Creado</label>
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
                <button type="button" onclick="closeViewModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-200 rounded transition-colors">
                    <i data-lucide="x" class="w-4 h-4 inline mr-2"></i>
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <div class="mt-4">{{ $crops->links() }}</div>
</div>

<script>
let currentCropId = null;
let deleteCropId = null;

// Función para abrir el modal de edición
function openEditModal(id, name, description, variety, yield_per_hectare, status, plot_id, photo) {
    currentCropId = id;
    
    console.log('Opening edit modal with data:', {
        id, name, description, variety, yield_per_hectare, status, plot_id, photo
    });
    
    // Llenar los campos del formulario
    document.getElementById('editName').value = name;
    document.getElementById('editDescription').value = description;
    document.getElementById('editVariety').value = variety;
    document.getElementById('editYield').value = yield_per_hectare;
    document.getElementById('editStatus').value = status;
    document.getElementById('editPlotId').value = plot_id || '';
    
    // Manejar la foto
    const photoPreview = document.getElementById('editPhotoPreview');
    const currentPhoto = document.getElementById('editCurrentPhoto');
    const photoInput = document.getElementById('editPhoto');
    const newPhotoPreview = document.getElementById('editPhotoNewPreview');
    
    console.log('Photo recibida:', photo);
    
    if (photo && photo !== '' && photo !== 'null') {
        currentPhoto.src = photo;
        currentPhoto.style.display = 'block';
        currentPhoto.onerror = function() {
            console.log('Error cargando imagen:', photo);
            this.style.display = 'none';
            photoPreview.querySelector('p').textContent = 'Foto actual: (No se pudo cargar la imagen)';
        };
        currentPhoto.onload = function() {
            console.log('Imagen cargada correctamente');
            photoPreview.querySelector('p').textContent = 'Foto actual:';
        };
        photoPreview.style.display = 'block';
    } else {
        photoPreview.style.display = 'none';
    }
    
    // Limpiar preview de nueva foto y el input
    photoInput.value = '';
    newPhotoPreview.classList.add('hidden');
    
    // Listener para preview de nueva foto
    photoInput.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validar tamaño del archivo (2MB)
            if (file.size > 2 * 1024 * 1024) {
                if (window.showErrorAlert) {
                    showErrorAlert('El archivo es demasiado grande. El tamaño máximo es 2MB.');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'El archivo es demasiado grande. El tamaño máximo es 2MB.',
                        confirmButtonText: 'Aceptar',
                    });
                }
                e.target.value = '';
                newPhotoPreview.classList.add('hidden');
                return;
            }
            
            // Validar tipo de archivo
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                if (window.showErrorAlert) {
                    showErrorAlert('Tipo de archivo no válido. Solo se permiten JPG, PNG y GIF.');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Tipo de archivo no válido. Solo se permiten JPG, PNG y GIF.',
                        confirmButtonText: 'Aceptar',
                    });
                }
                e.target.value = '';
                newPhotoPreview.classList.add('hidden');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('editPhotoNewPreviewImg').src = e.target.result;
                newPhotoPreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            newPhotoPreview.classList.add('hidden');
        }
    };
    
    // Debug: verificar que los valores se establecieron correctamente
    console.log('Modal values set:');
    console.log('- Status:', document.getElementById('editStatus').value);
    console.log('- Plot ID:', document.getElementById('editPlotId').value);
    
    // Mostrar el modal
    document.getElementById('editModal').style.display = 'flex';
}

// Función para cerrar el modal
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
    currentCropId = null;
}

// Función para abrir el modal de detalles
function openViewModal(id, name, description, variety, yield_per_hectare, status, created, updated) {
    // Llenar los campos del modal de detalles
    document.getElementById('viewName').textContent = name;
    document.getElementById('viewVariety').textContent = variety || '—';
    document.getElementById('viewDescription').textContent = description || 'Sin descripción';
    document.getElementById('viewYield').textContent = yield_per_hectare ? `${yield_per_hectare} kg/ha` : '—';
    document.getElementById('viewCreated').textContent = created;
    document.getElementById('viewUpdated').textContent = updated;
    
    // Configurar el estado con el badge apropiado
    const statusElement = document.getElementById('viewStatus');
    if (status === 'active') {
        statusElement.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Activo</span>';
    } else {
        statusElement.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Inactivo</span>';
    }
    
    // Mostrar el modal
    document.getElementById('viewModal').style.display = 'flex';
}

// Función para cerrar el modal de detalles
function closeViewModal() {
    document.getElementById('viewModal').style.display = 'none';
}

// Función para actualizar el cultivo
async function updateCrop() {
    // Confirmar antes de actualizar
    const confirmResult = await Swal.fire({
        title: '¿Actualizar cultivo?',
        text: '¿Estás seguro de que deseas guardar los cambios?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, actualizar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true,
        buttonsStyling: false,
        customClass: {
            popup: 'rounded-lg bg-white',
            confirmButton: 'px-4 py-2 rounded bg-emerald-500 hover:bg-emerald-600 text-white border border-emerald-600 transition-colors',
            cancelButton: 'px-4 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-100 ml-2',
        },
    });
    
    if (!confirmResult.isConfirmed) {
        return;
    }
    
    const updateButton = document.getElementById('updateButton');
    const originalText = updateButton.innerHTML;
    
    // Mostrar estado de carga
    updateButton.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 inline mr-2 animate-spin"></i><span>Actualizando...</span>';
    updateButton.disabled = true;
    
    try {
        // Recopilar datos del formulario
        const statusSelect = document.getElementById('editStatus');
        const plotSelect = document.getElementById('editPlotId');
        
        console.log('Status select element:', statusSelect);
        console.log('Status select value:', statusSelect ? statusSelect.value : 'NOT FOUND');
        console.log('Plot select element:', plotSelect);
        console.log('Plot select value:', plotSelect ? plotSelect.value : 'NOT FOUND');
        
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PUT');
        formData.append('name', document.getElementById('editName').value);
        formData.append('description', document.getElementById('editDescription').value);
        formData.append('variety', document.getElementById('editVariety').value);
        formData.append('yield_per_hectare', document.getElementById('editYield').value);
        formData.append('plot_id', plotSelect ? plotSelect.value : '');
        formData.append('status', statusSelect ? statusSelect.value : '');
        
        // Agregar la foto si se seleccionó una nueva
        const photoInput = document.getElementById('editPhoto');
        if (photoInput.files.length > 0) {
            formData.append('photo', photoInput.files[0]);
        }
        
        // Debug: mostrar los datos que se están enviando
        console.log('Sending data:', {
            name: formData.get('name'),
            status: formData.get('status'),
            plot_id: formData.get('plot_id'),
            hasPhoto: photoInput.files.length > 0
        });
        console.log('Current crop ID:', currentCropId);
        
        const response = await fetch(`/admin/crops/${currentCropId}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        });
        
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        
        if (response.ok) {
            const result = await response.json();
            console.log('Response data:', result);
            
            if (result.success) {
                // Actualizar la tabla en tiempo real con los datos del servidor
                updateTableRowWithServerData(result.crop);
                
                // Cerrar el modal
                closeEditModal();
                
                // Mostrar mensaje de éxito
                showSuccessMessage();
            } else {
                if (window.showErrorAlert) {
                    showErrorAlert(result.message || 'Error al actualizar el cultivo');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message || 'Error al actualizar el cultivo',
                        confirmButtonText: 'Aceptar',
                    });
                }
            }
        } else {
            const errorText = await response.text();
            console.error('Error response:', errorText);
            if (window.showErrorAlert) {
                showErrorAlert('Error al actualizar el cultivo. Status: ' + response.status);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al actualizar el cultivo. Status: ' + response.status,
                    confirmButtonText: 'Aceptar',
                });
            }
        }
    } catch (error) {
        console.error('Error:', error);
        if (window.showErrorAlert) {
            showErrorAlert('Error al actualizar el cultivo: ' + error.message);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al actualizar el cultivo: ' + error.message,
                confirmButtonText: 'Aceptar',
            });
        }
    } finally {
        // Restaurar el botón
        updateButton.innerHTML = originalText;
        updateButton.disabled = false;
    }
}

// Función para actualizar la fila en la tabla con datos del servidor
function updateTableRowWithServerData(cropData) {
    const row = document.querySelector(`tr[data-crop-id='${cropData.id}']`);
    if (row) {
        // Actualizar estado
        const statusCell = row.querySelector('.status-badge');
        if (statusCell) {
            if (cropData.status === 'active') {
                statusCell.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Activo</span>';
            } else {
                statusCell.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Inactivo</span>';
            }
        }
        
        // Actualizar nombre
        const nameCell = row.querySelector('.crop-name');
        if (nameCell) {
            nameCell.textContent = cropData.name;
        }
        
        // Actualizar variedad
        const varietyCell = row.querySelector('.crop-variety');
        if (varietyCell) {
            varietyCell.textContent = cropData.variety || '—';
        }
        
        // Actualizar rendimiento
        const yieldCell = row.querySelector('.crop-yield');
        if (yieldCell) {
            yieldCell.textContent = cropData.yield_per_hectare ? `${cropData.yield_per_hectare} kg/ha` : '—';
        }
        
        // Actualizar lote
        const plotCell = row.querySelector('td:nth-child(3)'); // La columna del lote
        if (plotCell) {
            if (cropData.plot_name) {
                plotCell.innerHTML = `<span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">${cropData.plot_name}</span>`;
            } else {
                plotCell.innerHTML = '<span class="text-gray-400 text-sm">Sin lote</span>';
            }
        }
        
        // Actualizar foto
        const photoCell = row.querySelector('td:first-child');
        if (photoCell && cropData.photo) {
            const existingImg = photoCell.querySelector('img');
            const existingPlaceholder = photoCell.querySelector('.photo-placeholder');
            if (existingImg) {
                existingImg.src = cropData.photo;
                existingImg.style.display = 'block';
                if (existingPlaceholder) {
                    existingPlaceholder.style.display = 'none';
                }
            } else if (!existingImg && !existingPlaceholder) {
                // Si no hay imagen ni placeholder, crear uno
                photoCell.innerHTML = `<img src="${cropData.photo}" alt="${cropData.name}" class="w-16 h-16 object-cover rounded border border-emerald-200" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"><div class="photo-placeholder w-16 h-16 bg-gray-100 rounded border border-emerald-200 flex items-center justify-center text-gray-400 text-xs" style="display: none;"><i data-lucide="image" class="w-6 h-6"></i></div>`;
            }
        } else if (photoCell && !cropData.photo) {
            // Si no hay foto, mostrar placeholder
            const existingImg = photoCell.querySelector('img');
            const existingPlaceholder = photoCell.querySelector('.photo-placeholder');
            if (existingImg) {
                existingImg.style.display = 'none';
            }
            if (existingPlaceholder) {
                existingPlaceholder.style.display = 'flex';
            } else {
                photoCell.innerHTML = `<div class="photo-placeholder w-16 h-16 bg-gray-100 rounded border border-emerald-200 flex items-center justify-center text-gray-400 text-xs"><i data-lucide="image" class="w-6 h-6"></i></div>`;
            }
        }
        
        // Actualizar data attributes del botón de editar
        const editButton = row.querySelector('.edit-crop-btn');
        if (editButton) {
            editButton.setAttribute('data-crop-name', cropData.name);
            editButton.setAttribute('data-crop-variety', cropData.variety || '');
            editButton.setAttribute('data-crop-yield', cropData.yield_per_hectare || '');
            editButton.setAttribute('data-crop-status', cropData.status);
            editButton.setAttribute('data-crop-plot-id', cropData.plot_id || '');
            editButton.setAttribute('data-crop-photo', cropData.photo || '');
        }
        
        // Re-inicializar Lucide icons si es necesario
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }
}

// Función para actualizar la fila en la tabla (versión anterior - mantener por compatibilidad)
function updateTableRow() {
    const row = document.querySelector(`tr[data-crop-id='${currentCropId}']`);
    if (row) {
        const statusCell = row.querySelector('.status-badge');
        if (statusCell) {
            const status = document.getElementById('editStatus').value;
            if (status === 'active') {
                statusCell.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Activo</span>';
            } else {
                statusCell.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Inactivo</span>';
            }
        }
        
        // Actualizar otros campos
        const nameCell = row.querySelector('.crop-name');
        if (nameCell) {
            nameCell.textContent = document.getElementById('editName').value;
        }
        
        const varietyCell = row.querySelector('.crop-variety');
        if (varietyCell) {
            varietyCell.textContent = document.getElementById('editVariety').value || '—';
        }
        
        const yieldCell = row.querySelector('.crop-yield');
        if (yieldCell) {
            const yieldValue = document.getElementById('editYield').value;
            yieldCell.textContent = yieldValue ? `${yieldValue} kg/ha` : '—';
        }
        
        // Actualizar el lote
        const plotCell = row.querySelector('td:nth-child(3)'); // La columna del lote
        if (plotCell) {
            const plotSelect = document.getElementById('editPlotId');
            const selectedOption = plotSelect.options[plotSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                plotCell.innerHTML = `<span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">${selectedOption.text.split(' - ')[0]}</span>`;
            } else {
                plotCell.innerHTML = '<span class="text-gray-400 text-sm">Sin lote</span>';
            }
        }
    }
}

// Función para mostrar mensaje de éxito
function showSuccessMessage(message = 'Cultivo actualizado correctamente') {
    const messageElement = document.createElement('div');
    messageElement.className = 'mb-4 p-3 bg-emerald-100 border border-emerald-300 text-emerald-700 rounded';
    messageElement.textContent = message;
    
    const content = document.querySelector('.bg-white.border.rounded.p-4');
    if (content) {
        content.insertBefore(messageElement, content.firstChild);
        
        // Remover el mensaje después de 4 segundos
        setTimeout(() => {
            if (messageElement.parentNode) {
                messageElement.parentNode.removeChild(messageElement);
            }
        }, 4000);
    }
}

// Función para mostrar mensaje de error
function showErrorMessage(message) {
    const messageElement = document.createElement('div');
    messageElement.className = 'mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded';
    messageElement.textContent = message;
    
    const content = document.querySelector('.bg-white.border.rounded.p-4');
    if (content) {
        content.insertBefore(messageElement, content.firstChild);
        
        // Remover el mensaje después de 5 segundos
        setTimeout(() => {
            if (messageElement.parentNode) {
                messageElement.parentNode.removeChild(messageElement);
            }
        }, 5000);
    }
}

// Función para abrir el modal de confirmación de eliminación
function openDeleteModal(id, name) {
    deleteCropId = id;
    document.getElementById('deleteCropName').textContent = name;
    document.getElementById('deleteModal').style.display = 'flex';
}

// Función para cerrar el modal de confirmación de eliminación
function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    deleteCropId = null;
}

// Función para confirmar la eliminación
async function confirmDelete() {
    if (!deleteCropId) return;
    
    const deleteButton = document.querySelector('#deleteModal button[onclick="confirmDelete()"]');
    const originalText = deleteButton.innerHTML;
    
    // Mostrar estado de carga
    deleteButton.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 inline mr-2 animate-spin"></i><span>Eliminando...</span>';
    deleteButton.disabled = true;
    
    try {
        const response = await fetch(`/admin/crops/${deleteCropId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (response.ok) {
            // Cerrar el modal
            closeDeleteModal();
            
            // Mostrar mensaje de éxito
            showSuccessMessage('Cultivo eliminado correctamente');
            
            // Remover la fila de la tabla
            const row = document.querySelector(`tr[data-crop-id='${deleteCropId}']`);
            if (row) {
                row.remove();
            }
        } else {
            const errorData = await response.json();
            showErrorMessage(errorData.message || 'Error al eliminar el cultivo');
        }
    } catch (error) {
        console.error('Error:', error);
        showErrorMessage('Error al eliminar el cultivo: ' + error.message);
    } finally {
        // Restaurar el botón
        deleteButton.innerHTML = originalText;
        deleteButton.disabled = false;
    }
}

// Inicialización cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    // Asegurar que el modal esté oculto por defecto
    const modal = document.getElementById('editModal');
    if (modal) {
        modal.style.display = 'none';
    }
    
    // Agregar eventos a los botones de editar
    document.querySelectorAll('.edit-crop-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-crop-id');
            const name = this.getAttribute('data-crop-name');
            const description = this.getAttribute('data-crop-description');
            const variety = this.getAttribute('data-crop-variety');
            const yield_per_hectare = this.getAttribute('data-crop-yield');
            const status = this.getAttribute('data-crop-status');
            const plot_id = this.getAttribute('data-crop-plot-id');
            const photo = this.getAttribute('data-crop-photo');
            
            openEditModal(id, name, description, variety, yield_per_hectare, status, plot_id, photo);
        });
    });
    
    // Agregar eventos a los botones de ver detalles
    document.querySelectorAll('.view-crop-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-crop-id');
            const name = this.getAttribute('data-crop-name');
            const description = this.getAttribute('data-crop-description');
            const variety = this.getAttribute('data-crop-variety');
            const yield_per_hectare = this.getAttribute('data-crop-yield');
            const status = this.getAttribute('data-crop-status');
            const created = this.getAttribute('data-crop-created');
            const updated = this.getAttribute('data-crop-updated');
            
            openViewModal(id, name, description, variety, yield_per_hectare, status, created, updated);
        });
    });
    
    // Agregar eventos a los botones de eliminar
    document.querySelectorAll('.delete-crop-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-crop-id');
            const name = this.getAttribute('data-crop-name');
            
            openDeleteModal(id, name);
        });
    });
    
    // Agregar evento de tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeEditModal();
            closeViewModal();
            closeDeleteModal();
        }
    });
    
    // Agregar evento de clic fuera del modal de edición
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeEditModal();
        }
    });
    
    // Agregar evento de clic fuera del modal de detalles
    const viewModal = document.getElementById('viewModal');
    viewModal.addEventListener('click', function(e) {
        if (e.target === viewModal) {
            closeViewModal();
        }
    });
    
    // Agregar evento de clic fuera del modal de eliminación
    const deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('click', function(e) {
        if (e.target === deleteModal) {
            closeDeleteModal();
        }
    });
    
    // Agregar evento de envío del formulario
    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();
        updateCrop();
    });
});
</script>
@endsection