@extends('foreman.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">
        {{ $type === 'entry' ? 'Entrada de Insumo' : 'Salida de Insumo' }}
    </h2>
    <a href="{{ route('foreman.supplies.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-emerald-700 hover:text-emerald-900">
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

    <form method="POST" action="{{ route('foreman.supply-movements.store') }}" class="space-y-6">
        @csrf
        
        <input type="hidden" name="type" value="{{ $type }}">
        
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
                                data-unit-cost="{{ $supply->unit_cost }}"
                                {{ old('supply_id') == $supply->id ? 'selected' : '' }}>
                            {{ $supply->name }} - Stock: {{ number_format($supply->current_stock, 3) }} {{ $supply->unit }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Fecha del Movimiento -->
            <div>
                <label for="movement_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha del Movimiento <span class="text-red-500">*</span>
                </label>
                <input type="date" 
                       id="movement_date" 
                       name="movement_date" 
                       value="{{ old('movement_date', date('Y-m-d')) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Cantidad -->
            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                    Cantidad <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       id="quantity" 
                       name="quantity" 
                       value="{{ old('quantity') }}"
                       step="0.001"
                       min="0.001"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="0.000"
                       required>
            </div>

            <!-- Costo por Unidad -->
            <div>
                <label for="unit_cost" class="block text-sm font-medium text-gray-700 mb-2">
                    Costo por Unidad <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       id="unit_cost" 
                       name="unit_cost" 
                       value="{{ old('unit_cost') }}"
                       step="0.01"
                       min="0"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="0.00"
                       required>
            </div>
        </div>

        <!-- Motivo -->
        <div>
            <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                Motivo del Movimiento
            </label>
            <input type="text" 
                   id="reason" 
                   name="reason" 
                   value="{{ old('reason') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                   placeholder="{{ $type === 'entry' ? 'Compra, donación, etc.' : 'Uso en cultivo, venta, etc.' }}">
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
                      placeholder="Información adicional sobre el movimiento...">{{ old('notes') }}</textarea>
        </div>


        <!-- Resumen del Costo Total -->
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <span class="text-lg font-medium text-gray-900">Costo Total:</span>
                <span id="total-cost" class="text-2xl font-bold text-emerald-600">$0.00</span>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex justify-end gap-4 pt-6 border-t">
            <a href="{{ route('foreman.supplies.index') }}" 
               class="px-6 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-6 py-2 {{ $type === 'entry' ? 'bg-green-100 hover:bg-green-200 text-green-700 border border-green-200' : 'bg-red-100 hover:bg-red-200 text-red-700 border border-red-200' }} rounded transition-colors">
                <i data-lucide="{{ $type === 'entry' ? 'plus-circle' : 'minus-circle' }}" class="w-4 h-4 inline mr-2"></i>
                {{ $type === 'entry' ? 'Registrar Entrada' : 'Registrar Salida' }}
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

    function calculateTotalCost() {
        const quantity = parseFloat(quantityInput.value) || 0;
        const unitCost = parseFloat(unitCostInput.value) || 0;
        const totalCost = quantity * unitCost;
        totalCostElement.textContent = '$' + totalCost.toFixed(2);
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
            @if($type === 'exit')
            if (unitCost && unitCost !== '0' && unitCost !== '') {
                unitCostInput.value = parseFloat(unitCost).toFixed(2);
                calculateTotalCost();
            } else {
                unitCostInput.value = '';
            }
            @else
            // Para entradas, limpiar el campo para que el usuario lo ingrese
            unitCostInput.value = '';
            @endif
        } else {
            unitCostInput.value = '';
        }
    });

    // Validación para salidas: verificar que no exceda el stock disponible
    @if($type === 'exit')
    quantityInput.addEventListener('input', function() {
        const selectedSupplyId = supplySelect.value;
        if (selectedSupplyId) {
            const selectedOption = supplySelect.options[supplySelect.selectedIndex];
            const stockText = selectedOption.text.split('Stock: ')[1];
            if (stockText) {
                const availableStock = parseFloat(stockText.split(' ')[0]);
                const requestedQuantity = parseFloat(this.value) || 0;
                
                if (requestedQuantity > availableStock) {
                    this.setCustomValidity(`No hay suficiente stock. Disponible: ${availableStock}`);
                    this.style.borderColor = '#ef4444';
                } else {
                    this.setCustomValidity('');
                    this.style.borderColor = '#d1d5db';
                }
            }
        }
    });
    @endif
});
</script>
@endsection