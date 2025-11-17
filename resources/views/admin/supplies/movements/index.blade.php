@extends('admin.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Movimientos de Inventario</h2>
    <a href="{{ route('admin.supplies.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-emerald-300 rounded text-emerald-700 hover:bg-emerald-100">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Volver a Insumos</span>
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

    <!-- Botones de acción -->
    <div class="mb-6 flex justify-start gap-4">
        <a href="{{ route('admin.supply-movements.create', ['type' => 'entry']) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-100 hover:bg-green-200 text-green-700 border border-green-200 rounded">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Entrada de Insumo</span>
        </a>
        <a href="{{ route('admin.supply-movements.create', ['type' => 'exit']) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 border border-red-200 rounded">
            <i data-lucide="minus-circle" class="w-4 h-4"></i>
            <span>Salida de Insumo</span>
        </a>
    </div>

    <!-- Tabla de movimientos -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-emerald-800 border-b">
                    <th class="py-3 pr-4">Fecha</th>
                    <th class="py-3 pr-4">Tipo</th>
                    <th class="py-3 pr-4">Insumo</th>
                    <th class="py-3 pr-4">Stock Disponible</th>
                    <th class="py-3 pr-4">Cantidad</th>
                    <th class="py-3 pr-4">Costo Unitario</th>
                    <th class="py-3 pr-4">Total</th>
                    <th class="py-3 pr-4">Motivo</th>
                    <th class="py-3 pr-4">Registrado por</th>
                    <th class="py-3 pr-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($movements as $movement)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 pr-4">
                        <div class="font-medium text-gray-900">{{ $movement->movement_date->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $movement->created_at->format('H:i') }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="px-2 py-1 text-xs rounded {{ $movement->isEntry() ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $movement->isEntry() ? 'Entrada' : 'Salida' }}
                        </span>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="font-medium text-gray-900">{{ $movement->supply->name }}</div>
                        <div class="text-xs text-gray-500">{{ $movement->supply->unit }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold {{ $movement->supply->isLowStock() ? 'text-red-600' : 'text-gray-900' }}">
                                {{ number_format($movement->supply->current_stock, 3) }}
                            </span>
                            @if($movement->supply->isLowStock())
                                <i data-lucide="alert-triangle" class="w-4 h-4 text-red-500" title="Stock bajo"></i>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500">Mín: {{ number_format($movement->supply->min_stock, 3) }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="font-medium text-gray-900">{{ number_format($movement->quantity, 3) }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-600">${{ number_format($movement->unit_cost, 2) }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="font-semibold text-gray-900">${{ number_format($movement->total_cost, 2) }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-600 max-w-xs truncate" title="{{ $movement->reason }}">
                            {{ $movement->reason ?? '—' }}
                        </div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-600">{{ $movement->createdBy->name }}</div>
                    </td>
                    <td class="py-3 pr-4 text-right">
                        <div class="flex items-center gap-1 justify-end">
                            <!-- Ver detalles -->
                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 border border-blue-200 rounded hover:bg-blue-50 text-blue-600 view-movement-btn" 
                                    data-movement-id="{{ $movement->id }}"
                                    data-movement-date="{{ $movement->movement_date->format('d/m/Y') }}"
                                    data-movement-type="{{ $movement->isEntry() ? 'Entrada' : 'Salida' }}"
                                    data-supply-name="{{ $movement->supply->name }}"
                                    data-supply-unit="{{ $movement->supply->unit }}"
                                    data-quantity="{{ number_format($movement->quantity, 3) }}"
                                    data-unit-cost="{{ number_format($movement->unit_cost, 2) }}"
                                    data-total-cost="{{ number_format($movement->total_cost, 2) }}"
                                    data-reason="{{ $movement->reason ?? '' }}"
                                    data-notes="{{ $movement->notes ?? '' }}"
                                    data-crop-name="{{ $movement->crop->name ?? '' }}"
                                    data-plot-name="{{ $movement->plot->name ?? '' }}"
                                    data-task-description="{{ $movement->task->description ?? '' }}"
                                    data-created-by="{{ $movement->createdBy->name }}"
                                    data-created-at="{{ $movement->created_at->format('d/m/Y H:i') }}"
                                    title="Ver detalles">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                            
                            <!-- Editar -->
                            <a href="{{ route('admin.supply-movements.edit', $movement) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 border border-emerald-200 rounded hover:bg-emerald-50 text-emerald-600" 
                               title="Editar">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                            
                            <!-- Eliminar -->
                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 border border-red-200 rounded hover:bg-red-50 text-red-600 delete-movement-btn" 
                                    data-movement-id="{{ $movement->id }}"
                                    data-supply-name="{{ $movement->supply->name }}"
                                    title="Eliminar">
                                <i data-lucide="trash" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="py-6 text-center text-emerald-800/70">No hay movimientos registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $movements->links() }}</div>
</div>

<!-- Modal de detalles -->
<div id="viewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-6 w-full max-w-2xl mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Detalles del Movimiento</h3>
            <button type="button" onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha del Movimiento</label>
                    <p id="viewMovementDate" class="text-sm text-gray-900"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tipo de Movimiento</label>
                    <p id="viewMovementType" class="text-sm text-gray-900"></p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Insumo</label>
                    <p id="viewSupplyName" class="text-sm text-gray-900"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cantidad</label>
                    <p id="viewQuantity" class="text-sm text-gray-900"></p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Costo Unitario</label>
                    <p id="viewUnitCost" class="text-sm text-gray-900"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Costo Total</label>
                    <p id="viewTotalCost" class="text-sm text-gray-900 font-semibold"></p>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Motivo</label>
                <p id="viewReason" class="text-sm text-gray-900"></p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Notas</label>
                <p id="viewNotes" class="text-sm text-gray-900"></p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cultivo</label>
                    <p id="viewCropName" class="text-sm text-gray-900"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Lote</label>
                    <p id="viewPlotName" class="text-sm text-gray-900"></p>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Tarea</label>
                <p id="viewTaskDescription" class="text-sm text-gray-900"></p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Registrado por</label>
                    <p id="viewCreatedBy" class="text-sm text-gray-900"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de Registro</label>
                    <p id="viewCreatedAt" class="text-sm text-gray-900"></p>
                </div>
            </div>
        </div>
        
        <div class="flex justify-end gap-2 mt-6">
            <button type="button" onclick="closeViewModal()" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
                Cerrar
            </button>
        </div>
    </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-6 w-full max-w-md mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="p-2 bg-red-100 rounded-full">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Confirmar Eliminación</h3>
        </div>
        
        <p class="text-gray-600 mb-6">
            ¿Está seguro de que desea eliminar este movimiento de inventario? 
            Esta acción actualizará automáticamente el stock del insumo.
        </p>
        
        <div class="flex justify-end gap-2">
            <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
                Cancelar
            </button>
            <button type="button" onclick="confirmDelete()" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 border border-red-200 rounded transition-colors">
                Eliminar
            </button>
        </div>
    </div>
</div>

<script>
let deleteMovementId = null;

// Función para abrir el modal de detalles
function openViewModal(movementId, movementDate, movementType, supplyName, supplyUnit, quantity, unitCost, totalCost, reason, notes, cropName, plotName, taskDescription, createdBy, createdAt) {
    document.getElementById('viewMovementDate').textContent = movementDate;
    document.getElementById('viewMovementType').textContent = movementType;
    document.getElementById('viewSupplyName').textContent = supplyName + ' (' + supplyUnit + ')';
    document.getElementById('viewQuantity').textContent = quantity;
    document.getElementById('viewUnitCost').textContent = '$' + unitCost;
    document.getElementById('viewTotalCost').textContent = '$' + totalCost;
    document.getElementById('viewReason').textContent = reason || '—';
    document.getElementById('viewNotes').textContent = notes || '—';
    document.getElementById('viewCropName').textContent = cropName || '—';
    document.getElementById('viewPlotName').textContent = plotName || '—';
    document.getElementById('viewTaskDescription').textContent = taskDescription || '—';
    document.getElementById('viewCreatedBy').textContent = createdBy;
    document.getElementById('viewCreatedAt').textContent = createdAt;
    
    document.getElementById('viewModal').style.display = 'flex';
}

// Función para cerrar el modal de detalles
function closeViewModal() {
    document.getElementById('viewModal').style.display = 'none';
}

// Función para abrir el modal de confirmación de eliminación
function openDeleteModal(movementId, supplyName) {
    deleteMovementId = movementId;
    document.getElementById('deleteModal').style.display = 'flex';
}

// Función para cerrar el modal de confirmación de eliminación
function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    deleteMovementId = null;
}

// Función para confirmar la eliminación
async function confirmDelete() {
    if (!deleteMovementId) return;
    
    const deleteButton = document.querySelector('#deleteModal button[onclick="confirmDelete()"]');
    const originalText = deleteButton.innerHTML;
    
    // Mostrar estado de carga
    deleteButton.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 inline mr-2 animate-spin"></i><span>Eliminando...</span>';
    deleteButton.disabled = true;
    
    try {
        const response = await fetch(`/admin/supply-movements/${deleteMovementId}`, {
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
            showSuccessMessage('Movimiento de inventario eliminado correctamente');
            
            // Recargar la página
            window.location.reload();
        } else {
            const errorData = await response.json();
            showErrorMessage(errorData.message || 'Error al eliminar el movimiento');
        }
    } catch (error) {
        console.error('Error:', error);
        showErrorMessage('Error al eliminar el movimiento: ' + error.message);
    } finally {
        // Restaurar el botón
        deleteButton.innerHTML = originalText;
        deleteButton.disabled = false;
    }
}

// Función para mostrar mensaje de éxito
function showSuccessMessage(message = 'Operación realizada correctamente') {
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

// Inicialización cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    // Agregar eventos a los botones de ver detalles
    document.querySelectorAll('.view-movement-btn').forEach(button => {
        button.addEventListener('click', function() {
            const movementId = this.getAttribute('data-movement-id');
            const movementDate = this.getAttribute('data-movement-date');
            const movementType = this.getAttribute('data-movement-type');
            const supplyName = this.getAttribute('data-supply-name');
            const supplyUnit = this.getAttribute('data-supply-unit');
            const quantity = this.getAttribute('data-quantity');
            const unitCost = this.getAttribute('data-unit-cost');
            const totalCost = this.getAttribute('data-total-cost');
            const reason = this.getAttribute('data-reason');
            const notes = this.getAttribute('data-notes');
            const cropName = this.getAttribute('data-crop-name');
            const plotName = this.getAttribute('data-plot-name');
            const taskDescription = this.getAttribute('data-task-description');
            const createdBy = this.getAttribute('data-created-by');
            const createdAt = this.getAttribute('data-created-at');
            
            openViewModal(movementId, movementDate, movementType, supplyName, supplyUnit, quantity, unitCost, totalCost, reason, notes, cropName, plotName, taskDescription, createdBy, createdAt);
        });
    });
    
    // Agregar eventos a los botones de eliminar
    document.querySelectorAll('.delete-movement-btn').forEach(button => {
        button.addEventListener('click', function() {
            const movementId = this.getAttribute('data-movement-id');
            const supplyName = this.getAttribute('data-supply-name');
            
            openDeleteModal(movementId, supplyName);
        });
    });
});
</script>
@endsection
