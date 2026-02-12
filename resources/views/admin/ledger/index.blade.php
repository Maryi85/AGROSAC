@extends('admin.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Gestión Contable</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-4">
    <!-- Barra de búsqueda y botones de acción -->
    <div class="flex justify-between items-center mb-6 gap-4">
        <x-search-bar placeholder="Buscar por categoría, cultivo, lote o referencia..." />
        <div class="flex gap-2">
            <a href="{{ route('admin.ledger.movements.pdf', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg whitespace-nowrap">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                <span>Descargar PDF</span>
            </a>
            <a href="{{ route('admin.ledger.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg whitespace-nowrap">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Nuevo Movimiento</span>
            </a>
        </div>
    </div>

    <!-- Tabla de movimientos -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-emerald-800 border-b">
                    <th class="py-3 pr-4">Fecha</th>
                    <th class="py-3 pr-4">Tipo</th>
                    <th class="py-3 pr-4">Categoría</th>
                    <th class="py-3 pr-4">Monto</th>
                    <th class="py-3 pr-4">Cultivo</th>
                    <th class="py-3 pr-4">Lote</th>
                    <th class="py-3 pr-4">Referencia</th>
                    <th class="py-3 pr-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($entries as $entry)
                <tr class="border-b hover:bg-gray-50" data-entry-id="{{ $entry->id }}">
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-900">{{ $entry->occurred_at->format('d/m/Y') }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="px-2 py-1 text-xs rounded {{ $entry->type === 'income' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                            {{ $entry->type === 'income' ? 'Ingreso' : 'Gasto' }}
                        </span>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-900">{{ $categories[$entry->category] }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm font-medium {{ $entry->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $entry->type === 'income' ? '+' : '-' }}${{ number_format($entry->amount, 2) }}
                        </div>
                    </td>
                    <td class="py-3 pr-4">
                        @if($entry->crop)
                            <div class="text-sm text-gray-900">{{ $entry->crop->name }}</div>
                        @else
                            <div class="text-sm text-gray-500">—</div>
                        @endif
                    </td>
                    <td class="py-3 pr-4">
                        @if($entry->plot)
                            <div class="text-sm text-gray-900">{{ $entry->plot->name }}</div>
                        @else
                            <div class="text-sm text-gray-500">—</div>
                        @endif
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-900">{{ $entry->reference ?? '—' }}</div>
                    </td>
                    <td class="py-3 pr-4 text-right">
                        <div class="flex items-center gap-1 justify-end">
                            <!-- Ver detalles -->
                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 border border-blue-200 rounded hover:bg-blue-50 text-blue-600 view-entry-btn" 
                                    data-entry-id="{{ $entry->id }}"
                                    data-type="{{ $entry->type }}"
                                    data-category="{{ $entry->category }}"
                                    data-amount="{{ $entry->amount }}"
                                    data-occurred-at="{{ $entry->occurred_at->format('d/m/Y') }}"
                                    data-crop-name="{{ $entry->crop ? $entry->crop->name : 'No especificado' }}"
                                    data-plot-name="{{ $entry->plot ? $entry->plot->name : 'No especificado' }}"
                                    data-reference="{{ $entry->reference ?? 'Sin referencia' }}"
                                    data-created="{{ $entry->created_at->format('d/m/Y H:i') }}"
                                    title="Ver detalles">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                            
                            <!-- Editar -->
                            <button type="button" 
                               class="inline-flex items-center justify-center w-8 h-8 border border-emerald-200 rounded hover:bg-emerald-50 text-emerald-600 edit-entry-btn" 
                               data-entry-id="{{ $entry->id }}"
                               data-type="{{ $entry->type }}"
                               data-category="{{ $entry->category }}"
                               data-amount="{{ $entry->amount }}"
                               data-occurred-at="{{ $entry->occurred_at->format('Y-m-d') }}"
                               data-crop-id="{{ $entry->crop_id }}"
                               data-plot-id="{{ $entry->plot_id }}"
                               data-reference="{{ $entry->reference }}"
                               title="Editar">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </button>
                            
                            <!-- Eliminar -->
                            <form method="POST" action="{{ route('admin.ledger.destroy', $entry) }}" class="inline" data-confirm="true" data-message="¿Eliminar este movimiento contable? Esta acción no se puede deshacer.">
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
                    <td colspan="8" class="py-6 text-center text-emerald-800/70">No hay movimientos contables registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $entries->links() }}</div>
</div>

<!-- Modal de Edición -->
<div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-emerald-700">Editar Movimiento Contable</h3>
            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form id="editForm" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Tipo de Movimiento -->
                <div>
                    <label for="edit_type" class="block text-sm mb-1 text-emerald-800">Tipo de Movimiento</label>
                    <select id="edit_type" name="type" 
                            class="w-full border border-emerald-200 rounded px-3 py-2 @error('type') border-red-500 @enderror" required>
                        <option value="">Seleccionar tipo</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Categoría -->
                <div>
                    <label for="edit_category" class="block text-sm mb-1 text-emerald-800">Categoría</label>
                    <select id="edit_category" name="category" 
                            class="w-full border border-emerald-200 rounded px-3 py-2 @error('category') border-red-500 @enderror" required>
                        <option value="">Seleccionar categoría</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Monto -->
                <div>
                    <label for="edit_amount" class="block text-sm mb-1 text-emerald-800">Monto</label>
                    <input type="number" step="0.01" min="0.01" id="edit_amount" name="amount" value="{{ old('amount') }}"
                           class="w-full border border-emerald-200 rounded px-3 py-2 @error('amount') border-red-500 @enderror" required />
                    @error('amount')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Fecha -->
                <div>
                    <label for="edit_occurred_at" class="block text-sm mb-1 text-emerald-800">Fecha del Movimiento</label>
                    <input type="date" id="edit_occurred_at" name="occurred_at" value="{{ old('occurred_at') }}"
                           class="w-full border border-emerald-200 rounded px-3 py-2 @error('occurred_at') border-red-500 @enderror" required />
                    @error('occurred_at')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Cultivo -->
                <div>
                    <label for="edit_crop_id" class="block text-sm mb-1 text-emerald-800">Cultivo (Opcional)</label>
                    <select id="edit_crop_id" name="crop_id" 
                            class="w-full border border-emerald-200 rounded px-3 py-2 @error('crop_id') border-red-500 @enderror">
                        <option value="">Seleccionar cultivo</option>
                        @foreach($crops as $crop)
                            <option value="{{ $crop->id }}" data-plot-id="{{ $crop->plot_id }}" {{ old('crop_id') == $crop->id ? 'selected' : '' }}>
                                {{ $crop->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('crop_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Lote -->
                <div>
                    <label for="edit_plot_id" class="block text-sm mb-1 text-emerald-800">Lote (Opcional)</label>
                    <select id="edit_plot_id" name="plot_id" 
                            class="w-full border border-emerald-200 rounded px-3 py-2 @error('plot_id') border-red-500 @enderror">
                        <option value="">Seleccionar lote</option>
                        @foreach($plots as $plot)
                            <option value="{{ $plot->id }}" {{ old('plot_id') == $plot->id ? 'selected' : '' }}>
                                {{ $plot->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('plot_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Referencia -->
                <div class="md:col-span-2">
                    <label for="edit_reference" class="block text-sm mb-1 text-emerald-800">Referencia (Opcional)</label>
                    <input type="text" id="edit_reference" name="reference" value="{{ old('reference') }}"
                           placeholder="Ej: Factura #123, Recibo de pago, etc."
                           class="w-full border border-emerald-200 rounded px-3 py-2 @error('reference') border-red-500 @enderror" />
                    @error('reference')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Botones -->
            <div class="flex items-center justify-end gap-2 pt-4">
                <button type="button" onclick="closeEditModal()" 
                   class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 inline-flex items-center gap-2">
                    Cancelar
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white border border-emerald-600 rounded inline-flex items-center gap-2 transition-colors">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Actualizar Movimiento</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de detalles -->
<div id="viewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-6 w-full max-w-2xl mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-emerald-700">Detalles del Movimiento</h3>
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
                        <label class="block text-sm font-medium text-gray-700">Tipo</label>
                        <p id="viewType" class="text-sm mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Categoría</label>
                        <p id="viewCategory" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Monto</label>
                        <p id="viewAmount" class="text-sm font-medium mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha</label>
                        <p id="viewOccurredAt" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                </div>
            </div>
            
            <!-- Información de Asociación -->
            <div class="bg-gray-50 rounded p-4">
                <h4 class="text-md font-semibold text-emerald-700 mb-3">Información de Asociación</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cultivo</label>
                        <p id="viewCropName" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Lote</label>
                        <p id="viewPlotName" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Referencia</label>
                        <p id="viewReference" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                </div>
            </div>
            
            <!-- Información del Sistema -->
            <div class="bg-gray-50 rounded p-4">
                <h4 class="text-md font-semibold text-emerald-700 mb-3">Información del Sistema</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Registrado</label>
                        <p id="viewCreated" class="text-sm text-gray-900 mt-1"></p>
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

<script>
// Función para abrir el modal de detalles
function openViewModal(id, type, category, amount, occurredAt, cropName, plotName, reference, created) {
    // Llenar los campos del modal de detalles
    document.getElementById('viewOccurredAt').textContent = occurredAt;
    document.getElementById('viewCategory').textContent = category;
    document.getElementById('viewCropName').textContent = cropName;
    document.getElementById('viewPlotName').textContent = plotName;
    document.getElementById('viewReference').textContent = reference;
    document.getElementById('viewCreated').textContent = created;
    
    // Configurar el tipo con el badge apropiado
    const typeElement = document.getElementById('viewType');
    if (type === 'income') {
        typeElement.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Ingreso</span>';
    } else {
        typeElement.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">Gasto</span>';
    }
    
    // Configurar el monto con el color apropiado
    const amountElement = document.getElementById('viewAmount');
    const formattedAmount = '$' + parseFloat(amount).toFixed(2);
    if (type === 'income') {
        amountElement.innerHTML = '<span class="text-emerald-600 font-semibold">+' + formattedAmount + '</span>';
    } else {
        amountElement.innerHTML = '<span class="text-red-600 font-semibold">-' + formattedAmount + '</span>';
    }
    
    // Mostrar el modal
    document.getElementById('viewModal').style.display = 'flex';
}

// Función para cerrar el modal de detalles
function closeViewModal() {
    document.getElementById('viewModal').style.display = 'none';
}

// Inicialización cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    // Asegurar que el modal esté oculto por defecto
    const viewModal = document.getElementById('viewModal');
    if (viewModal) {
        viewModal.style.display = 'none';
    }
    
    // Agregar eventos a los botones de ver detalles
    document.querySelectorAll('.view-entry-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-entry-id');
            const type = this.getAttribute('data-type');
            const category = this.getAttribute('data-category');
            const amount = this.getAttribute('data-amount');
            const occurredAt = this.getAttribute('data-occurred-at');
            const cropName = this.getAttribute('data-crop-name');
            const plotName = this.getAttribute('data-plot-name');
            const reference = this.getAttribute('data-reference');
            const created = this.getAttribute('data-created');
            
            openViewModal(id, type, category, amount, occurredAt, cropName, plotName, reference, created);
        });
    });
    
    // Agregar evento de tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeViewModal();
        }
    });
    
    // Agregar evento de clic fuera del modal
    viewModal.addEventListener('click', function(e) {
        if (e.target === viewModal) {
            closeViewModal();
        }
    });
});
</script>

