@extends('worker.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Mi Perfil</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-6 max-w-3xl">
    @if (session('status'))
        <div class="mb-4 flex items-start gap-3 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-md shadow-sm">
            <i data-lucide="check-circle" class="w-5 h-5 mt-0.5 text-emerald-600"></i>
            <div>
                <p class="font-medium">Datos guardados</p>
                <p class="text-sm text-emerald-700">{{ session('status') }}</p>
            </div>
        </div>
    @endif

    <!-- Vista de solo lectura -->
    <div id="view-mode" class="space-y-6">
        <div class="flex items-center justify-between pb-6 border-b">
            <div class="flex items-center gap-4">
                <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-100 border-2 border-gray-200 shadow-sm">
                    @if($user->photo)
                        <img src="{{ asset('storage/' . $user->photo) }}" alt="Foto de perfil" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gradient-to-br from-gray-50 to-gray-100">
                            <i data-lucide="user" class="w-12 h-12"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $user->email }}</p>
                </div>
            </div>
            <button id="edit-btn" onclick="toggleEditMode()" class="inline-flex items-center justify-center w-10 h-10 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded-full transition-colors" title="Editar perfil">
                <i data-lucide="pencil" class="w-5 h-5"></i>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nombre Completo</label>
                <p class="text-sm text-gray-900 font-medium">{{ $user->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Correo Electrónico</label>
                <p class="text-sm text-gray-900 font-medium">{{ $user->email }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Teléfono</label>
                <p class="text-sm text-gray-900 font-medium">{{ $user->phone ?: 'No registrado' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Registro</label>
                <p class="text-sm text-gray-900 font-medium">{{ $user->created_at->format('d/m/Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Vista de edición -->
    <form id="edit-mode" method="POST" action="{{ route('worker.profile.update') }}" enctype="multipart/form-data" class="space-y-6 hidden">
        @csrf
        @method('PUT')

        <div class="flex items-center gap-4">
            <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100 border border-gray-200">
                @if($user->photo)
                    <img id="photo-preview" src="{{ asset('storage/' . $user->photo) }}" alt="Foto de perfil" class="w-full h-full object-cover">
                @else
                    <div id="photo-placeholder" class="w-full h-full flex items-center justify-center text-gray-400">
                        <i data-lucide="user" class="w-8 h-8"></i>
                    </div>
                @endif
            </div>
            <div>
                <p class="text-sm text-gray-600">Puedes actualizar tu foto de perfil y tu información de contacto.</p>
                <input type="file" name="photo" id="photo-input" accept="image/*" class="mt-2 text-sm @error('photo') border-red-500 @enderror">
                @error('photo')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('name') border-red-500 @enderror" required>
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Correo (solo lectura)</label>
                <input type="email" value="{{ $user->email }}" disabled class="w-full border border-gray-200 bg-gray-50 rounded px-3 py-2 text-gray-600">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('phone') border-red-500 @enderror"
                       placeholder="Ej: +57 300 000 0000">
                @error('phone')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button type="button" onclick="toggleEditMode()" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">Cancelar</button>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm">
                Guardar cambios
            </button>
        </div>
    </form>
</div>

<script>
function toggleEditMode() {
    const viewMode = document.getElementById('view-mode');
    const editMode = document.getElementById('edit-mode');
    const editBtn = document.getElementById('edit-btn');
    
    if (viewMode.classList.contains('hidden')) {
        // Cambiar a vista
        viewMode.classList.remove('hidden');
        editMode.classList.add('hidden');
        editBtn.innerHTML = '<i data-lucide="pencil" class="w-5 h-5"></i>';
        editBtn.title = 'Editar perfil';
        editBtn.classList.remove('bg-gray-100', 'hover:bg-gray-200', 'text-gray-700', 'border-gray-200');
        editBtn.classList.add('bg-emerald-100', 'hover:bg-emerald-200', 'text-emerald-700', 'border-emerald-200');
    } else {
        // Cambiar a edición
        viewMode.classList.add('hidden');
        editMode.classList.remove('hidden');
        editBtn.innerHTML = '<i data-lucide="x" class="w-5 h-5"></i>';
        editBtn.title = 'Cancelar edición';
        editBtn.classList.remove('bg-emerald-100', 'hover:bg-emerald-200', 'text-emerald-700', 'border-emerald-200');
        editBtn.classList.add('bg-gray-100', 'hover:bg-gray-200', 'text-gray-700', 'border-gray-200');
    }
    
    if (window.lucide) {
        window.lucide.createIcons();
    }
}

// Vista previa de imagen
document.getElementById('photo-input')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('photo-preview');
            const placeholder = document.getElementById('photo-placeholder');
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            if (placeholder) {
                placeholder.style.display = 'none';
            }
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection

