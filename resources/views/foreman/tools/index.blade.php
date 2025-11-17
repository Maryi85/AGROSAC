@extends('foreman.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Gestión de Inventario</h2>
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
            <a href="{{ route('foreman.tools.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded-lg font-medium transition-colors">
                <i data-lucide="plus" class="w-5 h-5"></i>
                <span>Nueva Herramienta</span>
            </a>
            <a href="{{ route('foreman.tool-entries.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-100 hover:bg-blue-200 text-blue-700 border border-blue-200 rounded-lg font-medium transition-colors">
                <i data-lucide="package" class="w-5 h-5"></i>
                <span>Gestionar Entradas</span>
            </a>
            <a href="{{ route('foreman.tool-damage.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-orange-100 hover:bg-orange-200 text-orange-700 border border-orange-200 rounded-lg font-medium transition-colors">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                <span>Daños y Pérdidas</span>
            </a>
        </div>
        <a href="{{ route('foreman.tools.pdf', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 border border-red-200 rounded-lg font-medium transition-colors">
            <i data-lucide="file-text" class="w-5 h-5"></i>
            <span>Descargar PDF</span>
        </a>
    </div>

    <!-- Filtros de búsqueda -->
    <form method="GET" class="mb-4 flex gap-2 items-end">
        <div class="flex-1">
            <label class="block text-sm mb-1 text-emerald-800">Buscar por nombre</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar herramientas..." class="w-full border border-emerald-200 rounded px-3 py-2" />
        </div>
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Categoría</label>
            <select name="category" class="border border-emerald-200 rounded px-3 py-2">
                <option value="all">Todas las categorías</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $category)) }}
                    </option>
                @endforeach
            </select>
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

    <!-- Tabla de herramientas -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-emerald-800 border-b">
                    <th class="py-3 pr-4">Foto</th>
                    <th class="py-3 pr-4">Nombre</th>
                    <th class="py-3 pr-4">Categoría</th>
                    <th class="py-3 pr-4">Total Entradas</th>
                    <th class="py-3 pr-4">Disponible</th>
                    <th class="py-3 pr-4">Dañadas</th>
                    <th class="py-3 pr-4">Perdidas</th>
                    <th class="py-3 pr-4">Estado</th>
                    <th class="py-3 pr-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tools as $tool)
                <tr class="border-b hover:bg-gray-50" data-tool-id="{{ $tool->id }}">
                    <td class="py-3 pr-4">
                        @if(!empty($tool->photo))
                            <img src="{{ asset('storage/' . $tool->photo) }}" alt="{{ $tool->name }}" class="w-16 h-16 object-cover rounded border border-emerald-200" onerror="this.onerror=null; this.src=''; this.style.display='none'; this.nextElementSibling.style.display='flex';">
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
                        <div class="font-medium text-gray-900 tool-name">{{ $tool->name }}</div>
                        @if($tool->brand)
                            <div class="text-xs text-gray-500">{{ $tool->brand }}</div>
                        @endif
                    </td>
                    <td class="py-3 pr-4 tool-category">
                        {{ ucfirst(str_replace('_', ' ', $tool->category)) }}
                    </td>
                    <td class="py-3 pr-4">
                        <div class="font-semibold text-gray-900">{{ $tool->total_entries }}</div>
                        <div class="text-xs text-gray-500">Total ingresado</div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="font-semibold {{ $tool->available_qty > 0 ? 'text-green-600' : 'text-gray-500' }}">
                            {{ $tool->available_qty }}
                        </div>
                        <div class="text-xs text-gray-500">Para préstamo</div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="font-semibold {{ $tool->damaged_qty > 0 ? 'text-orange-600' : 'text-gray-500' }}">
                            {{ $tool->damaged_qty }}
                        </div>
                        <div class="text-xs text-gray-500">Dañadas</div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="font-semibold {{ $tool->lost_qty > 0 ? 'text-red-600' : 'text-gray-500' }}">
                            {{ $tool->lost_qty }}
                        </div>
                        <div class="text-xs text-gray-500">Perdidas</div>
                    </td>
                    <td class="py-3 pr-4">
                        @php
                            $inventoryStatus = $tool->inventory_status ?? 'available';
                            $statusClasses = [
                                'available' => 'bg-green-100 text-green-700',
                                'damaged' => 'bg-orange-100 text-orange-700',
                                'lost' => 'bg-red-100 text-red-700',
                                'empty' => 'bg-gray-100 text-gray-700',
                            ];
                            $statusLabels = [
                                'available' => 'Disponible',
                                'damaged' => 'Dañada',
                                'lost' => 'Perdida',
                                'empty' => 'Vacía',
                            ];
                        @endphp
                        <span class="px-2 py-1 text-xs rounded {{ $statusClasses[$inventoryStatus] }}">
                            {{ $statusLabels[$inventoryStatus] }}
                        </span>
                    </td>
                    <td class="py-3 pr-4 text-right">
                        <div class="flex items-center gap-1 justify-end">
                            <!-- Ver detalles -->
                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 border border-blue-200 rounded hover:bg-blue-100 text-blue-600 view-tool-btn" 
                                    data-tool-id="{{ $tool->id }}"
                                    data-tool-name="{{ $tool->name }}"
                                    data-tool-category="{{ $tool->category }}"
                                    data-tool-status="{{ $tool->status }}"
                                    data-tool-total-entries="{{ $tool->total_entries }}"
                                    data-tool-available-qty="{{ $tool->available_qty }}"
                                    data-tool-created="{{ $tool->created_at->format('d/m/Y H:i') }}"
                                    data-tool-updated="{{ $tool->updated_at->format('d/m/Y H:i') }}"
                                    title="Ver detalles">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                            
                            <!-- Gestionar entradas -->
                            <a href="{{ route('foreman.tool-entries.create', ['tool_id' => $tool->id]) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 border border-purple-200 rounded hover:bg-purple-100 text-purple-600" 
                               title="Gestionar entradas">
                                <i data-lucide="package" class="w-4 h-4"></i>
                            </a>
                            
                            <!-- Registrar daño/pérdida -->
                            <a href="{{ route('foreman.tool-damage.create', ['tool_id' => $tool->id]) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 border border-orange-200 rounded hover:bg-orange-100 text-orange-600" 
                               title="Registrar daño o pérdida">
                                <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                            </a>
                            
                            <!-- Editar -->
                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 border border-emerald-200 rounded hover:bg-emerald-100 text-emerald-600 edit-tool-btn" 
                                    data-tool-id="{{ $tool->id }}"
                                    data-tool-name="{{ $tool->name }}"
                                    data-tool-category="{{ $tool->category }}"
                                    data-tool-status="{{ $tool->status }}"
                                    data-tool-total-entries="{{ $tool->total_entries }}"
                                    data-tool-available-qty="{{ $tool->available_qty }}"
                                    title="Editar">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </button>
                            
                            <!-- Eliminar -->
                            @if($tool->loans()->where('status', 'active')->exists())
                                <button class="inline-flex items-center justify-center w-8 h-8 border border-gray-200 rounded opacity-50 cursor-not-allowed bg-gray-100 text-gray-400" title="No se puede eliminar una herramienta con préstamos activos" disabled>
                                    <i data-lucide="trash" class="w-4 h-4"></i>
                                </button>
                            @else
                                <form method="POST" action="{{ route('foreman.tools.destroy', $tool) }}" class="inline" data-confirm="true" data-message="¿Eliminar esta herramienta? Esta acción no se puede deshacer.">
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
                    <td colspan="9" class="py-6 text-center text-emerald-800/70">No hay herramientas registradas</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $tools->links() }}</div>
</div>

<!-- Modal de detalles -->
<div id="viewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-6 w-full max-w-2xl mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-emerald-700">Detalles de la Herramienta</h3>
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
                        <label class="block text-sm font-medium text-gray-700">Categoría</label>
                        <p id="viewCategory" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estado</label>
                        <p id="viewStatus" class="text-sm mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cantidad Total</label>
                        <p id="viewTotalQty" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cantidad Disponible</label>
                        <p id="viewAvailableQty" class="text-sm text-gray-900 mt-1"></p>
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
            <h3 class="text-lg font-semibold text-emerald-700">Editar Herramienta</h3>
            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="editForm" class="space-y-4">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="PUT">
            
            <!-- Nombre -->
            <div>
                <label class="block text-sm mb-1 text-emerald-800">Nombre</label>
                <input type="text" name="name" id="editName" class="w-full border border-emerald-200 rounded px-3 py-2" required />
            </div>
            
            <!-- Categoría -->
            <div>
                <label class="block text-sm mb-1 text-emerald-800">Categoría</label>
                <select name="category" id="editCategory" class="w-full border border-emerald-200 rounded px-3 py-2">
                    <option value="herramientas_manuales">Herramientas Manuales</option>
                    <option value="herramientas_electricas">Herramientas Eléctricas</option>
                    <option value="equipos_agricolas">Equipos Agrícolas</option>
                    <option value="vehiculos">Vehículos</option>
                    <option value="otros">Otros</option>
                </select>
            </div>
            
            <!-- Estado y Cantidades -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm mb-1 text-emerald-800">Estado</label>
                    <select name="status" id="editStatus" class="w-full border border-emerald-200 rounded px-3 py-2">
                        <option value="operational">Operacional</option>
                        <option value="damaged">Dañado</option>
                        <option value="lost">Perdido</option>
                        <option value="retired">Retirado</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1 text-emerald-800">Cantidad Total</label>
                    <input type="number" min="0" name="total_qty" id="editTotalQty" class="w-full border border-emerald-200 rounded px-3 py-2" required />
                </div>
                <div>
                    <label class="block text-sm mb-1 text-emerald-800">Cantidad Disponible</label>
                    <input type="number" min="0" name="available_qty" id="editAvailableQty" class="w-full border border-emerald-200 rounded px-3 py-2" required />
                </div>
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
let currentToolId = null;