<script>
// Variables globales y utilidades
const formBaseAction = "{{ route('admin.ledger.update', 'ID_PLACEHOLDER') }}";

// --- Lógica del Modal de Edición ---

function openEditModal(id, type, category, amount, occurredAt, cropId, plotId, reference) {
    // 1. Configurar la acción del formulario
    const form = document.getElementById('editForm');
    // Generar la URL correcta usando la ruta base y el ID
    // Es mejor regenerar la URL cada vez que reemplazar sobre la variable global const que ya tiene el ID anterior si se abre 2 veces
    let actionUrl = formBaseAction.replace('ID_PLACEHOLDER', id);
    form.action = actionUrl;
    
    // 2. Llenar los campos
    document.getElementById('edit_type').value = type;
    document.getElementById('edit_category').value = category;
    document.getElementById('edit_amount').value = amount;
    document.getElementById('edit_occurred_at').value = occurredAt;
    document.getElementById('edit_reference').value = reference || '';
    
    // 3. Manejar Cultivos y Lotes (dependientes)
    const cropSelect = document.getElementById('edit_crop_id');
    const plotSelect = document.getElementById('edit_plot_id');
    
    // Resetear y establecer valores
    cropSelect.value = cropId || '';
    
    // Disparar evento change para actualizar lotes, luego establecer el lote
    // Necesitamos hacerlo manualmente o esperar a que se procese
    if (cropId) {
        // En este caso simple, como no filtramos la lista de lotes por JS (solo seleccionamos), 
        // simplemente seteamos los valores. Si hubiera lógica de filtrado, habría que llamarla.
        // La lógica original en create.blade.php seleccionaba automáticamente el lote asociado al cultivo.
        // Aquí respetamos lo que viene de la BD.
        
        // Simular la lógica de selección automática si se cambia el cultivo
        // Pero primero establecemos el valor actual de la BD
    }
    plotSelect.value = plotId || '';
    
    // 4. Mostrar el modal
    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

// Lógica de dependencia Cultivo -> Lote en el modal de edición
document.addEventListener('DOMContentLoaded', function() {
    const editCropSelect = document.getElementById('edit_crop_id');
    const editPlotSelect = document.getElementById('edit_plot_id');
    
    if (editCropSelect && editPlotSelect) {
        editCropSelect.addEventListener('change', function() {
            const selectedCrop = this.options[this.selectedIndex];
            const plotId = selectedCrop.getAttribute('data-plot-id');
            
            // Si el usuario cambia el cultivo y ese cultivo tiene un lote asociado por defecto,
            // podemos sugerirlo. Pero en edición, tal vez el usuario ya tenía otro lote.
            // Para mantener consistencia con 'create', lo sugerimos.
            
            if (plotId) {
                // Buscar si existe ese lote en el select
                const plotOption = Array.from(editPlotSelect.options).find(opt => opt.value == plotId);
                if (plotOption) {
                    editPlotSelect.value = plotId;
                }
            } else if (!this.value) {
                // Si limpia el cultivo, ¿limpiamos el lote?
                // editPlotSelect.value = ''; // Opcional
            }
        });
    }

    // Event listeners para los botones de editar
    document.querySelectorAll('.edit-entry-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-entry-id');
            const type = this.getAttribute('data-type');
            const category = this.getAttribute('data-category');
            const amount = this.getAttribute('data-amount');
            const occurredAt = this.getAttribute('data-occurred-at');
            const cropId = this.getAttribute('data-crop-id');
            const plotId = this.getAttribute('data-plot-id'); // Note: data-plot-id might be empty string
            const reference = this.getAttribute('data-reference');
            
            openEditModal(id, type, category, amount, occurredAt, cropId, plotId, reference);
        });
    });
    
    // Cerrar modal al hacer clic fuera
    const editModal = document.getElementById('editModal');
    if (editModal) {
        editModal.addEventListener('click', function(e) {
            if (e.target === editModal) {
                closeEditModal();
            }
        });
    }
});

// MODIFICACIÓN PARA AJAX SUBMIT (Mejor experiencia de usuario y persistencia de errores)
document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Deshabilitar botón
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span> Guardando...';
    
    // Limpiar errores previos
    form.querySelectorAll('.text-red-500').forEach(el => el.remove());
    form.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.errors) {
            // Manejar errores de validación
            Object.keys(data.errors).forEach(field => {
                const input = document.getElementById('edit_' + field);
                if (input) {
                    input.classList.add('border-red-500');
                    const errorMsg = document.createElement('p');
                    errorMsg.className = 'text-red-500 text-xs mt-1';
                    errorMsg.textContent = data.errors[field][0];
                    input.parentNode.appendChild(errorMsg);
                }
            });
        } else if (data.status === 'success' || data.message) {
            // Éxito - Recargar página o actualizar UI
            window.location.reload();
        } else {
            // Otro tipo de éxito (redirect)
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al guardar. Por favor intente nuevamente.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
});

</script>
@endsection
