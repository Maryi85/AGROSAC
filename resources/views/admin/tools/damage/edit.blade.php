@extends('admin.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Editar Daño/Pérdida</h2>
    <a href="{{ route('admin.tool-damage.show', $tool->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-200 rounded transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Volver</span>
    </a>
</div>
@endsection

@section('content')
<form method="POST" action="{{ route('admin.tool-damage.update', $entry->id) }}" class="space-y-6" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Información del Daño/Pérdida</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Herramienta -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Herramienta
                </label>
                <div class="w-full border border-gray-200 rounded-md px-3 py-2 bg-gray-50 text-gray-700">
                    {{ $tool->name }} - {{ ucfirst(str_replace('_', ' ', $tool->category)) }}
                </div>
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
                        <option value="{{ $key }}" {{ old('damage_type', $entry->type) == $key ? 'selected' : '' }}>
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
                       value="{{ old('quantity', $entry->damaged_qty > 0 ? $entry->damaged_qty : $entry->lost_qty) }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('quantity') border-red-500 @enderror">
                @error('quantity')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Disponible: {{ $tool->available_qty }}</p>
            </div>

            <!-- Fecha -->
            <div>
                <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha <span class="text-red-500">*</span>
                </label>
                <input type="date" name="date" id="date" required 
                       value="{{ old('date', optional($entry->entry_date)->format('Y-m-d')) }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('date') border-red-500 @enderror">
                @error('date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notas -->
            <div class="md:col-span-2">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Notas
                </label>
                <textarea name="notes" id="notes" rows="3" maxlength="1000"
                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('notes') border-red-500 @enderror"
                          placeholder="Describe el daño o las circunstancias de la pérdida...">{{ old('notes', $entry->damage_notes ?? $entry->loss_notes) }}</textarea>
                @error('notes')
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
                <p class="mt-1 text-xs text-gray-500">Puedes adjuntar o reemplazar la imagen de evidencia.</p>
                @error('photo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                @if($entry->damage_photo)
                <div class="mt-3">
                    <p class="text-xs text-gray-500 mb-1">Foto actual:</p>
                    <a href="{{ asset('storage/' . $entry->damage_photo) }}" target="_blank" class="inline-flex items-center gap-2 text-emerald-700 hover:text-emerald-900 mb-2">
                        <i data-lucide="image" class="w-4 h-4"></i>
                        <span>Ver en pestaña</span>
                    </a>
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $entry->damage_photo) }}" alt="Foto actual del daño/pérdida" class="max-h-48 rounded border border-gray-200">
                    </div>
                </div>
                @endif

                <div id="photo-preview-wrapper" class="mt-3 hidden">
                    <p class="text-xs text-gray-500 mb-1">Vista previa nueva:</p>
                    <img id="photo-preview" src="" alt="Vista previa de la imagen seleccionada" class="max-h-48 rounded border border-gray-200">
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="flex justify-end gap-4 pt-6 border-t">
        <a href="{{ route('admin.tool-damage.show', $tool->id) }}" 
           class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-200 rounded transition-colors">
            Cancelar
        </a>
        <button type="submit" 
                class="px-6 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded transition-colors">
            <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
            Guardar cambios
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
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

