@extends('admin.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Editar Cultivo</h2>
    <a href="{{ route('admin.crops.index') }}" class="inline-flex items-center gap-2 px-3 py-2 border border-emerald-300 rounded text-emerald-700 hover:bg-emerald-100">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Volver</span>
    </a>
</div>
@endsection

@section('content')
<div class="bg-white border rounded p-6">
    @if (session('status'))
        <div class="mb-6 p-4 bg-emerald-100 border border-emerald-300 text-emerald-700 rounded">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-100 border border-red-300 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.crops.update', $crop) }}" class="space-y-6" id="editCropForm" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- Información básica -->
        <div class="border-b pb-6">
            <h3 class="text-lg font-medium text-emerald-800 mb-4">Información Básica</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre del cultivo -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-emerald-800 mb-2">Nombre del Cultivo *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $crop->name) }}" class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Ej: Maíz, Tomate, Lechuga..." required>
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Variedad -->
                <div>
                    <label for="variety" class="block text-sm font-medium text-emerald-800 mb-2">Variedad</label>
                    <input type="text" name="variety" id="variety" value="{{ old('variety', $crop->variety) }}" class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Ej: Híbrido 123, Cherry, Romana...">
                    @error('variety')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rendimiento por hectárea -->
                <div>
                    <label for="yield_per_hectare" class="block text-sm font-medium text-emerald-800 mb-2">Rendimiento por Hectárea (kg)</label>
                    <input type="number" name="yield_per_hectare" id="yield_per_hectare" value="{{ old('yield_per_hectare', $crop->yield_per_hectare) }}" step="0.01" min="0" class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Ej: 5000.50">
                    <p class="text-xs text-gray-500 mt-1">Rendimiento esperado en kilogramos por hectárea</p>
                    @error('yield_per_hectare')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lote -->
                <div>
                    <label for="plot_id" class="block text-sm font-medium text-emerald-800 mb-2">Lote *</label>
                    <select name="plot_id" id="plot_id" class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                        <option value="">Seleccionar lote...</option>
                        @foreach($plots as $plot)
                            <option value="{{ $plot->id }}" {{ old('plot_id', $crop->plot_id) == $plot->id ? 'selected' : '' }}>
                                {{ $plot->name }} - {{ $plot->location }} ({{ $plot->area }} ha)
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Lote donde se sembrará este cultivo</p>
                    @error('plot_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Descripción -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-emerald-800 mb-2">Descripción</label>
                <textarea name="description" id="description" rows="3" class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Describe las características del cultivo, cuidados especiales, etc.">{{ old('description', $crop->description) }}</textarea>
                @error('description')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Foto -->
            <div class="mt-6">
                <label for="photo" class="block text-sm font-medium text-emerald-800 mb-2">Foto del Cultivo</label>
                @if(!empty($crop->photo))
                    <div class="mb-3">
                        <p class="text-sm text-gray-600 mb-2">Foto actual:</p>
                        <img src="{{ asset('storage/' . $crop->photo) }}" alt="Foto actual" class="max-w-xs rounded border border-emerald-200" onerror="this.style.display='none';">
                    </div>
                @endif
                <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/jpg,image/gif" 
                       class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <p class="text-xs text-gray-500 mt-1">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB</p>
                @error('photo')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
                <div id="photo-preview" class="mt-3 hidden">
                    <p class="text-sm text-gray-600 mb-2">Nueva foto:</p>
                    <img id="photo-preview-img" src="" alt="Vista previa" class="max-w-xs rounded border border-emerald-200">
                </div>
            </div>
        </div>


        <!-- Estado actual -->
        <div class="bg-gray-50 p-4 rounded">
            <h3 class="text-sm font-medium text-gray-800 mb-2">Estado Actual</h3>
            <div class="flex items-center gap-2">
                <span class="px-2 py-1 text-xs rounded {{ $crop->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                    {{ $crop->status === 'active' ? 'Activo' : 'Inactivo' }}
                </span>
                <span class="text-xs text-gray-600">
                    ({{ $crop->tasks()->count() }} tareas asociadas)
                </span>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex items-center gap-4 pt-6 border-t">
            <button type="submit" class="px-6 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded inline-flex items-center gap-2 transition-colors">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Actualizar Cultivo</span>
            </button>
            <a href="{{ route('admin.crops.index') }}" class="px-6 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 inline-flex items-center gap-2">
                <i data-lucide="x" class="w-4 h-4"></i>
                <span>Cancelar</span>
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editCropForm');
    const submitButton = form.querySelector('button[type="submit"]');
    
    form.addEventListener('submit', function(e) {
        // Mostrar estado de carga en el botón
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i><span>Actualizando cultivo...</span>';
        submitButton.disabled = true;
        
        // Restaurar el botón después de un tiempo (en caso de que haya errores de validación)
        setTimeout(() => {
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        }, 5000);
    });
    
    // Auto-ocultar mensajes de éxito después de 5 segundos
    const successMessage = document.querySelector('.bg-emerald-100');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.transition = 'opacity 0.5s ease-out';
            successMessage.style.opacity = '0';
            setTimeout(() => {
                if (successMessage.parentNode) {
                    successMessage.parentNode.removeChild(successMessage);
                }
            }, 500);
        }, 5000);
    }
    
    // Auto-ocultar mensajes de error después de 7 segundos
    const errorMessage = document.querySelector('.bg-red-100');
    if (errorMessage) {
        setTimeout(() => {
            errorMessage.style.transition = 'opacity 0.5s ease-out';
            errorMessage.style.opacity = '0';
            setTimeout(() => {
                if (errorMessage.parentNode) {
                    errorMessage.parentNode.removeChild(errorMessage);
                }
            }, 500);
        }, 7000);
    }
    
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
