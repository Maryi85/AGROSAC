@extends('foreman.layout')

@section('title', $type === 'entry' ? 'Entrada de Insumo' : 'Salida de Insumo')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">
        {{ $type === 'entry' ? 'Entrada de Insumo' : 'Salida de Insumo' }}
    </h2>
</div>
@endsection

@section('content')
<form method="POST" action="{{ route('foreman.supply-movements.store') }}" class="space-y-6">
    @csrf
    <input type="hidden" name="type" value="{{ $type }}">

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        {{-- Cabecera del card con botón Volver --}}
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Información de la Entrada</h3>
            <a href="{{ route('foreman.supplies.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 border border-emerald-300 rounded text-emerald-700 hover:bg-emerald-100 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Volver</span>
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Insumo --}}
            <div class="md:col-span-2">
                <label for="supply_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Insumo <span class="text-red-500">*</span>
                </label>
                <select id="supply_id"
                        name="supply_id"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('supply_id') border-red-500 @enderror"
                        required>
                    <option value="">Seleccionar insumo</option>
                    @foreach($supplies as $supply)
                        <option value="{{ $supply->id }}"
                                data-unit-cost="{{ (int)$supply->unit_cost }}"
                                {{ old('supply_id') == $supply->id ? 'selected' : '' }}>
                            {{ $supply->name }} - Stock: {{ rtrim(rtrim(number_format($supply->current_stock, 2, '.', ','), '0'), '.') }} {{ $supply->unit }}
                        </option>
                    @endforeach
                </select>
                @error('supply_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Cantidad --}}
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
                       placeholder="0.000"
                       required
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('quantity') border-red-500 @enderror">
                @error('quantity')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Fecha del Movimiento --}}
            <div>
                <label for="movement_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha del Movimiento <span class="text-red-500">*</span>
                </label>
                <input type="date"
                       id="movement_date"
                       name="movement_date"
                       value="{{ old('movement_date', date('Y-m-d')) }}"
                       required
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('movement_date') border-red-500 @enderror">
                @error('movement_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Costo por Unidad --}}
            <div>
                <label for="unit_cost" class="block text-sm font-medium text-gray-700 mb-2">
                    Costo por Unidad <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">$</span>
                    <input type="number"
                           id="unit_cost"
                           name="unit_cost"
                           value="{{ old('unit_cost') }}"
                           step="1"
                           min="0"
                           placeholder="0.00"
                           required
                           class="w-full border border-gray-300 rounded-md pl-8 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('unit_cost') border-red-500 @enderror">
                </div>
                @error('unit_cost')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Costo Total (calculado) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Costo Total</label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500 font-semibold">$</span>
                    <input type="text" id="total_cost" readonly
                           value="$0.00"
                           class="w-full border border-gray-300 rounded-md pl-8 pr-3 py-2 bg-gray-50 font-semibold text-emerald-700">
                </div>
                <p class="mt-1 text-xs text-gray-500">Calculado automáticamente: Cantidad × Costo por Unidad</p>
            </div>

            {{-- Motivo --}}
            <div>
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                    Motivo del Movimiento
                </label>
                <input type="text"
                       id="reason"
                       name="reason"
                       value="{{ old('reason') }}"
                       placeholder="{{ $type === 'entry' ? 'Compra, donación, etc.' : 'Uso en cultivo, venta, etc.' }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('reason') border-red-500 @enderror">
                @error('reason')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Notas --}}
            <div class="md:col-span-2">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Notas Adicionales
                </label>
                <textarea id="notes"
                          name="notes"
                          rows="3"
                          placeholder="Información adicional sobre el movimiento..."
                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Botones de acción --}}
    <div class="flex justify-end gap-4 pt-6 border-t">
        <a href="{{ route('foreman.supplies.index') }}"
           class="px-6 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition-colors">
            Cancelar
        </a>
        <button type="submit"
                class="px-6 py-2 {{ $type === 'entry' ? 'bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200' : 'bg-red-100 hover:bg-red-200 text-red-700 border border-red-200' }} rounded transition-colors">
            <i data-lucide="{{ $type === 'entry' ? 'plus-circle' : 'minus-circle' }}" class="w-4 h-4 inline mr-2"></i>
            {{ $type === 'entry' ? 'Registrar Entrada' : 'Registrar Salida' }}
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput  = document.getElementById('quantity');
    const unitCostInput  = document.getElementById('unit_cost');
    const totalCostInput = document.getElementById('total_cost');
    const supplySelect   = document.getElementById('supply_id');

    function calculateTotal() {
        const quantity = parseFloat(quantityInput.value) || 0;
        const unitCost = parseFloat(unitCostInput.value) || 0;
        const total    = quantity * unitCost;

        const formatted = new Intl.NumberFormat('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(total);

        totalCostInput.value = '$' + formatted;
    }

    quantityInput.addEventListener('input', calculateTotal);
    unitCostInput.addEventListener('input', calculateTotal);
    calculateTotal();

    // Auto-completar costo unitario al seleccionar un insumo
    supplySelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const unitCost = selected ? selected.getAttribute('data-unit-cost') : null;

        @if($type === 'exit')
        if (unitCost && unitCost !== '0' && unitCost !== '') {
            unitCostInput.value = parseFloat(unitCost).toFixed(0);
            calculateTotal();
        } else {
            unitCostInput.value = '';
        }
        @else
        unitCostInput.value = '';
        @endif
    });

    // Validación de stock para salidas
    @if($type === 'exit')
    quantityInput.addEventListener('input', function() {
        const selected = supplySelect.options[supplySelect.selectedIndex];
        if (selected && supplySelect.value) {
            const stockText = selected.text.split('Stock: ')[1];
            if (stockText) {
                const availableStock     = parseFloat(stockText.split(' ')[0]);
                const requestedQuantity  = parseFloat(this.value) || 0;
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

