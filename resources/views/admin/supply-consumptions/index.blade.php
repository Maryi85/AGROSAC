@extends('admin.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Gestión de Consumos de Insumos</h2>
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
        <a href="{{ route('admin.supplies.index') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
            <i data-lucide="flask-round" class="w-4 h-4"></i>
            <span>Ver Insumos</span>
        </a>
        <a href="{{ route('admin.supply-consumptions.create') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Registrar Consumo</span>
        </a>
    </div>

    <!-- Filtros de búsqueda -->
    <form method="GET" class="mb-4 flex gap-2 items-end flex-wrap">
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Insumo</label>
            <select name="supply_id" class="border border-emerald-200 rounded px-3 py-2">
                <option value="all">Todos los insumos</option>
                @foreach($supplies as $supply)
                    <option value="{{ $supply->id }}" {{ request('supply_id') == $supply->id ? 'selected' : '' }}>
                        {{ $supply->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Cultivo</label>
            <select name="crop_id" class="border border-emerald-200 rounded px-3 py-2">
                <option value="all">Todos los cultivos</option>
                @foreach($crops as $crop)
                    <option value="{{ $crop->id }}" {{ request('crop_id') == $crop->id ? 'selected' : '' }}>
                        {{ $crop->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Lote</label>
            <select name="plot_id" class="border border-emerald-200 rounded px-3 py-2">
                <option value="all">Todos los lotes</option>
                @foreach($plots as $plot)
                    <option value="{{ $plot->id }}" {{ request('plot_id') == $plot->id ? 'selected' : '' }}>
                        {{ $plot->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Desde</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="border border-emerald-200 rounded px-3 py-2" />
        </div>
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Hasta</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="border border-emerald-200 rounded px-3 py-2" />
        </div>
        <button type="submit" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded inline-flex items-center gap-2">
            <i data-lucide="search" class="w-4 h-4"></i>
            <span>Filtrar</span>
        </button>
    </form>

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
                        <div class="text-sm text-gray-900 font-medium">${{ number_format($consumption->total_cost, 2) }}</div>
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
                                    data-total-cost="{{ $consumption->total_cost }}"
                                    data-crop-name="{{ $consumption->crop ? $consumption->crop->name : 'No especificado' }}"
                                    data-plot-name="{{ $consumption->plot ? $consumption->plot->name : 'No especificado' }}"
                                    data-task-id="{{ $consumption->task ? $consumption->task->id : 'No especificado' }}"
                                    data-used-at="{{ $consumption->used_at->format('d/m/Y') }}"
                                    data-created="{{ $consumption->created_at->format('d/m/Y H:i') }}"
                                    title="Ver detalles">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                            
                            <!-- Editar -->
                            <a href="{{ route('admin.supply-consumptions.edit', $consumption) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 border border-emerald-200 rounded hover:bg-emerald-50 text-emerald-600" 
                               title="Editar">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                            
                            <!-- Eliminar -->
                            <form method="POST" action="{{ route('admin.supply-consumptions.destroy', $consumption) }}" class="inline" data-confirm="true" data-message="¿Eliminar este consumo? Esta acción no se puede deshacer.">
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
                    <td colspan="7" class="py-6 text-center text-emerald-800/70">No hay consumos registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $consumptions->links() }}</div>
</div>

<!-- Modal de detalles -->
<div id="viewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-6 w-full max-w-2xl mx-4">
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

<script>
// Función para abrir el modal de detalles
function openViewModal(id, supplyName, supplyUnit, supplyUnitCost, qty, totalCost, cropName, plotName, taskId, usedAt, created) {
    // Llenar los campos del modal de detalles
    document.getElementById('viewSupplyName').textContent = supplyName;
    document.getElementById('viewSupplyUnit').textContent = supplyUnit;
    document.getElementById('viewSupplyUnitCost').textContent = '$' + parseFloat(supplyUnitCost).toFixed(2);
    document.getElementById('viewQty').textContent = parseFloat(qty).toFixed(3);
    document.getElementById('viewTotalCost').textContent = '$' + parseFloat(totalCost).toFixed(2);
    document.getElementById('viewCropName').textContent = cropName;
    document.getElementById('viewPlotName').textContent = plotName;
    document.getElementById('viewTaskId').textContent = taskId;
    document.getElementById('viewUsedAt').textContent = usedAt;
    document.getElementById('viewCreated').textContent = created;
    
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
    document.querySelectorAll('.view-consumption-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-consumption-id');
            const supplyName = this.getAttribute('data-supply-name');
            const supplyUnit = this.getAttribute('data-supply-unit');
            const supplyUnitCost = this.getAttribute('data-supply-unit-cost');
            const qty = this.getAttribute('data-qty');
            const totalCost = this.getAttribute('data-total-cost');
            const cropName = this.getAttribute('data-crop-name');
            const plotName = this.getAttribute('data-plot-name');
            const taskId = this.getAttribute('data-task-id');
            const usedAt = this.getAttribute('data-used-at');
            const created = this.getAttribute('data-created');
            
            openViewModal(id, supplyName, supplyUnit, supplyUnitCost, qty, totalCost, cropName, plotName, taskId, usedAt, created);
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
@endsection
