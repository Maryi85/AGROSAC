@php
    $layout = route_prefix() === 'foreman.' ? 'foreman.layout' : 'admin.layout';
@endphp
@extends($layout)

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Prestar Herramienta</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-4">
    <form method="POST" action="{{ route(route_prefix() . 'loans.store') }}" class="space-y-4">
        @csrf
        
        <!-- Herramienta -->
        <div>
            <label for="tool_id" class="block text-sm mb-1 text-emerald-800">Herramienta</label>
            <select id="tool_id" name="tool_id" 
                    class="w-full border border-emerald-200 rounded px-3 py-2 @error('tool_id') border-red-500 @enderror" 
                    required>
                <option value="">Seleccionar herramienta</option>
                @foreach($tools as $tool)
                    <option value="{{ $tool->id }}" {{ old('tool_id') == $tool->id ? 'selected' : '' }}
                            data-available="{{ $tool->available_qty }}">
                        {{ $tool->name }} ({{ $tool->available_qty }} disponibles)
                    </option>
                @endforeach
            </select>
            @error('tool_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Trabajador -->
        <div>
            <label for="user_id" class="block text-sm mb-1 text-emerald-800">Trabajador</label>
            <select id="user_id" name="user_id" 
                    class="w-full border border-emerald-200 rounded px-3 py-2 @error('user_id') border-red-500 @enderror" 
                    required>
                <option value="">Seleccionar trabajador</option>
                @foreach($workers as $worker)
                    <option value="{{ $worker->id }}" {{ old('user_id') == $worker->id ? 'selected' : '' }}>
                        {{ $worker->name }} ({{ $worker->email }})
                    </option>
                @endforeach
            </select>
            @error('user_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Cantidad -->
        <div>
            <label for="quantity" class="block text-sm mb-1 text-emerald-800">Cantidad</label>
            <input type="number" id="quantity" name="quantity" value="{{ old('quantity', 1) }}" 
                   min="1" max="1"
                   class="w-full border border-emerald-200 rounded px-3 py-2 @error('quantity') border-red-500 @enderror" 
                   required />
            <p class="text-xs text-gray-500 mt-1" id="available-info">Seleccione una herramienta para ver la cantidad disponible</p>
            @error('quantity')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Fecha de devolución -->
        <div>
            <label for="due_at" class="block text-sm mb-1 text-emerald-800">Fecha de Devolución (Opcional)</label>
            <input type="date" id="due_at" name="due_at" value="{{ old('due_at') }}" 
                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                   class="w-full border border-emerald-200 rounded px-3 py-2 @error('due_at') border-red-500 @enderror" />
            <p class="text-xs text-gray-500 mt-1">Si no se especifica, no habrá fecha límite de devolución</p>
            @error('due_at')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Información adicional -->
        <div class="bg-blue-50 border border-blue-200 rounded p-4">
            <h4 class="text-sm font-semibold text-blue-800 mb-2">Información Importante</h4>
            <ul class="text-xs text-blue-700 space-y-1">
                <li>• Al prestar una herramienta, se reducirá automáticamente la cantidad disponible en el inventario</li>
                <li>• La herramienta se marcará como "Prestado" hasta que sea devuelta</li>
                <li>• Puede marcar la herramienta como devuelta, perdida o dañada desde la lista de préstamos</li>
                <li>• Si marca como perdida, se reducirá la cantidad total del inventario</li>
                <li>• Si marca como dañada, se devolverá al inventario pero con estado "Dañado"</li>
            </ul>
        </div>
        
        <!-- Botones -->
        <div class="flex items-center gap-2 pt-4">
            <a href="{{ route(route_prefix() . 'loans.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 inline-flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Volver</span>
            </a>
            <button type="submit" 
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded inline-flex items-center gap-2">
                <i data-lucide="hand" class="w-4 h-4"></i>
                <span>Prestar Herramienta</span>
            </button>
        </div>
    </form>
</div>

<script>
// Actualizar cantidad máxima y información disponible
document.addEventListener('DOMContentLoaded', function() {
    const toolSelect = document.getElementById('tool_id');
    const quantityInput = document.getElementById('quantity');
    const availableInfo = document.getElementById('available-info');
    
    function updateQuantityInfo() {
        const selectedOption = toolSelect.options[toolSelect.selectedIndex];
        if (selectedOption.value) {
            const available = parseInt(selectedOption.getAttribute('data-available'));
            quantityInput.max = available;
            quantityInput.value = Math.min(quantityInput.value || 1, available);
            availableInfo.textContent = `Cantidad disponible: ${available} unidades`;
        } else {
            quantityInput.max = 1;
            availableInfo.textContent = 'Seleccione una herramienta para ver la cantidad disponible';
        }
    }
    
    toolSelect.addEventListener('change', updateQuantityInfo);
    
    // Validación en tiempo real para cantidad
    quantityInput.addEventListener('input', function() {
        const selectedOption = toolSelect.options[toolSelect.selectedIndex];
        if (selectedOption.value) {
            const available = parseInt(selectedOption.getAttribute('data-available'));
            const quantity = parseInt(this.value) || 0;
            
            if (quantity > available) {
                this.setCustomValidity(`No puede prestar más de ${available} unidades`);
                this.reportValidity();
            } else {
                this.setCustomValidity('');
            }
        }
    });
});
</script>
@endsection
