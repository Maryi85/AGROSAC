@extends('admin.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">
        Editar Movimiento de Insumo
    </h2>
    <a href="{{ route('admin.supply-movements.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-emerald-300 rounded text-emerald-700 hover:bg-emerald-100">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Volver</span>
    </a>
</div>
@endsection

@section('content')
<div class="bg-white border rounded p-6">
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.supply-movements.update', $supplyMovement) }}" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Insumo -->
            <div>
                <label for="supply_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Insumo <span class="text-red-500">*</span>
                </label>
                <select id="supply_id" 
                        name="supply_id" 
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        required>
                    <option value="">Seleccionar insumo</option>
                    @foreach($supplies as $supply)
                        <option value="{{ $supply->id }}" 
                                data-unit-cost="{{ (int)$supply->unit_cost }}"
                                {{ old('supply_id', $supplyMovement->supply_id) == $supply->id ? 'selected' : '' }}>
                            {{ $supply->name }} - Stock: {{ rtrim(rtrim(number_format($supply->current_stock, 2, '.', ','), '0'), '.') }} {{ $supply->unit }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tipo de Movimiento -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                    Tipo de Movimiento <span class="text-red-500">*</span>
                </label>
                <select id="type" 
                        name="type" 
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        required>
                    <option value="entry" {{ old('type', $supplyMovement->type) == 'entry' ? 'selected' : '' }}>Entrada</option>
                    <option value="exit" {{ old('type', $supplyMovement->type) == 'exit' ? 'selected' : '' }}>Salida</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Fecha del Movimiento -->
            <div>
                <label for="movement_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha del Movimiento <span class="text-red-500">*</span>
                </label>
                <input type="date" 
                       id="movement_date" 
                       name="movement_date" 
                       value="{{ old('movement_date', $supplyMovement->movement_date->format('Y-m-d')) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       required>
            </div>

            <!-- Cantidad -->
            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                    Cantidad <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       id="quantity" 
                       name="quantity" 
                       value="{{ old('quantity', (float)$supplyMovement->quantity) }}"
                       step="0.001"
                       min="0.001"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="0.000"
                       required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Costo por Unidad -->
            <div>
                <label for="unit_cost" class="block text-sm font-medium text-gray-700 mb-2">
                    Costo por Unidad <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       id="unit_cost" 
                       name="unit_cost" 
                       value="{{ old('unit_cost', $supplyMovement->unit_cost) }}"
                       step="1"
                       min="0"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="0.00"
                       required>
            </div>

            <!-- Motivo -->
            <div>
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                    Motivo del Movimiento
                </label>
                <input type="text" 
                       id="reason" 
                       name="reason" 
                       value="{{ old('reason', $supplyMovement->reason) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="Compra, donación, uso en cultivo, etc.">
            </div>
        </div>

        <!-- Notas -->
        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                Notas Adicionales
            </label>
            <textarea id="notes" 
                      name="notes" 
                      rows="3"
                      class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                      placeholder="Información adicional sobre el movimiento...">{{ old('notes', $supplyMovement->notes) }}</textarea>
        </div>

        <!-- Resumen del Costo Total -->
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <span class="text-lg font-medium text-gray-900">Costo Total:</span>
                <span id="total-cost" class="text-2xl font-bold text-emerald-600">${{ number_format($supplyMovement->total_cost, 0) }}</span>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex justify-end gap-4 pt-6 border-t">
            <a href="{{ route('admin.supply-movements.index') }}" 
               class="px-6 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded transition-colors">
                <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                Actualizar Movimiento
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    const unitCostInput = document.getElementById('unit_cost');
    const totalCostElement = document.getElementById('total-cost');
    const supplySelect = document.getElementById('supply_id');
    const typeSelect = document.getElementById('type');

    function calculateTotalCost() {
        const quantity = parseFloat(quantityInput.value) || 0;
        const unitCost = parseFloat(unitCostInput.value) || 0;
        const totalCost = quantity * unitCost;
        totalCostElement.textContent = '$' + totalCost.toFixed(0);
    }

    // Calcular costo total cuando cambien los valores
    quantityInput.addEventListener('input', calculateTotalCost);
    unitCostInput.addEventListener('input', calculateTotalCost);

    // Auto-completar costo unitario cuando se seleccione un insumo
    supplySelect.addEventListener('change', function() {
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            const unitCost = selectedOption.getAttribute('data-unit-cost');
            
            // Si es una salida y hay un precio registrado, auto-completar el precio
            if (typeSelect.value === 'exit' && unitCost && unitCost !== '0' && unitCost !== '') {
                unitCostInput.value = parseFloat(unitCost).toFixed(0);
                calculateTotalCost();
            }
        }
    });

    // Validación para salidas: verificar que no exceda el stock disponible
    function validateStock() {
        if (typeSelect.value === 'exit') {
            const selectedSupplyId = supplySelect.value;
            if (selectedSupplyId) {
                const selectedOption = supplySelect.options[supplySelect.selectedIndex];
                const stockText = selectedOption.text.split('Stock: ')[1];
                if (stockText) {
                    const availableStock = parseFloat(stockText.split(' ')[0].replace(',', ''));
                    const requestedQuantity = parseFloat(quantityInput.value) || 0;
                    
                    // Agregar la cantidad original del movimiento si estamos editando una salida
                    const originalQuantity = {{ $supplyMovement->type === 'exit' ? (float)$supplyMovement->quantity : 0 }};
                    const adjustedStock = availableStock + originalQuantity;
                    
                    if (requestedQuantity > adjustedStock) {
                        quantityInput.setCustomValidity(`No hay suficiente stock. Disponible: ${adjustedStock.toFixed(3)}`);
                        quantityInput.style.borderColor = '#ef4444';
                    } else {
                        quantityInput.setCustomValidity('');
                        quantityInput.style.borderColor = '#d1d5db';
                    }
                }
            }
        } else {
            quantityInput.setCustomValidity('');
            quantityInput.style.borderColor = '#d1d5db';
        }
    }

    quantityInput.addEventListener('input', validateStock);
    typeSelect.addEventListener('change', validateStock);
    supplySelect.addEventListener('change', validateStock);

    // Validar al cargar la página
    validateStock();
});
</script>
@endsection
