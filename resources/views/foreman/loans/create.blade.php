@extends('foreman.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Prestar Herramienta</h2>
    <a href="{{ route('foreman.loans.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
        Volver
    </a>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white border rounded p-6">
        <form method="POST" action="{{ route('foreman.loans.store') }}" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Herramienta -->
                <div>
                    <label for="tool_id" class="block text-sm font-medium text-emerald-700 mb-2">Herramienta *</label>
                    <select name="tool_id" id="tool_id" required 
                            class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('tool_id') border-red-500 @enderror">
                        <option value="">Seleccionar herramienta</option>
                        @foreach($tools as $tool)
                            <option value="{{ $tool->id }}" {{ old('tool_id') == $tool->id ? 'selected' : '' }}>
                                {{ $tool->name }} (Disponibles: {{ $tool->available_qty }})
                            </option>
                        @endforeach
                    </select>
                    @error('tool_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Trabajador -->
                <div>
                    <label for="user_id" class="block text-sm font-medium text-emerald-700 mb-2">Trabajador *</label>
                    <select name="user_id" id="user_id" required 
                            class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('user_id') border-red-500 @enderror">
                        <option value="">Seleccionar trabajador</option>
                        @foreach($workers as $worker)
                            <option value="{{ $worker->id }}" {{ old('user_id') == $worker->id ? 'selected' : '' }}>
                                {{ $worker->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Cantidad -->
                <div>
                    <label for="quantity" class="block text-sm font-medium text-emerald-700 mb-2">Cantidad *</label>
                    <input type="number" name="quantity" id="quantity" min="1" required 
                           value="{{ old('quantity', 1) }}"
                           class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('quantity') border-red-500 @enderror">
                    @error('quantity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fecha de devolución -->
                <div>
                    <label for="due_at" class="block text-sm font-medium text-emerald-700 mb-2">Fecha de Devolución</label>
                    <input type="date" name="due_at" id="due_at" 
                           value="{{ old('due_at') }}"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('due_at') border-red-500 @enderror">
                    @error('due_at')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Opcional. Si no se especifica, no habrá fecha límite.</p>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="bg-emerald-50 border border-emerald-200 rounded p-4">
                <h3 class="text-sm font-semibold text-emerald-700 mb-2">Información Importante</h3>
                <ul class="text-sm text-emerald-600 space-y-1">
                    <li>• El sistema actualizará automáticamente la disponibilidad de la herramienta</li>
                    <li>• El trabajador recibirá una notificación del préstamo</li>
                    <li>• Puedes supervisar el estado del préstamo desde la lista principal</li>
                </ul>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('foreman.loans.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="bg-emerald-600 text-white px-6 py-2 rounded hover:bg-emerald-700 transition-colors">
                    <i data-lucide="check" class="w-4 h-4 inline mr-2"></i>
                    Prestar Herramienta
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toolSelect = document.getElementById('tool_id');
    const quantityInput = document.getElementById('quantity');
    
    // Actualizar cantidad máxima cuando se selecciona una herramienta
    toolSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            // Extraer la cantidad disponible del texto de la opción
            const availableText = selectedOption.textContent.match(/Disponibles: (\d+)/);
            if (availableText) {
                const availableQty = parseInt(availableText[1]);
                quantityInput.max = availableQty;
                
                // Si la cantidad actual es mayor que la disponible, ajustarla
                if (parseInt(quantityInput.value) > availableQty) {
                    quantityInput.value = availableQty;
                }
            }
        }
    });
    
    // Validar cantidad en tiempo real
    quantityInput.addEventListener('input', function() {
        const maxQty = parseInt(this.max);
        const currentQty = parseInt(this.value);
        
        if (currentQty > maxQty) {
            this.value = maxQty;
        }
        
        if (currentQty < 1) {
            this.value = 1;
        }
    });
});
</script>
@endsection
