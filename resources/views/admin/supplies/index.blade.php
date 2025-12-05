@extends('admin.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Gestión de Insumos</h2>
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
        <div class="flex gap-4 flex-wrap">
            <a href="{{ route('admin.supply-movements.create', ['type' => 'entry']) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-100 hover:bg-green-200 text-green-700 border border-green-200 rounded">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Entrada de Insumo</span>
            </a>
            <a href="{{ route('admin.supply-movements.create', ['type' => 'exit']) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 border border-red-200 rounded">
                <i data-lucide="minus-circle" class="w-4 h-4"></i>
                <span>Salida de Insumo</span>
            </a>
            <a href="{{ route('admin.supply-movements.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 border border-blue-200 rounded">
                <i data-lucide="activity" class="w-4 h-4"></i>
                <span>Ver Movimientos</span>
            </a>
            <a href="{{ route('admin.supply-consumptions.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-100 hover:bg-purple-200 text-purple-700 border border-purple-200 rounded">
                <i data-lucide="bar-chart" class="w-4 h-4"></i>
                <span>Ver Consumos</span>
            </a>
            <a href="{{ route('admin.supplies.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded-lg font-medium transition-colors">
                <i data-lucide="plus" class="w-5 h-5"></i>
                <span>Nuevo Insumo</span>
            </a>
        </div>
        <a href="{{ route('admin.supplies.pdf', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 border border-red-200 rounded-lg font-medium transition-colors">
            <i data-lucide="file-text" class="w-5 h-5"></i>
            <span>Descargar PDF</span>
        </a>
    </div>

    <!-- Filtros de búsqueda -->
    <form method="GET" class="mb-4 flex gap-2 items-end">
        <div class="flex-1">
            <label class="block text-sm mb-1 text-emerald-800">Buscar por nombre</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar insumos..." class="w-full border border-emerald-200 rounded px-3 py-2" />
        </div>
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Estado</label>
            <select name="status" class="border border-emerald-200 rounded px-3 py-2">
                <option value="all">Todos los estados</option>
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-3 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded inline-flex items-center gap-2 transition-colors">
            <i data-lucide="search" class="w-4 h-4"></i>
            <span>Filtrar</span>
        </button>
    </form>

    <!-- Tabla de insumos -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-emerald-800 border-b">
                    <th class="py-3 pr-4">Foto</th>
                    <th class="py-3 pr-4">Nombre</th>
                    <th class="py-3 pr-4">Unidad</th>
                    <th class="py-3 pr-4">Stock Actual</th>
                    <th class="py-3 pr-4">Stock Mínimo</th>
                    <th class="py-3 pr-4">Costo por Unidad</th>
                    <th class="py-3 pr-4">Estado</th>
                    <th class="py-3 pr-4">Consumos</th>
                    <th class="py-3 pr-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($supplies as $supply)
                <tr class="border-b hover:bg-gray-50" data-supply-id="{{ $supply->id }}">
                    <td class="py-3 pr-4">
                        @if(!empty($supply->photo))
                            <img src="{{ asset('storage/' . $supply->photo) }}" alt="{{ $supply->name }}" class="w-16 h-16 object-cover rounded border border-emerald-200" onerror="this.onerror=null; this.src=''; this.style.display='none'; this.nextElementSibling.style.display='flex';">
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
                        <div class="font-medium text-gray-900 supply-name">{{ $supply->name }}</div>
                    </td>
                    <td class="py-3 pr-4 supply-unit">
                        {{ $supply->unit }}
                    </td>
                    <td class="py-3 pr-4">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold {{ $supply->isLowStock() ? 'text-red-600' : 'text-gray-900' }}">
                                {{ number_format($supply->current_stock, 3) }}
                            </span>
                            @if($supply->isLowStock())
                                <i data-lucide="alert-triangle" class="w-4 h-4 text-red-500" title="Stock bajo"></i>
                            @endif
                        </div>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="text-sm text-gray-600">{{ number_format($supply->min_stock, 3) }}</span>
                    </td>
                    <td class="py-3 pr-4 supply-unit-cost">
                        ${{ number_format($supply->unit_cost, 2) }}
                    </td>
                    <td class="py-3 pr-4 status-badge">
                        <span class="px-2 py-1 text-xs rounded {{ $supply->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $statuses[$supply->status] }}
                        </span>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-900">{{ $supply->consumptions()->count() }}</div>
                        <div class="text-xs text-gray-500">registros</div>
                    </td>
                    <td class="py-3 pr-4 text-right">
                        <div class="flex items-center gap-1 justify-end">
                            <!-- Ver detalles -->
                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 border border-blue-200 rounded hover:bg-blue-50 text-blue-600 view-supply-btn" 
                                    data-supply-id="{{ $supply->id }}"
                                    data-supply-name="{{ $supply->name }}"
                                    data-supply-unit="{{ $supply->unit }}"
                                    data-supply-unit-cost="{{ $supply->unit_cost }}"
                                    data-supply-status="{{ $supply->status }}"
                                    data-supply-created="{{ $supply->created_at->format('d/m/Y H:i') }}"
                                    data-supply-updated="{{ $supply->updated_at->format('d/m/Y H:i') }}"
                                    data-supply-consumptions="{{ $supply->consumptions()->count() }}"
                                    title="Ver detalles">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                            
                            <!-- Editar -->
                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 border border-emerald-200 rounded hover:bg-emerald-50 text-emerald-600 edit-supply-btn" 
                                    data-supply-id="{{ $supply->id }}"
                                    data-supply-name="{{ $supply->name }}"
                                    data-supply-unit="{{ $supply->unit }}"
                                    data-supply-unit-cost="{{ $supply->unit_cost }}"
                                    data-supply-status="{{ $supply->status }}"
                                    data-supply-photo="{{ $supply->photo ? asset('storage/' . $supply->photo) : '' }}"
                                    title="Editar">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </button>
                            
                            <!-- Eliminar -->
                            @if($supply->consumptions()->exists())
                                <button class="inline-flex items-center justify-center w-8 h-8 border border-gray-200 rounded opacity-50 cursor-not-allowed bg-gray-100 text-gray-400" title="No se puede eliminar un insumo con consumos registrados" disabled>
                                    <i data-lucide="trash" class="w-4 h-4"></i>
                                </button>
                            @else
                                <form method="POST" action="{{ route('admin.supplies.destroy', $supply) }}" class="inline" data-confirm="true" data-message="¿Eliminar este insumo? Esta acción no se puede deshacer.">
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
                    <td colspan="9" class="py-6 text-center text-emerald-800/70">No hay insumos registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $supplies->links() }}</div>
