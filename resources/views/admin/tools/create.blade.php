@extends('admin.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Registrar Nueva Herramienta</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-4">
    <form method="POST" action="{{ route('admin.tools.store') }}" class="space-y-4">
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
        
        <!-- Cantidades -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="total_qty" class="block text-sm mb-1 text-emerald-800">Cantidad Total</label>
                <input type="number" id="total_qty" name="total_qty" value="{{ old('total_qty', 0) }}" 
                       min="0" 
                       class="w-full border border-emerald-200 rounded px-3 py-2 @error('total_qty') border-red-500 @enderror" 
                       required />
                @error('total_qty')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="available_qty" class="block text-sm mb-1 text-emerald-800">Cantidad Disponible</label>
                <input type="number" id="available_qty" name="available_qty" value="{{ old('available_qty', 0) }}" 
                       min="0" 
                       class="w-full border border-emerald-200 rounded px-3 py-2 @error('available_qty') border-red-500 @enderror" 
                       required />
                @error('available_qty')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Botones -->
        <div class="flex items-center gap-2 pt-4">
            <a href="{{ route('admin.tools.index') }}" 
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
// Validación en tiempo real para cantidad disponible
document.addEventListener('DOMContentLoaded', function() {
    const totalQtyInput = document.getElementById('total_qty');
    const availableQtyInput = document.getElementById('available_qty');
    
    function validateAvailableQty() {
        const totalQty = parseInt(totalQtyInput.value) || 0;
        const availableQty = parseInt(availableQtyInput.value) || 0;
        
        if (availableQty > totalQty) {
            availableQtyInput.setCustomValidity('La cantidad disponible no puede ser mayor que la cantidad total');
            availableQtyInput.reportValidity();
        } else {
            availableQtyInput.setCustomValidity('');
        }
    }
    
    totalQtyInput.addEventListener('input', validateAvailableQty);
    availableQtyInput.addEventListener('input', validateAvailableQty);
});
</script>
@endsection
