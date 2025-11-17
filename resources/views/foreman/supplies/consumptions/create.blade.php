@extends('foreman.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Registrar Nuevo Consumo de Insumo</h2>
    <a href="{{ route('foreman.supply-consumptions.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-200 rounded transition-colors">
        <i data-lucide="arrow-left" class="w-5 h-5"></i>
        <span>Volver</span>
    </a>
</div>
@endsection

@section('content')
<div class="bg-white border rounded p-6">
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500">
            <div class="text-red-800 font-medium mb-2">Se encontraron los siguientes errores:</div>
            <ul class="text-red-700 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('foreman.supply-consumptions.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="supply_id" class="block text-sm font-medium text-gray-700 mb-1">Insumo</label>
                <select name="supply_id" id="supply_id" class="w-full border border-emerald-200 rounded px-3 py-2" required>
                    <option value="">Seleccione un insumo</option>
                    @foreach($supplies as $supply)
                        <option value="{{ $supply->id }}" 
                                data-unit="{{ $supply->unit }}" 
                                data-unit-cost="{{ $supply->unit_cost }}"
                                {{ old('supply_id') == $supply->id ? 'selected' : '' }}>
                            {{ $supply->name }} ({{ $supply->unit }}) - ${{ number_format($supply->unit_cost, 2) }}
                        </option>
                    @endforeach
                </select>
                @error('supply_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="qty" class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                <input type="number" step="0.001" class="w-full border border-emerald-200 rounded px-3 py-2" 
                       id="qty" name="qty" value="{{ old('qty') }}" required>
                <p class="text-xs text-gray-500 mt-1" id="unit-info">Seleccione un insumo para ver la unidad</p>
                @error('qty')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>


            <div>
                <label for="used_at" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Uso</label>
                <input type="date" class="w-full border border-emerald-200 rounded px-3 py-2" 
                       id="used_at" name="used_at" value="{{ old('used_at', date('Y-m-d')) }}" required>
                @error('used_at')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
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
                                    {{ old('crop_id') == $crop->id ? 'selected' : '' }}>
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
                    <label for="task_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Tarea
                    </label>
                    <select id="task_id" 
                            name="task_id" 
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Seleccionar tarea</option>
                        @foreach($tasks as $task)
                            <option value="{{ $task->id }}" {{ old('task_id') == $task->id ? 'selected' : '' }}>
                                {{ $task->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('task_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
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

        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded-lg font-medium transition-colors">
                <i data-lucide="save" class="w-5 h-5"></i>
                <span>Guardar</span>
            </button>
            <a href="{{ route('foreman.supply-consumptions.index') }}" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-200 rounded-lg transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
                <span>Cancelar</span>
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar información de costo en tiempo real
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
            
            if (unit) {
                unitInfo.textContent = `Unidad: ${unit}`;
            }
            
            // Mostrar el precio unitario registrado en la entrada de insumos
            unitCostDisplay.textContent = `$${unitCost.toFixed(2)}`;
            
            // Calcular y mostrar el precio total cuando se ingresa la cantidad
            const totalCost = qty * unitCost;
            totalCostDisplay.textContent = `$${totalCost.toFixed(2)}`;
        } else {
            unitInfo.textContent = 'Seleccione un insumo para ver la unidad';
            unitCostDisplay.textContent = '$0.00';
            totalCostDisplay.textContent = '$0.00';
        }
    }
    
    // Actualizar cuando se seleccione un insumo
    supplySelect.addEventListener('change', function() {
        updateCostInfo();
    });
    
    // Actualizar cuando se cambie la cantidad
    qtyInput.addEventListener('input', function() {
        updateCostInfo();
    });
    
    // Actualizar al cargar la página si hay valores predefinidos
    if (supplySelect.value) {
        updateCostInfo();
    }
    
    // Función para actualizar el lote cuando se selecciona un cultivo
    const cropSelect = document.getElementById('crop_id');
    const plotSelect = document.getElementById('plot_id');
    
    if (cropSelect && plotSelect) {
        cropSelect.addEventListener('change', function() {
            const selectedCrop = this.options[this.selectedIndex];
            const plotId = selectedCrop.getAttribute('data-plot-id');
            
            // Si no hay cultivo seleccionado, limpiar el lote
            if (!this.value) {
                plotSelect.value = '';
                return;
            }
            
            // Si hay un plot_id asociado al cultivo, seleccionarlo automáticamente
            if (plotId) {
                // Buscar la opción del lote con ese ID
                const plotOption = Array.from(plotSelect.options).find(opt => opt.value == plotId);
                if (plotOption) {
                    plotSelect.value = plotId;
                }
            } else {
                // Si el cultivo no tiene lote asociado, limpiar la selección
                plotSelect.value = '';
            }
        });
        
        // Si hay un valor antiguo, disparar el evento
        if (cropSelect.value) {
            cropSelect.dispatchEvent(new Event('change'));
        }
    }
});
</script>
@endsection