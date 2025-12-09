@extends('foreman.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Editar Consumo de Insumo</h2>
    <a href="{{ route('foreman.supply-consumptions.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-emerald-300 rounded text-emerald-700 hover:bg-emerald-100">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Volver</span>
    </a>
</div>
@endsection

@section('content')
<form method="POST" action="{{ route('foreman.supply-consumptions.update', $supplyConsumption) }}" class="space-y-6">
    @csrf
    @method('PUT')
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Información del Consumo</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Insumo -->
            <div class="md:col-span-2">
                <label for="supply_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Insumo <span class="text-red-500">*</span>
                </label>
                <select name="supply_id" id="supply_id" required 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('supply_id') border-red-500 @enderror">
                    <option value="">Seleccionar insumo</option>
                    @foreach($supplies as $supply)
                        <option value="{{ $supply->id }}" 
                                {{ old('supply_id', $supplyConsumption->supply_id) == $supply->id ? 'selected' : '' }}
                                data-unit="{{ $supply->unit }}" 
                                data-unit-cost="{{ $supply->unit_cost }}">
                            {{ $supply->name }} ({{ $supply->unit }}) - ${{ number_format($supply->unit_cost, 2) }}
                        </option>
                    @endforeach
                </select>
                @error('supply_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Cultivo -->
            <div>
                <label for="crop_id" class="block text-sm font-medium text-gray-700 mb-2">Cultivo</label>
                <select name="crop_id" id="crop_id" 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Sin cultivo</option>
                    @foreach($crops as $crop)
                        <option value="{{ $crop->id }}" {{ old('crop_id', $supplyConsumption->crop_id) == $crop->id ? 'selected' : '' }}>
                            {{ $crop->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Lote -->
            <div>
                <label for="plot_id" class="block text-sm font-medium text-gray-700 mb-2">Lote</label>
                <select name="plot_id" id="plot_id" 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Sin lote</option>
                    @foreach($plots as $plot)
                        <option value="{{ $plot->id }}" {{ old('plot_id', $supplyConsumption->plot_id) == $plot->id ? 'selected' : '' }}>
                            {{ $plot->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tarea -->
            <div>
                <label for="task_id" class="block text-sm font-medium text-gray-700 mb-2">Tarea</label>
                <select name="task_id" id="task_id" 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Sin tarea</option>
                    @foreach($tasks as $task)
                        <option value="{{ $task->id }}" {{ old('task_id', $supplyConsumption->task_id) == $task->id ? 'selected' : '' }}>
                            {{ $task->description }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Cantidad -->
            <div>
                <label for="qty" class="block text-sm font-medium text-gray-700 mb-2">
                    Cantidad <span class="text-red-500">*</span>
                </label>
                <input type="number" name="qty" id="qty" step="0.001" min="0.001" required 
                       value="{{ old('qty', $supplyConsumption->qty) }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('qty') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-500" id="unit-info">Seleccione un insumo para ver la unidad</p>
                @error('qty')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Fecha de uso -->
            <div>
                <label for="used_at" class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha de Uso <span class="text-red-500">*</span>
                </label>
                <input type="date" name="used_at" id="used_at" required 
                       value="{{ old('used_at', $supplyConsumption->used_at->format('Y-m-d')) }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('used_at') border-red-500 @enderror">
                @error('used_at')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Costo Total (calculado) -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Costo Total</label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500 font-semibold">$</span>
                    <input type="text" id="total_cost" readonly
                           value="$0.00"
                           class="w-full border border-gray-300 rounded-md pl-8 pr-3 py-2 bg-gray-50 font-semibold text-emerald-700">
                </div>
                <p class="mt-1 text-xs text-gray-500">Calculado automáticamente: Cantidad × Costo Unitario</p>
            </div>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="flex justify-end gap-4 pt-6 border-t">
        <a href="{{ route('foreman.supply-consumptions.index') }}" 
           class="px-6 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition-colors">
            Cancelar
        </a>
        <button type="submit" 
                class="px-6 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded transition-colors">
            <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
            Actualizar Consumo
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const supplySelect = document.getElementById('supply_id');
    const qtyInput = document.getElementById('qty');
    const totalCostInput = document.getElementById('total_cost');
    const unitInfo = document.getElementById('unit-info');
    
    function updateTotal() {
        const selectedOption = supplySelect.options[supplySelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const unitCost = parseFloat(selectedOption.getAttribute('data-unit-cost')) || 0;
            const unit = selectedOption.getAttribute('data-unit') || '';
            const qty = parseFloat(qtyInput.value) || 0;
            const total = unitCost * qty;
            
            unitInfo.textContent = `Unidad: ${unit}`;
            
            const formattedTotal = new Intl.NumberFormat('es-CO', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(total);
            
            totalCostInput.value = '$' + formattedTotal;
        } else {
            unitInfo.textContent = 'Seleccione un insumo para ver la unidad';
            totalCostInput.value = '$0.00';
        }
    }
    
    supplySelect.addEventListener('change', updateTotal);
    qtyInput.addEventListener('input', updateTotal);
    updateTotal();
});
</script>
@endsection





