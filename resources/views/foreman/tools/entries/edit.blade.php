@extends('foreman.layout')

@section('title', 'Editar Entrada de Herramienta')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Editar Entrada de Herramienta</h2>
    <a href="{{ route('foreman.tool-entries.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-emerald-300 rounded text-emerald-700 hover:bg-emerald-100">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Volver</span>
    </a>
</div>
@endsection

@section('content')
<form method="POST" action="{{ route('foreman.tool-entries.update', $entry) }}" class="space-y-6">
    @csrf
    @method('PUT')
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Información de la Entrada</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Herramienta -->
            <div class="md:col-span-2">
                <label for="tool_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Herramienta <span class="text-red-500">*</span>
                </label>
                <select name="tool_id" id="tool_id" required 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('tool_id') border-red-500 @enderror">
                    <option value="">Seleccionar herramienta</option>
                    @foreach($tools as $tool)
                        <option value="{{ $tool->id }}" {{ old('tool_id', $entry->tool_id) == $tool->id ? 'selected' : '' }}>
                            {{ $tool->name }} - {{ $tool->category }}
                        </option>
                    @endforeach
                </select>
                @error('tool_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Cantidad -->
            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                    Cantidad <span class="text-red-500">*</span>
                </label>
                <input type="number" name="quantity" id="quantity" min="1" required 
                       value="{{ old('quantity', $entry->quantity) }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('quantity') border-red-500 @enderror">
                @error('quantity')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tipo de entrada -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                    Tipo de Entrada <span class="text-red-500">*</span>
                </label>
                <select name="type" id="type" required 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('type') border-red-500 @enderror">
                    <option value="">Seleccionar tipo</option>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}" {{ old('type', $entry->type) == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Fecha de entrada -->
            <div>
                <label for="entry_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha de Entrada <span class="text-red-500">*</span>
                </label>
                <input type="date" name="entry_date" id="entry_date" required 
                       value="{{ old('entry_date', $entry->entry_date->format('Y-m-d')) }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('entry_date') border-red-500 @enderror">
                @error('entry_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Costo unitario -->
            <div>
                <label for="unit_cost" class="block text-sm font-medium text-gray-700 mb-2">
                    Costo Unitario
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">$</span>
                    <input type="number" name="unit_cost" id="unit_cost" step="0.01" min="0" 
                           value="{{ old('unit_cost', $entry->unit_cost) }}"
                           class="w-full border border-gray-300 rounded-md pl-8 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('unit_cost') border-red-500 @enderror">
                </div>
                @error('unit_cost')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Precio Total -->
            <div>
                <label for="total_cost" class="block text-sm font-medium text-gray-700 mb-2">
                    Precio Total
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500 font-semibold">$</span>
                    <input type="text" id="total_cost" readonly
                           value="$0.00"
                           class="w-full border border-gray-300 rounded-md pl-8 pr-3 py-2 bg-gray-50 font-semibold text-emerald-700">
                </div>
                <p class="mt-1 text-xs text-gray-500">Calculado automáticamente: Cantidad × Costo Unitario</p>
            </div>

            <!-- Notas -->
            <div class="md:col-span-2">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Notas Adicionales
                </label>
                <textarea name="notes" id="notes" rows="3" maxlength="1000"
                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('notes') border-red-500 @enderror"
                          placeholder="Información adicional sobre la entrada...">{{ old('notes', $entry->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="flex justify-end gap-4 pt-6 border-t">
        <a href="{{ route('foreman.tool-entries.index') }}" 
           class="px-6 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition-colors">
            Cancelar
        </a>
        <button type="submit" 
                class="px-6 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded transition-colors">
            <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
            Actualizar Entrada
        </button>
    </div>
</form>

<script>
// Calcular total automáticamente
document.addEventListener('DOMContentLoaded', function() {
    const unitCostInput = document.getElementById('unit_cost');
    const quantityInput = document.getElementById('quantity');
    const totalCostInput = document.getElementById('total_cost');
    
    function calculateTotal() {
        const unitCost = parseFloat(unitCostInput.value) || 0;
        const quantity = parseInt(quantityInput.value) || 0;
        const total = unitCost * quantity;
        
        // Formatear el total con 2 decimales y separador de miles
        const formattedTotal = new Intl.NumberFormat('es-CO', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(total);
        
        // Actualizar el campo de precio total
        totalCostInput.value = '$' + formattedTotal;
    }
    
    // Calcular cuando cambian los valores
    unitCostInput.addEventListener('input', calculateTotal);
    quantityInput.addEventListener('input', calculateTotal);
    
    // Calcular al cargar la página si hay valores
    calculateTotal();
});
</script>
@endsection
