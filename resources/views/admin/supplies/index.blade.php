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
    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('admin.supply-consumptions.index') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
            <i data-lucide="activity" class="w-4 h-4"></i>
            <span>Ver Consumos</span>
        </a>
        <a href="{{ route('admin.supplies.create') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Nuevo Insumo</span>
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
        <button type="submit" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded inline-flex items-center gap-2">
            <i data-lucide="search" class="w-4 h-4"></i>
            <span>Filtrar</span>
        </button>
    </form>

    <!-- Tabla de insumos -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-emerald-800 border-b">
                    <th class="py-3 pr-4">Nombre</th>
                    <th class="py-3 pr-4">Unidad</th>
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
                        <div class="font-medium text-gray-900 supply-name">{{ $supply->name }}</div>
                    </td>
                    <td class="py-3 pr-4 supply-unit">
                        {{ $supply->unit }}
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
                    <td colspan="6" class="py-6 text-center text-emerald-800/70">No hay insumos registrados</td>
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
            <button type="button" onclick="closeViewModal()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded">
                <i data-lucide="x" class="w-4 h-4 inline mr-2"></i>
                Cerrar
            </button>
        </div>
    </div>
</div>

<!-- Modal de edición -->
<div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-6 w-full max-w-2xl mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-emerald-700">Editar Insumo</h3>
            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="editForm" class="space-y-4">
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
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded" id="updateButton">
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
function openEditModal(id, name, unit, unit_cost, status) {
    currentSupplyId = id;
    
    // Llenar los campos del formulario
    document.getElementById('editName').value = name;
    document.getElementById('editUnit').value = unit;
    document.getElementById('editUnitCost').value = unit_cost;
    document.getElementById('editStatus').value = status;
    
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
        
        const response = await fetch(`/admin/supplies/${currentSupplyId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (response.ok) {
            // Actualizar la tabla en tiempo real
            updateTableRow();
            
            // Cerrar el modal
            closeEditModal();
            
            // Mostrar mensaje de éxito
            showSuccessMessage();
        } else {
            alert('Error al actualizar el insumo');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al actualizar el insumo');
    } finally {
        // Restaurar el botón
        updateButton.innerHTML = originalText;
        updateButton.disabled = false;
    }
}

// Función para actualizar la fila en la tabla
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
            
            openEditModal(id, name, unit, unit_cost, status);
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