// Función para abrir el modal de edición
function openEditModal(id, name, category, status, total_entries, available_qty) {
    currentToolId = id;
    
    // Llenar los campos del formulario
    document.getElementById('editName').value = name;
    document.getElementById('editCategory').value = category;
    document.getElementById('editStatus').value = status;
    document.getElementById('editTotalQty').value = total_entries;
    document.getElementById('editAvailableQty').value = available_qty;
    
    // Mostrar el modal
    document.getElementById('editModal').style.display = 'flex';
}

// Función para cerrar el modal
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
    currentToolId = null;
}

// Función para abrir el modal de detalles
function openViewModal(id, name, category, status, total_entries, available_qty, created, updated) {
    // Llenar los campos del modal de detalles
    document.getElementById('viewName').textContent = name;
    document.getElementById('viewCategory').textContent = category.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
    document.getElementById('viewTotalQty').textContent = total_entries;
    document.getElementById('viewAvailableQty').textContent = available_qty;
    document.getElementById('viewCreated').textContent = created;
    document.getElementById('viewUpdated').textContent = updated;
    
    // Configurar el estado con el badge apropiado
    const statusElement = document.getElementById('viewStatus');
    const statusLabels = {
        'operational': 'Operacional',
        'damaged': 'Dañado',
        'lost': 'Perdido',
        'retired': 'Retirado'
    };
    
    if (status === 'operational') {
        statusElement.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Operacional</span>';
    } else if (status === 'damaged') {
        statusElement.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">Dañado</span>';
    } else if (status === 'lost') {
        statusElement.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-orange-100 text-orange-700">Perdido</span>';
    } else {
        statusElement.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Retirado</span>';
    }
    
    // Mostrar el modal
    document.getElementById('viewModal').style.display = 'flex';
}

// Función para cerrar el modal de detalles
function closeViewModal() {
    document.getElementById('viewModal').style.display = 'none';
}

// Función para actualizar la herramienta
async function updateTool() {
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
        formData.append('category', document.getElementById('editCategory').value);
        formData.append('status', document.getElementById('editStatus').value);
        formData.append('total_qty', document.getElementById('editTotalQty').value);
        formData.append('available_qty', document.getElementById('editAvailableQty').value);
        
        const response = await fetch(`/foreman/tools/${currentToolId}`, {
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
            alert('Error al actualizar la herramienta');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al actualizar la herramienta');
    } finally {
        // Restaurar el botón
        updateButton.innerHTML = originalText;
        updateButton.disabled = false;
    }
}

// Función para actualizar la fila en la tabla
function updateTableRow() {
    const row = document.querySelector(`tr[data-tool-id='${currentToolId}']`);
    if (row) {
        const statusCell = row.querySelector('.status-badge');
        if (statusCell) {
            const status = document.getElementById('editStatus').value;
            if (status === 'operational') {
                statusCell.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Operacional</span>';
            } else if (status === 'damaged') {
                statusCell.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">Dañado</span>';
            } else if (status === 'lost') {
                statusCell.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-orange-100 text-orange-700">Perdido</span>';
            } else {
                statusCell.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Retirado</span>';
            }
        }
        
        // Actualizar otros campos
        const nameCell = row.querySelector('.tool-name');
        if (nameCell) {
            nameCell.textContent = document.getElementById('editName').value;
        }
        
        const categoryCell = row.querySelector('.tool-category');
        if (categoryCell) {
            categoryCell.textContent = document.getElementById('editCategory').value.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
        }
        
        const totalQtyCell = row.querySelector('.tool-total-qty');
        if (totalQtyCell) {
            totalQtyCell.textContent = document.getElementById('editTotalQty').value;
        }
        
        const availableQtyCell = row.querySelector('.tool-available-qty');
        if (availableQtyCell) {
            availableQtyCell.textContent = document.getElementById('editAvailableQty').value;
        }
    }
}

// Función para mostrar mensaje de éxito
function showSuccessMessage() {
    const message = document.createElement('div');
    message.className = 'mb-4 p-3 bg-emerald-100 border border-emerald-300 text-emerald-700 rounded';
    message.textContent = 'Herramienta actualizada correctamente';
    
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
    document.querySelectorAll('.edit-tool-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-tool-id');
            const name = this.getAttribute('data-tool-name');
            const category = this.getAttribute('data-tool-category');
            const status = this.getAttribute('data-tool-status');
            const total_entries = this.getAttribute('data-tool-total-entries');
            const available_qty = this.getAttribute('data-tool-available-qty');
            
            openEditModal(id, name, category, status, total_entries, available_qty);
        });
    });
    
    // Agregar eventos a los botones de ver detalles
    document.querySelectorAll('.view-tool-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-tool-id');
            const name = this.getAttribute('data-tool-name');
            const category = this.getAttribute('data-tool-category');
            const status = this.getAttribute('data-tool-status');
            const total_entries = this.getAttribute('data-tool-total-entries');
            const available_qty = this.getAttribute('data-tool-available-qty');
            const created = this.getAttribute('data-tool-created');
            const updated = this.getAttribute('data-tool-updated');
            
            openViewModal(id, name, category, status, total_entries, available_qty, created, updated);
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
        updateTool();
    });
});
</script>
@endsection