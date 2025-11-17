@extends('foreman.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Registrar Nuevo Insumo</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-4">
    <form method="POST" action="{{ route('foreman.supplies.store') }}" class="space-y-4" enctype="multipart/form-data">
        @csrf
        
        <!-- Nombre -->
        <div>
            <label for="name" class="block text-sm mb-1 text-emerald-800">Nombre del Insumo</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" 
                   class="w-full border border-emerald-200 rounded px-3 py-2 @error('name') border-red-500 @enderror" 
                   required />
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Unidad de Medida -->
        <div>
            <label for="unit" class="block text-sm mb-1 text-emerald-800">Unidad de Medida</label>
            <select id="unit" name="unit" 
                    class="w-full border border-emerald-200 rounded px-3 py-2 @error('unit') border-red-500 @enderror" 
                    required>
                <option value="">Seleccionar unidad</option>
                @foreach($units as $value => $label)
                    <option value="{{ $value }}" {{ old('unit') === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('unit')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Stock Mínimo -->
        <div>
            <label for="min_stock" class="block text-sm mb-1 text-emerald-800">Stock Mínimo</label>
            <input type="number" step="0.01" min="0" name="min_stock" id="min_stock" 
                   class="w-full border border-emerald-200 rounded px-3 py-2 @error('min_stock') border-red-500 @enderror" 
                   value="{{ old('min_stock', 0) }}" required>
            @error('min_stock')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Foto -->
        <div>
            <label for="photo" class="block text-sm mb-1 text-emerald-800">Foto del Insumo</label>
            <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/jpg,image/gif" 
                   class="w-full border border-emerald-200 rounded px-3 py-2 @error('photo') border-red-500 @enderror">
            <p class="text-xs text-gray-500 mt-1">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB</p>
            @error('photo')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
            <div id="photo-preview" class="mt-3 hidden">
                <img id="photo-preview-img" src="" alt="Vista previa" class="max-w-xs rounded border border-emerald-200">
            </div>
        </div>
        
        <!-- Estado -->
        <div>
            <label for="status" class="block text-sm mb-1 text-emerald-800">Estado</label>
            <select id="status" name="status" 
                    class="w-full border border-emerald-200 rounded px-3 py-2 @error('status') border-red-500 @enderror" 
                    required>
                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Activo</option>
                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactivo</option>
            </select>
            @error('status')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Información adicional -->
        <div class="bg-blue-50 border border-blue-200 rounded p-4">
            <h4 class="text-sm font-semibold text-blue-800 mb-2">Información Importante</h4>
            <ul class="text-xs text-blue-700 space-y-1">
                <li>• El nombre del insumo debe ser único en el sistema</li>
                <li>• Los insumos activos estarán disponibles para registrar consumos</li>
                <li>• El stock mínimo ayudará a identificar cuándo se necesita reabastecimiento</li>
            </ul>
        </div>
        
        <!-- Botones -->
        <div class="flex items-center gap-2 pt-4">
            <a href="{{ route('foreman.supplies.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 inline-flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Volver</span>
            </a>
            <button type="submit" 
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded inline-flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Registrar Insumo</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Vista previa de la foto
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('photo-preview');
    const photoPreviewImg = document.getElementById('photo-preview-img');
    
    if (photoInput) {
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    photoPreviewImg.src = e.target.result;
                    photoPreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                photoPreview.classList.add('hidden');
            }
        });
    }
});
</script>
@endpush
@endsection