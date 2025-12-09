@extends('worker.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Solicitar Préstamo de Herramienta</h2>
    <a href="{{ route('worker.loans.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-emerald-300 rounded text-emerald-700 hover:bg-emerald-100">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Volver</span>
    </a>
</div>
@endsection

@section('content')
<div class="bg-white border rounded p-6">
    <form method="POST" action="{{ route('worker.loans.store') }}" class="space-y-6">
        @csrf
        
        <!-- Herramienta -->
        <div>
            <label for="tool_id" class="block text-sm font-medium text-gray-700 mb-2">
                Herramienta <span class="text-red-500">*</span>
            </label>
            <select name="tool_id" id="tool_id" required 
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('tool_id') border-red-500 @enderror"
                    onchange="updateToolInfo()">
                <option value="">Selecciona una herramienta</option>
                @foreach($tools as $tool)
                    <option value="{{ $tool->id }}" 
                            data-available="{{ $tool->available_qty }}"
                            data-category="{{ $tool->category }}"
                            data-photo="{{ $tool->photo ? asset('storage/' . $tool->photo) : '' }}"
                            {{ old('tool_id') == $tool->id ? 'selected' : '' }}>
                        {{ $tool->name }} ({{ $tool->category }}) - Disponible: {{ $tool->available_qty }}
                    </option>
                @endforeach
            </select>
            @error('tool_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Información de la herramienta seleccionada -->
        <div id="tool-info" class="hidden p-4 bg-gray-50 rounded border">
            <h3 class="font-medium text-gray-900 mb-2">Información de la herramienta</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-600">Categoría:</span>
                    <span id="tool-category" class="font-medium text-gray-900"></span>
                </div>
                <div>
                    <span class="text-gray-600">Disponible:</span>
                    <span id="tool-available" class="font-medium text-gray-900"></span>
                </div>
            </div>
            <div id="tool-photo-wrapper" class="mt-3 hidden">
                <p class="text-xs text-gray-600 mb-1">Foto de referencia:</p>
                <img id="tool-photo" src="" alt="Foto de herramienta" class="h-24 w-24 object-cover rounded border border-gray-200">
            </div>
        </div>

        <!-- Cantidad -->
        <div>
            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                Cantidad <span class="text-red-500">*</span>
            </label>
            <input type="number" name="quantity" id="quantity" min="1" max="1" required
                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('quantity') border-red-500 @enderror"
                   value="{{ old('quantity', 1) }}"
                   onchange="validateQuantity()">
            @error('quantity')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-sm text-gray-500" id="quantity-help">Máximo disponible: <span id="max-quantity">1</span></p>
        </div>

        <!-- Fecha de vencimiento -->
        <div>
            <label for="due_at" class="block text-sm font-medium text-gray-700 mb-2">
                Fecha de vencimiento <span class="text-red-500">*</span>
            </label>
            <input type="date" name="due_at" id="due_at" required
                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('due_at') border-red-500 @enderror"
                   value="{{ old('due_at') }}"
                   min="{{ date('Y-m-d', strtotime('+1 day')) }}">
            @error('due_at')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-sm text-gray-500">La fecha debe ser posterior a hoy</p>
        </div>

        <!-- Notas de la solicitud -->
        <div>
            <label for="request_notes" class="block text-sm font-medium text-gray-700 mb-2">
                Notas de la solicitud
            </label>
            <textarea name="request_notes" id="request_notes" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('request_notes') border-red-500 @enderror"
                      placeholder="Describe el uso que le darás a la herramienta...">{{ old('request_notes') }}</textarea>
            @error('request_notes')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-sm text-gray-500">Opcional: Explica brevemente para qué necesitas la herramienta</p>
        </div>

        <!-- Botones -->
        <div class="flex justify-end gap-4 pt-6 border-t">
            <a href="{{ route('worker.loans.index') }}" 
               class="px-6 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors">
                <i data-lucide="send" class="w-4 h-4 inline mr-2"></i>
                Enviar Solicitud
            </button>
        </div>
    </form>
</div>

<script>
function updateToolInfo() {
    const select = document.getElementById('tool_id');
    const option = select.options[select.selectedIndex];
    const toolInfo = document.getElementById('tool-info');
    const quantityInput = document.getElementById('quantity');
    const maxQuantitySpan = document.getElementById('max-quantity');
    const photoWrapper = document.getElementById('tool-photo-wrapper');
    const photoImg = document.getElementById('tool-photo');
    
    if (option.value) {
        const available = parseInt(option.getAttribute('data-available'));
        const category = option.getAttribute('data-category');
        const photo = option.getAttribute('data-photo');
        
        document.getElementById('tool-category').textContent = category;
        document.getElementById('tool-available').textContent = available;
        maxQuantitySpan.textContent = available;
        
        quantityInput.max = available;
        quantityInput.value = Math.min(parseInt(quantityInput.value) || 1, available);
        
        toolInfo.classList.remove('hidden');
        if (photo) {
            photoImg.src = photo;
            photoWrapper.classList.remove('hidden');
        } else {
            photoImg.src = '';
            photoWrapper.classList.add('hidden');
        }
        validateQuantity();
    } else {
        toolInfo.classList.add('hidden');
        quantityInput.max = 1;
        maxQuantitySpan.textContent = '1';
        photoWrapper.classList.add('hidden');
        photoImg.src = '';
    }
}

function validateQuantity() {
    const quantityInput = document.getElementById('quantity');
    const maxQuantity = parseInt(quantityInput.max);
    const quantity = parseInt(quantityInput.value);
    
    if (quantity > maxQuantity) {
        quantityInput.setCustomValidity(`La cantidad no puede ser mayor a ${maxQuantity}`);
        quantityInput.reportValidity();
    } else {
        quantityInput.setCustomValidity('');
    }
}

// Establecer fecha mínima como mañana
document.getElementById('due_at').min = new Date().toISOString().split('T')[0];
</script>
@endsection
