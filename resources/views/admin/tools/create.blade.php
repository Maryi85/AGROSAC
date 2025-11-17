@php
    $layout = route_prefix() === 'foreman.' ? 'foreman.layout' : 'admin.layout';
@endphp
@extends($layout)

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Registrar Nueva Herramienta</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-4">
    <form method="POST" action="{{ route(route_prefix() . 'tools.store') }}" class="space-y-4" enctype="multipart/form-data">
        @csrf
        
        <!-- Nombre -->
        <div>
            <label for="name" class="block text-sm mb-1 text-emerald-800">Nombre de la Herramienta</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" 
                   class="w-full border border-emerald-200 rounded px-3 py-2 @error('name') border-red-500 @enderror" 
                   required />
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Categoría -->
        <div>
            <label for="category" class="block text-sm mb-1 text-emerald-800">Categoría</label>
            <select id="category" name="category" 
                    class="w-full border border-emerald-200 rounded px-3 py-2 @error('category') border-red-500 @enderror" 
                    required>
                <option value="">Seleccionar categoría</option>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('category')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Estado -->
        <div>
            <label for="status" class="block text-sm mb-1 text-emerald-800">Estado</label>
            <select id="status" name="status" 
                    class="w-full border border-emerald-200 rounded px-3 py-2 @error('status') border-red-500 @enderror" 
                    required>
                <option value="">Seleccionar estado</option>
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" {{ old('status') === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('status')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Foto -->
        <div>
            <label for="photo" class="block text-sm mb-1 text-emerald-800">Foto de la Herramienta</label>
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
        
        <!-- Botones -->
        <div class="flex items-center gap-2 pt-4">
            <a href="{{ route(route_prefix() . 'tools.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 inline-flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Volver</span>
            </a>
            <button type="submit" 
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded inline-flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Registrar Herramienta</span>
            </button>
        </div>
    </form>
</div>

<script>
// Vista previa de la foto
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
@endsection
