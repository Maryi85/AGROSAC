@extends('admin.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Registrar Consumo de Insumo</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-4">
    <form method="POST" action="{{ route('admin.supply-consumptions.store') }}" class="space-y-4">
        @csrf
        
        <!-- Insumo -->
        <div>
            <label for="supply_id" class="block text-sm mb-1 text-emerald-800">Insumo</label>
            <select id="supply_id" name="supply_id" 
                    class="w-full border border-emerald-200 rounded px-3 py-2 @error('supply_id') border-red-500 @enderror" 
                    required>
                <option value="">Seleccionar insumo</option>
                @foreach($supplies as $supply)
                    <option value="{{ $supply->id }}" {{ old('supply_id') == $supply->id ? 'selected' : '' }}
                            data-unit="{{ $supply->unit }}" data-unit-cost="{{ $supply->unit_cost }}">
                        {{ $supply->name }} ({{ $supply->unit }}) - ${{ number_format($supply->unit_cost, 2) }}
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
            <input type="number" step="0.001" min="0.001" id="qty" name="qty" value="{{ old('qty') }}" 
                   class="w-full border border-emerald-200 rounded px-3 py-2 @error('qty') border-red-500 @enderror" 
                   required />
            <p class="text-xs text-gray-500 mt-1" id="unit-info">Seleccione un insumo para ver la unidad</p>
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
                    <p id="unit-cost-display" class="text-sm text-gray-900 mt-1">$0.00</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Costo Total</label>
                    <p id="total-cost-display" class="text-sm text-gray-900 mt-1 font-semibold">$0.00</p>
                </div>
            </div>
        </div>
        
        <!-- Cultivo -->
        <div>
            <label for="crop_id" class="block text-sm mb-1 text-emerald-800">Cultivo (Opcional)</label>
            <select id="crop_id" name="crop_id" 
                    class="w-full border border-emerald-200 rounded px-3 py-2 @error('crop_id') border-red-500 @enderror">
                <option value="">Seleccionar cultivo</option>
                @foreach($crops as $crop)
                    <option value="{{ $crop->id }}" {{ old('crop_id') == $crop->id ? 'selected' : '' }}>
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
            <label for="plot_id" class="block text-sm mb-1 text-emerald-800">Lote (Opcional)</label>
            <select id="plot_id" name="plot_id" 
                    class="w-full border border-emerald-200 rounded px-3 py-2 @error('plot_id') border-red-500 @enderror">
                <option value="">Seleccionar lote</option>
                @foreach($plots as $plot)
                    <option value="{{ $plot->id }}" {{ old('plot_id') == $plot->id ? 'selected' : '' }}>
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
            <label for="task_id" class="block text-sm mb-1 text-emerald-800">Tarea (Opcional)</label>
            <select id="task_id" name="task_id" 
                    class="w-full border border-emerald-200 rounded px-3 py-2 @error('task_id') border-red-500 @enderror">
                <option value="">Seleccionar tarea</option>
                @foreach($tasks as $task)
                    <option value="{{ $task->id }}" {{ old('task_id') == $task->id ? 'selected' : '' }}>
                        {{ $task->type }} - {{ $task->scheduled_for->format('d/m/Y') }}
                    </option>
                @endforeach
            </select>
            @error('task_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Fecha de uso -->
        <div>
            <label for="used_at" class="block text-sm mb-1 text-emerald-800">Fecha de Uso</label>
            <input type="date" id="used_at" name="used_at" value="{{ old('used_at', date('Y-m-d')) }}" 
                   class="w-full border border-emerald-200 rounded px-3 py-2 @error('used_at') border-red-500 @enderror" 
                   required />
            @error('used_at')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Información adicional -->
        <div class="bg-green-50 border border-green-200 rounded p-4">
            <h4 class="text-sm font-semibold text-green-800 mb-2">Información Importante</h4>
            <ul class="text-xs text-green-700 space-y-1">
                <li>• El consumo se registrará inmediatamente en el sistema</li>
                <li>• El costo total se calculará automáticamente basado en la cantidad y el costo por unidad</li>
                <li>• Puede asociar el consumo a un cultivo, lote o tarea específica</li>
                <li>• Esta información será utilizada para reportes y análisis de costos</li>
            </ul>
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
                <span>Registrar Consumo</span>
            </button>
        </div>
    </form>
</div>

<script>
// Actualizar información de costo en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    const supplySelect = document.getElementById('supply_id');
    const qtyInput = document.getElementById('qty');
    const unitInfo = document.getElementById('unit-info');
    const unitCostDisplay = document.getElementById('unit-cost-display');
    const totalCostDisplay = document.getElementById('total-cost-display');
    
    function updateCostInfo() {
        const selectedOption = supplySelect.options[supplySelect.selectedIndex];
        const qty = parseFloat(qtyInput.value) || 0;
        
        if (selectedOption.value) {
            const unit = selectedOption.getAttribute('data-unit');
            const unitCost = parseFloat(selectedOption.getAttribute('data-unit-cost'));
            
            unitInfo.textContent = `Unidad: ${unit}`;
            unitCostDisplay.textContent = `$${unitCost.toFixed(2)}`;
            
            const totalCost = qty * unitCost;
            totalCostDisplay.textContent = `$${totalCost.toFixed(2)}`;
        } else {
            unitInfo.textContent = 'Seleccione un insumo para ver la unidad';
            unitCostDisplay.textContent = '$0.00';
            totalCostDisplay.textContent = '$0.00';
        }
    }
    
    supplySelect.addEventListener('change', updateCostInfo);
    qtyInput.addEventListener('input', updateCostInfo);
    
    // Actualizar al cargar la página si hay valores predefinidos
    updateCostInfo();
});
</script>
@endsection
