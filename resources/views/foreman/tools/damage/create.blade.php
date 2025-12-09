@extends('foreman.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Registrar Daño o Pérdida</h2>
    <a href="{{ route('foreman.tool-damage.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-200 rounded transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Volver</span>
    </a>
</div>
@endsection

@section('content')
<form method="POST" action="{{ route('foreman.tool-damage.store') }}" class="space-y-6" enctype="multipart/form-data">
    @csrf
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Información del Daño/Pérdida</h3>
        
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
                        <option value="{{ $tool->id }}" {{ (old('tool_id') == $tool->id || ($selectedTool && $selectedTool->id == $tool->id)) ? 'selected' : '' }}
                                data-available="{{ $tool->available_qty }}">
                            {{ $tool->name }} - {{ ucfirst(str_replace('_', ' ', $tool->category)) }} (Disponible: {{ $tool->available_qty }})
                        </option>
                    @endforeach
                </select>
                @error('tool_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tipo de problema -->
            <div>
                <label for="damage_type" class="block text-sm font-medium text-gray-700 mb-2">
                    Tipo de Problema <span class="text-red-500">*</span>
                </label>
                <select name="damage_type" id="damage_type" required 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('damage_type') border-red-500 @enderror">
                    <option value="">Seleccionar tipo</option>
                    @foreach($damageTypes as $key => $label)
                        <option value="{{ $key }}" {{ old('damage_type') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('damage_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Cantidad -->
            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                    Cantidad <span class="text-red-500">*</span>
                </label>
                <input type="number" name="quantity" id="quantity" min="1" required 
                       value="{{ old('quantity') }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('quantity') border-red-500 @enderror">
                @error('quantity')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500" id="available-info">Selecciona una herramienta para ver la cantidad disponible</p>
            </div>

            <!-- Fecha -->
            <div>
                <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha <span class="text-red-500">*</span>
                </label>
                <input type="date" name="date" id="date" required 
                       value="{{ old('date', date('Y-m-d')) }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('date') border-red-500 @enderror">
                @error('date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Foto del daño/pérdida -->
            <div class="md:col-span-2">
                <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
                    Foto (opcional)
                </label>
                <input type="file" name="photo" id="photo" accept="image/*"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('photo') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-500">Adjunta evidencia fotográfica si está disponible.</p>
                @error('photo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <div id="photo-preview-wrapper" class="mt-3 hidden">
                    <p class="text-xs text-gray-500 mb-1">Vista previa:</p>
                    <img id="photo-preview" src="" alt="Vista previa de la imagen seleccionada" class="max-h-48 rounded border border-gray-200">
                </div>
            </div>

            <!-- Notas -->
            <div class="md:col-span-2">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Notas
                </label>
                <textarea name="notes" id="notes" rows="3" maxlength="1000"
                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('notes') border-red-500 @enderror"
                          placeholder="Describe el daño o las circunstancias de la pérdida...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="flex justify-end gap-4 pt-6 border-t">
        <a href="{{ route('foreman.tool-damage.index') }}" 
           class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-200 rounded transition-colors">
            Cancelar
        </a>
        <button type="submit" 
                class="px-6 py-2 bg-orange-100 hover:bg-orange-200 text-orange-700 border border-orange-200 rounded transition-colors">
            <i data-lucide="alert-triangle" class="w-4 h-4 inline mr-2"></i>
            Registrar
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toolSelect = document.getElementById('tool_id');
    const quantityInput = document.getElementById('quantity');
    const availableInfo = document.getElementById('available-info');
    
    function updateAvailableInfo() {
        const selectedOption = toolSelect.options[toolSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const available = selectedOption.getAttribute('data-available');
            availableInfo.textContent = `Cantidad disponible: ${available}`;
            quantityInput.setAttribute('max', available);
        } else {
            availableInfo.textContent = 'Selecciona una herramienta para ver la cantidad disponible';
            quantityInput.removeAttribute('max');
        }
    }
    
    toolSelect.addEventListener('change', updateAvailableInfo);
    updateAvailableInfo(); // Inicializar

    // Vista previa de imagen
    const photoInput = document.getElementById('photo');
    const previewWrapper = document.getElementById('photo-preview-wrapper');
    const previewImg = document.getElementById('photo-preview');

    function updatePhotoPreview() {
        const file = photoInput.files && photoInput.files[0];
        if (!file) {
            previewWrapper.classList.add('hidden');
            previewImg.src = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = e => {
            previewImg.src = e.target.result;
            previewWrapper.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    photoInput.addEventListener('change', updatePhotoPreview);
});
</script>
@endsection