</div>

<!-- Modal de detalles -->
<div id="viewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-6 w-full max-w-2xl mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-emerald-700">Detalles del Insumo</h3>
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
                        <label class="block text-sm font-medium text-gray-700">Unidad de Medida</label>
                        <p id="viewUnit" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Costo por Unidad</label>
                        <p id="viewUnitCost" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estado</label>
                        <p id="viewStatus" class="text-sm mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Total de Consumos</label>
                        <p id="viewConsumptions" class="text-sm text-gray-900 mt-1"></p>
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

<!-- Modal de edición -->
<div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 overflow-y-auto" style="display: none;">
    <div class="bg-white border rounded p-6 w-full max-w-2xl mx-4 my-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-emerald-700">Editar Insumo</h3>
            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="editForm" class="space-y-4" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="PUT">
            
            <!-- Nombre -->
            <div>
                <label class="block text-sm mb-1 text-emerald-800">Nombre del Insumo</label>
                <input type="text" name="name" id="editName" class="w-full border border-emerald-200 rounded px-3 py-2" required />
            </div>
            
            <!-- Unidad y Costo -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm mb-1 text-emerald-800">Unidad de Medida</label>
                    <select name="unit" id="editUnit" class="w-full border border-emerald-200 rounded px-3 py-2">
                        <option value="kg">Kilogramos (kg)</option>
                        <option value="lt">Litros (lt)</option>
                        <option value="unit">Unidades</option>
                        <option value="g">Gramos (g)</option>
                        <option value="ml">Mililitros (ml)</option>
                        <option value="lb">Libras (lb)</option>
                        <option value="gal">Galones (gal)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1 text-emerald-800">Costo por Unidad</label>
                    <input type="number" step="0.01" min="0" name="unit_cost" id="editUnitCost" class="w-full border border-emerald-200 rounded px-3 py-2" required />
                </div>
            </div>
            
            <!-- Foto -->
            <div>
                <label class="block text-sm mb-1 text-emerald-800">Foto del Insumo</label>
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

<script>
let currentSupplyId = null;

