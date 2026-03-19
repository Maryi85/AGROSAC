@extends('admin.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Gestión de Consumos de Insumos</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-4">
    {{-- Responsive: stack on mobile --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 gap-3">
        <x-search-bar placeholder="Buscar por insumo, cultivo o lote..." />
        <a href="{{ route('admin.supply-consumptions.create') }}" class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Registrar Consumo</span>
        </a>
    </div>

    <!-- Tabla de consumos -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-emerald-800 border-b">
                    <th class="py-3 pr-4">Insumo</th>
                    <th class="py-3 pr-4">Cantidad</th>
                    <th class="py-3 pr-4">Costo Total</th>
                    <th class="py-3 pr-4">Cultivo</th>
                    <th class="py-3 pr-4">Lote</th>
                    <th class="py-3 pr-4">Fecha de Uso</th>
                    <th class="py-3 pr-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($consumptions as $consumption)
                <tr class="border-b hover:bg-gray-50" data-consumption-id="{{ $consumption->id }}">
                    <td class="py-3 pr-4">
                        <div class="font-medium text-gray-900">{{ $consumption->supply->name }}</div>
                        <div class="text-xs text-gray-500">{{ $consumption->supply->unit }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-900 font-medium">{{ number_format($consumption->qty, 3) }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-900 font-medium">${{ number_format($consumption->total_cost, 0) }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        @if($consumption->crop)
                            <div class="text-sm text-gray-900">{{ $consumption->crop->name }}</div>
                        @else
                            <div class="text-sm text-gray-500">—</div>
                        @endif
                    </td>
                    <td class="py-3 pr-4">
                        @if($consumption->plot)
                            <div class="text-sm text-gray-900">{{ $consumption->plot->name }}</div>
                        @else
                            <div class="text-sm text-gray-500">—</div>
                        @endif
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-900">{{ $consumption->used_at->format('d/m/Y') }}</div>
                    </td>
                    <td class="py-3 pr-4 text-right">
                        <div class="flex items-center gap-1 justify-end">
                            <!-- Ver detalles -->
                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 border border-blue-200 rounded hover:bg-blue-50 text-blue-600 view-consumption-btn" 
                                    data-consumption-id="{{ $consumption->id }}"
                                    data-supply-name="{{ $consumption->supply->name }}"
                                    data-supply-unit="{{ $consumption->supply->unit }}"
                                    data-supply-unit-cost="{{ $consumption->supply->unit_cost }}"
                                    data-qty="{{ $consumption->qty }}"
                                    data-total-cost="{{ (int)$consumption->total_cost }}"
                                    data-crop-name="{{ $consumption->crop ? $consumption->crop->name : 'No especificado' }}"
                                    data-plot-name="{{ $consumption->plot ? $consumption->plot->name : 'No especificado' }}"
                                    data-task-id="{{ $consumption->task ? $consumption->task->id : 'No especificado' }}"
                                    data-used-at="{{ $consumption->used_at->format('d/m/Y') }}"
                                    data-created="{{ $consumption->created_at->format('d/m/Y H:i') }}"
                                    title="Ver detalles">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                            
                            <!-- Editar -->
                            <button type="button" 
                                    class="inline-flex items-center justify-center w-8 h-8 border border-emerald-200 rounded hover:bg-emerald-50 text-emerald-600 edit-consumption-btn" 
                                    data-consumption-id="{{ $consumption->id }}"
                                    data-supply-id="{{ $consumption->supply_id }}"
                                    data-qty="{{ $consumption->qty }}"
                                    data-crop-id="{{ $consumption->crop_id }}"
                                    data-plot-id="{{ $consumption->plot_id }}"
                                    data-task-id="{{ $consumption->task_id }}"
                                    data-used-at="{{ $consumption->used_at->format('Y-m-d') }}"
                                    title="Editar">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </button>
                            
                             <!-- Los registros de consumo no se pueden eliminar -->
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-6 text-center text-emerald-800/70">No hay consumos registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $consumptions->links() }}</div>
</div>

    </div>
</div>

<!-- Modal de detalles -->
<div id="viewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-5 sm:p-6 w-full max-w-2xl mx-4 max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-emerald-700">Detalles del Consumo</h3>
            <button type="button" onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <div class="space-y-6">
            <!-- Información del Insumo -->
            <div class="bg-gray-50 rounded p-4">
                <h4 class="text-md font-semibold text-emerald-700 mb-3">Información del Insumo</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre</label>
                        <p id="viewSupplyName" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Unidad</label>
                        <p id="viewSupplyUnit" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Costo por Unidad</label>
                        <p id="viewSupplyUnitCost" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                </div>
            </div>
            
            <!-- Información del Consumo -->
            <div class="bg-gray-50 rounded p-4">
                <h4 class="text-md font-semibold text-emerald-700 mb-3">Información del Consumo</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cantidad Consumida</label>
                        <p id="viewQty" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Costo Total</label>
                        <p id="viewTotalCost" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha de Uso</label>
                        <p id="viewUsedAt" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                </div>
            </div>
            
            <!-- Información de Aplicación -->
            <div class="bg-gray-50 rounded p-4">
                <h4 class="text-md font-semibold text-emerald-700 mb-3">Información de Aplicación</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cultivo</label>
                        <p id="viewCropName" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Lote</label>
                        <p id="viewPlotName" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tarea Asociada</label>
                        <p id="viewTaskId" class="text-sm text-gray-900 mt-1"></p>
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

<!-- Modal de edición -->
<div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-5 sm:p-6 w-full max-w-2xl mx-4 max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-emerald-700">Editar Consumo de Insumo</h3>
            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form id="editForm" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Insumo -->
                <div class="md:col-span-2">
                    <label for="edit_supply_id" class="block text-sm mb-1 text-emerald-800">Insumo</label>
                    <select id="edit_supply_id" name="supply_id" 
                            class="w-full border border-emerald-200 rounded px-3 py-2" required>
                        @foreach($supplies as $supply)
                            <option value="{{ $supply->id }}" data-unit="{{ $supply->unit }}" data-unit-cost="{{ (int)$supply->unit_cost }}">
                                {{ $supply->name }} ({{ $supply->unit }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Cantidad -->
                <div>
                    <label for="edit_qty" class="block text-sm mb-1 text-emerald-800">Cantidad</label>
                    <input type="number" step="0.001" min="0.001" id="edit_qty" name="qty" 
                           class="w-full border border-emerald-200 rounded px-3 py-2" required />
                </div>
                
                <!-- Fecha de uso -->
                <div>
                    <label for="edit_used_at" class="block text-sm mb-1 text-emerald-800">Fecha de Uso</label>
                    <input type="date" id="edit_used_at" name="used_at" 
                           class="w-full border border-emerald-200 rounded px-3 py-2" required />
                </div>
            </div>

            <!-- Información de costo -->
            <div class="bg-blue-50 border border-blue-200 rounded p-4">
                <h4 class="text-sm font-semibold text-blue-800 mb-2">Información de Costo</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Costo por Unidad</label>
                        <p id="edit-unit-cost-display" class="text-sm text-gray-900 mt-1">$0.00</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Costo Total</label>
                        <p id="edit-total-cost-display" class="text-sm text-gray-900 mt-1 font-semibold">$0.00</p>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Cultivo -->
                <div>
                    <label for="edit_crop_id" class="block text-sm mb-1 text-emerald-800">Cultivo</label>
                    <select id="edit_crop_id" name="crop_id" class="w-full border border-emerald-200 rounded px-3 py-2">
                        <option value="">Ninguno</option>
                        @foreach($crops as $crop)
                            <option value="{{ $crop->id }}">{{ $crop->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Lote -->
                <div>
                    <label for="edit_plot_id" class="block text-sm mb-1 text-emerald-800">Lote</label>
                    <select id="edit_plot_id" name="plot_id" class="w-full border border-emerald-200 rounded px-3 py-2">
                        <option value="">Ninguno</option>
                        @foreach($plots as $plot)
                            <option value="{{ $plot->id }}">{{ $plot->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tarea -->
                <div>
                    <label for="edit_task_id" class="block text-sm mb-1 text-emerald-800">Tarea</label>
                    <select id="edit_task_id" name="task_id" class="w-full border border-emerald-200 rounded px-3 py-2">
                        <option value="">Ninguna</option>
                        @foreach($tasks as $task)
                            <option value="{{ $task->id }}">{{ $task->type }} - {{ $task->scheduled_for->format('d/m/Y') }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            {{-- Responsive: full-width on mobile --}}
            <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                <button type="button" onclick="closeEditModal()" class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-center">
                    Cancelar
                </button>
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-center">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Función para abrir el modal de detalles
function openViewModal(id, supplyName, supplyUnit, supplyUnitCost, qty, totalCost, cropName, plotName, taskId, usedAt, created) {
    document.getElementById('viewSupplyName').textContent = supplyName;
    document.getElementById('viewSupplyUnit').textContent = supplyUnit;
    document.getElementById('viewSupplyUnitCost').textContent = '$' + parseFloat(supplyUnitCost).toFixed(0);
    document.getElementById('viewQty').textContent = parseFloat(qty).toFixed(3);
    document.getElementById('viewTotalCost').textContent = '$' + parseFloat(totalCost).toFixed(0);
    document.getElementById('viewCropName').textContent = cropName;
    document.getElementById('viewPlotName').textContent = plotName;
    document.getElementById('viewTaskId').textContent = taskId;
    document.getElementById('viewUsedAt').textContent = usedAt;
    document.getElementById('viewCreated').textContent = created;
    document.getElementById('viewModal').style.display = 'flex';
}

function closeViewModal() {
    document.getElementById('viewModal').style.display = 'none';
}

// Función para abrir el modal de edición
function openEditModal(id, supplyId, qty, cropId, plotId, taskId, usedAt) {
    const form = document.getElementById('editForm');
    form.action = `/admin/supply-consumptions/${id}`;
    
    document.getElementById('edit_supply_id').value = supplyId;
    document.getElementById('edit_qty').value = qty;
    document.getElementById('edit_used_at').value = usedAt;
    document.getElementById('edit_crop_id').value = cropId || '';
    document.getElementById('edit_plot_id').value = plotId || '';
    document.getElementById('edit_task_id').value = taskId || '';
    
    // Disparar evento de cambio para actualizar costos
    document.getElementById('edit_supply_id').dispatchEvent(new Event('change'));
    
    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

// Función para actualizar costos en el modal de edición
function updateEditCostInfo() {
    const supplySelect = document.getElementById('edit_supply_id');
    const qtyInput = document.getElementById('edit_qty');
    const unitCostDisplay = document.getElementById('edit-unit-cost-display');
    const totalCostDisplay = document.getElementById('edit-total-cost-display');
    
    const selectedOption = supplySelect.options[supplySelect.selectedIndex];
    const qty = parseFloat(qtyInput.value) || 0;
    
    if (selectedOption && selectedOption.value) {
        const unitCostStr = selectedOption.getAttribute('data-unit-cost');
        const unitCost = unitCostStr ? parseFloat(unitCostStr) : 0;
        
        unitCostDisplay.textContent = `$${unitCost.toFixed(0)}`;
        const totalCost = qty * unitCost;
        totalCostDisplay.textContent = `$${totalCost.toFixed(0)}`;
    } else {
        unitCostDisplay.textContent = '$0.00';
        totalCostDisplay.textContent = '$0.00';
    }
}

// Inicialización cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    // Asegurar que los modales estén ocultos
    const viewModal = document.getElementById('viewModal');
    const editModal = document.getElementById('editModal');
    
    if (viewModal) viewModal.style.display = 'none';
    if (editModal) editModal.style.display = 'none';

    // Eventos para el modal de edición
    const editSupplySelect = document.getElementById('edit_supply_id');
    const editQtyInput = document.getElementById('edit_qty');
    const editCropSelect = document.getElementById('edit_crop_id');
    const editPlotSelect = document.getElementById('edit_plot_id');

    if (editSupplySelect) {
        editSupplySelect.addEventListener('change', updateEditCostInfo);
    }
    if (editQtyInput) {
        editQtyInput.addEventListener('input', updateEditCostInfo);
    }

    // Lógica de cultivo-lote en el modal
    if (editCropSelect && editPlotSelect) {
        editCropSelect.addEventListener('change', function() {
            const selectedCrop = this.options[this.selectedIndex];
            const plotId = selectedCrop.getAttribute('data-plot-id');
            if (plotId) {
                editPlotSelect.value = plotId;
            }
        });
    }
    
    // Botones de vista
    document.querySelectorAll('.view-consumption-btn').forEach(button => {
        button.addEventListener('click', function() {
            openViewModal(
                this.getAttribute('data-consumption-id'),
                this.getAttribute('data-supply-name'),
                this.getAttribute('data-supply-unit'),
                this.getAttribute('data-supply-unit-cost'),
                this.getAttribute('data-qty'),
                this.getAttribute('data-total-cost'),
                this.getAttribute('data-crop-name'),
                this.getAttribute('data-plot-name'),
                this.getAttribute('data-task-id'),
                this.getAttribute('data-used-at'),
                this.getAttribute('data-created')
            );
        });
    });
    
    // Botones de edición
    document.querySelectorAll('.edit-consumption-btn').forEach(button => {
        button.addEventListener('click', function() {
            openEditModal(
                this.getAttribute('data-consumption-id'),
                this.getAttribute('data-supply-id'),
                this.getAttribute('data-qty'),
                this.getAttribute('data-crop-id'),
                this.getAttribute('data-plot-id'),
                this.getAttribute('data-task-id'),
                this.getAttribute('data-used-at')
            );
        });
    });
    
    // Cerrar con Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeViewModal();
            closeEditModal();
        }
    });

    // Clic fuera para cerrar
    window.onclick = function(event) {
        if (event.target == viewModal) closeViewModal();
        if (event.target == editModal) closeEditModal();
    }
});
</script>
@endsection
