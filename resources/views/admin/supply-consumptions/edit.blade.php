@extends('admin.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Editar Consumo de Insumo</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-4">
    <form method="POST" action="{{ route('admin.supply-consumptions.update', $supplyConsumption) }}" class="space-y-4">
        @csrf
        @method('PUT')
        
        <!-- Insumo -->
        <div>
            <label for="supply_id" class="block text-sm mb-1 text-emerald-800">Insumo</label>
            <select id="supply_id" name="supply_id" 
                    class="w-full border border-emerald-200 rounded px-3 py-2 @error('supply_id') border-red-500 @enderror" 
                    required>
                <option value="">Seleccionar insumo</option>
                @foreach($supplies as $supply)
                    <option value="{{ $supply->id }}" {{ old('supply_id', $supplyConsumption->supply_id) == $supply->id ? 'selected' : '' }}
                            data-unit="{{ $supply->unit }}" data-unit-cost="{{ (int)$supply->unit_cost }}">
                        {{ $supply->name }} ({{ $supply->unit }}) - ${{ number_format((int)$supply->unit_cost, 0) }}
                    </option>
                @endforeach
            </select>
            @error('supply_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Cantidad -->
        <div>
            <label for="qty" class="block text-sm mb-1 text-emerald-800">Cantidad</label>
            <input type="number" step="0.001" min="0.001" id="qty" name="qty" value="{{ old('qty', (float)$supplyConsumption->qty) }}" 
                   class="w-full border border-emerald-200 rounded px-3 py-2 @error('qty') border-red-500 @enderror" 
                   required />
            <p class="text-xs text-gray-500 mt-1" id="unit-info">Unidad: {{ $supplyConsumption->supply->unit }}</p>
            @error('qty')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Información de costo -->
        <div class="bg-blue-50 border border-blue-200 rounded p-4">
            <h4 class="text-sm font-semibold text-blue-800 mb-2">Información de Costo</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Costo por Unidad</label>
                    <p id="unit-cost-display" class="text-sm text-gray-900 mt-1">${{ number_format($supplyConsumption->supply->unit_cost, 0) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Costo Total</label>
                    <p id="total-cost-display" class="text-sm text-gray-900 mt-1 font-semibold">${{ number_format((int)$supplyConsumption->total_cost, 0) }}</p>
                </div>
            </div>
        </div>
        
        <!-- Información Relacionada (Opcional) -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Información Relacionada (Opcional)</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Cultivo -->
                <div>
                    <label for="crop_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Cultivo
                    </label>
                    <select id="crop_id" 
                            name="crop_id" 
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Seleccionar cultivo</option>
                        @foreach($crops as $crop)
                            <option value="{{ $crop->id }}" 
                                    data-plot-id="{{ $crop->plot_id }}"
                                    {{ old('crop_id', $supplyConsumption->crop_id) == $crop->id ? 'selected' : '' }}>
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
                    <label for="plot_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Lote
                    </label>
                    <select id="plot_id" 
                            name="plot_id" 
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Seleccionar lote</option>
                        @foreach($plots as $plot)
                            <option value="{{ $plot->id }}" {{ old('plot_id', $supplyConsumption->plot_id) == $plot->id ? 'selected' : '' }}>
                                {{ $plot->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('plot_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tarea -->
                <div>
                    <label for="task_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Tarea
                    </label>
                    <select id="task_id" 
                            name="task_id" 
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Seleccionar tarea</option>
                        @foreach($tasks as $task)
                            <option value="{{ $task->id }}" {{ old('task_id', $supplyConsumption->task_id) == $task->id ? 'selected' : '' }}>
                                {{ $task->type }} - {{ $task->scheduled_for->format('d/m/Y') }}
                            </option>
                        @endforeach
                    </select>
                    @error('task_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Fecha de uso -->
        <div>
            <label for="used_at" class="block text-sm mb-1 text-emerald-800">Fecha de Uso</label>
            <input type="date" id="used_at" name="used_at" value="{{ old('used_at', $supplyConsumption->used_at->format('Y-m-d')) }}" 
                   class="w-full border border-emerald-200 rounded px-3 py-2 @error('used_at') border-red-500 @enderror" 
                   required />
            @error('used_at')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Botones -->
        <div class="flex items-center gap-2 pt-4">
            <a href="{{ route('admin.supply-consumptions.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 inline-flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Volver</span>
            </a>
            <button type="submit" 
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded inline-flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Actualizar Consumo</span>
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const supplySelect = document.getElementById('supply_id');
    const qtyInput = document.getElementById('qty');
    const unitInfo = document.getElementById('unit-info');
    const unitCostDisplay = document.getElementById('unit-cost-display');
    const totalCostDisplay = document.getElementById('total-cost-display');
    
    function updateCostInfo() {
        const selectedOption = supplySelect.options[supplySelect.selectedIndex];
        const qty = parseFloat(qtyInput.value) || 0;
        
        if (selectedOption && selectedOption.value) {
            const unit = selectedOption.getAttribute('data-unit') || '';
            const unitCostStr = selectedOption.getAttribute('data-unit-cost');
            const unitCost = unitCostStr ? parseFloat(unitCostStr) : 0;
            
            unitInfo.textContent = `Unidad: ${unit}`;
            unitCostDisplay.textContent = `$${unitCost.toFixed(0)}`;
            const totalCost = qty * unitCost;
            totalCostDisplay.textContent = `$${totalCost.toFixed(0)}`;
        }
    }
    
    supplySelect.addEventListener('change', updateCostInfo);
    qtyInput.addEventListener('input', updateCostInfo);
    
    // Crop/Plot association
    const cropSelect = document.getElementById('crop_id');
    const plotSelect = document.getElementById('plot_id');
    
    if (cropSelect && plotSelect) {
        cropSelect.addEventListener('change', function() {
            const selectedCrop = this.options[this.selectedIndex];
            const plotId = selectedCrop.getAttribute('data-plot-id');
            if (plotId) {
                plotSelect.value = plotId;
            }
        });
    }
});
</script>
@endsection