// Función para abrir el modal de edición
function openEditModal(id, name, unit, unit_cost, status, photo) {
    currentSupplyId = id;
    
    // Llenar los campos del formulario
    document.getElementById('editName').value = name;
    document.getElementById('editUnit').value = unit;
    document.getElementById('editUnitCost').value = unit_cost;
    document.getElementById('editStatus').value = status;
    
    // Manejar la foto
    const photoPreview = document.getElementById('editPhotoPreview');
    const currentPhoto = document.getElementById('editCurrentPhoto');
    const photoInput = document.getElementById('editPhoto');
    const newPhotoPreview = document.getElementById('editPhotoNewPreview');
    
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
    
    // Mostrar el modal
    document.getElementById('editModal').style.display = 'flex';
}

// Función para cerrar el modal
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
    currentSupplyId = null;
}

// Función para abrir el modal de detalles
function openViewModal(id, name, unit, unit_cost, status, created, updated, consumptions) {
    // Llenar los campos del modal de detalles
    document.getElementById('viewName').textContent = name;
    document.getElementById('viewUnit').textContent = unit;
    document.getElementById('viewUnitCost').textContent = '$' + parseFloat(unit_cost).toFixed(2);
    document.getElementById('viewConsumptions').textContent = consumptions + ' registros';
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

// Función para actualizar el insumo
async function updateSupply() {
    // Confirmar antes de actualizar
    const confirmResult = await Swal.fire({
        title: '¿Actualizar insumo?',
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
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PUT');
        formData.append('name', document.getElementById('editName').value);
        formData.append('unit', document.getElementById('editUnit').value);
        formData.append('unit_cost', document.getElementById('editUnitCost').value);
        formData.append('status', document.getElementById('editStatus').value);
        
        // Agregar la foto si se seleccionó una nueva
        const photoInput = document.getElementById('editPhoto');
        if (photoInput.files.length > 0) {
            formData.append('photo', photoInput.files[0]);
        }
        
        const response = await fetch(`/admin/supplies/${currentSupplyId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            const result = await response.json();
            console.log('Response data:', result);
            
            if (result.success) {
                // Actualizar la tabla en tiempo real con los datos del servidor
                updateTableRowWithServerData(result.supply);
                
                // Cerrar el modal
                closeEditModal();
                
                // Mostrar mensaje de éxito
                showSuccessMessage();
            } else {
                if (window.showErrorAlert) {
                    showErrorAlert(result.message || 'Error al actualizar el insumo');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message || 'Error al actualizar el insumo',
                        confirmButtonText: 'Aceptar',
                    });
                }
            }
        } else {
            const errorText = await response.text();
            console.error('Error response:', errorText);
            if (window.showErrorAlert) {
                showErrorAlert('Error al actualizar el insumo. Status: ' + response.status);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al actualizar el insumo. Status: ' + response.status,
                    confirmButtonText: 'Aceptar',
                });
            }
        }
    } catch (error) {
        console.error('Error:', error);
        if (window.showErrorAlert) {
            showErrorAlert('Error al actualizar el insumo: ' + error.message);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al actualizar el insumo: ' + error.message,
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
function updateTableRowWithServerData(supplyData) {
    const row = document.querySelector(`tr[data-supply-id='${supplyData.id}']`);
    if (row) {
        // Actualizar estado
        const statusCell = row.querySelector('.status-badge');
        if (statusCell) {
            if (supplyData.status === 'active') {
                statusCell.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Activo</span>';
            } else {
                statusCell.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Inactivo</span>';
            }
        }
        
        // Actualizar nombre
        const nameCell = row.querySelector('.supply-name');
        if (nameCell) {
            nameCell.textContent = supplyData.name;
        }
        
        // Actualizar unidad
        const unitCell = row.querySelector('.supply-unit');
        if (unitCell) {
            unitCell.textContent = supplyData.unit;
        }
        
        // Actualizar costo unitario
        const unitCostCell = row.querySelector('.supply-unit-cost');
        if (unitCostCell) {
            unitCostCell.textContent = '$' + parseFloat(supplyData.unit_cost).toFixed(2);
        }
        
        // Actualizar foto
        const photoCell = row.querySelector('td:first-child');
        if (photoCell && supplyData.photo) {
            const existingImg = photoCell.querySelector('img');
            const existingPlaceholder = photoCell.querySelector('.photo-placeholder');
            if (existingImg) {
                existingImg.src = supplyData.photo;
                existingImg.style.display = 'block';
                if (existingPlaceholder) {
                    existingPlaceholder.style.display = 'none';
                }
            } else if (!existingImg && !existingPlaceholder) {
                photoCell.innerHTML = `<img src="${supplyData.photo}" alt="${supplyData.name}" class="w-16 h-16 object-cover rounded border border-emerald-200" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"><div class="photo-placeholder w-16 h-16 bg-gray-100 rounded border border-emerald-200 flex items-center justify-center text-gray-400 text-xs" style="display: none;"><i data-lucide="image" class="w-6 h-6"></i></div>`;
            }
        } else if (photoCell && !supplyData.photo) {
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
        const editButton = row.querySelector('.edit-supply-btn');
        if (editButton) {
            editButton.setAttribute('data-supply-name', supplyData.name);
            editButton.setAttribute('data-supply-unit', supplyData.unit);
            editButton.setAttribute('data-supply-unit-cost', supplyData.unit_cost);
            editButton.setAttribute('data-supply-status', supplyData.status);
            editButton.setAttribute('data-supply-photo', supplyData.photo || '');
        }
        
        // Re-inicializar Lucide icons si es necesario
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }
}

// Función para actualizar la fila en la tabla (versión anterior - mantener por compatibilidad)
function updateTableRow() {
    const row = document.querySelector(`tr[data-supply-id='${currentSupplyId}']`);
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
        const nameCell = row.querySelector('.supply-name');
        if (nameCell) {
            nameCell.textContent = document.getElementById('editName').value;
        }
        
        const unitCell = row.querySelector('.supply-unit');
        if (unitCell) {
            unitCell.textContent = document.getElementById('editUnit').value;
        }
        
        const unitCostCell = row.querySelector('.supply-unit-cost');
        if (unitCostCell) {
            unitCostCell.textContent = '$' + parseFloat(document.getElementById('editUnitCost').value).toFixed(2);
        }
    }
}

// Función para mostrar mensaje de éxito
function showSuccessMessage() {
    const message = document.createElement('div');
    message.className = 'mb-4 p-3 bg-emerald-100 border border-emerald-300 text-emerald-700 rounded';
    message.textContent = 'Insumo actualizado correctamente';
    
    const content = document.querySelector('.bg-white.border.rounded.p-4');
    if (content) {
        content.insertBefore(message, content.firstChild);
        
        // Remover el mensaje después de 3 segundos
        setTimeout(() => {
            if (message.parentNode) {
                message.parentNode.removeChild(message);
            }
        }, 3000);
    }
}

// Inicialización cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    // Asegurar que los modales estén ocultos por defecto
    const editModal = document.getElementById('editModal');
    const viewModal = document.getElementById('viewModal');
    if (editModal) editModal.style.display = 'none';
    if (viewModal) viewModal.style.display = 'none';
    
    // Agregar eventos a los botones de editar
    document.querySelectorAll('.edit-supply-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-supply-id');
            const name = this.getAttribute('data-supply-name');
            const unit = this.getAttribute('data-supply-unit');
            const unit_cost = this.getAttribute('data-supply-unit-cost');
            const status = this.getAttribute('data-supply-status');
            const photo = this.getAttribute('data-supply-photo');
            
            openEditModal(id, name, unit, unit_cost, status, photo);
        });
    });
    
    // Agregar eventos a los botones de ver detalles
    document.querySelectorAll('.view-supply-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-supply-id');
            const name = this.getAttribute('data-supply-name');
            const unit = this.getAttribute('data-supply-unit');
            const unit_cost = this.getAttribute('data-supply-unit-cost');
            const status = this.getAttribute('data-supply-status');
            const created = this.getAttribute('data-supply-created');
            const updated = this.getAttribute('data-supply-updated');
            const consumptions = this.getAttribute('data-supply-consumptions');
            
            openViewModal(id, name, unit, unit_cost, status, created, updated, consumptions);
        });
    });
    
    // Agregar evento de tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeEditModal();
            closeViewModal();
        }
    });
    
    // Agregar evento de clic fuera del modal de edición
    editModal.addEventListener('click', function(e) {
        if (e.target === editModal) {
            closeEditModal();
        }
    });
    
    // Agregar evento de clic fuera del modal de detalles
    viewModal.addEventListener('click', function(e) {
        if (e.target === viewModal) {
            closeViewModal();
        }
    });
    
    // Agregar evento de envío del formulario
    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();
        updateSupply();
    });
});
</script>
@endsection
