@extends('foreman.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Editar Herramienta</h2>
    <a href="{{ route('foreman.tools.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
        Volver
    </a>
</div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white border rounded p-6">
        <form method="POST" action="{{ route('foreman.tools.update', $tool) }}" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div>
                    <label for="name" class="block text-sm font-medium text-emerald-700 mb-2">Nombre de la Herramienta *</label>
                    <input type="text" name="name" id="name" required 
                           value="{{ old('name', $tool->name) }}"
                           class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Categoría -->
                <div>
                    <label for="category" class="block text-sm font-medium text-emerald-700 mb-2">Categoría</label>
                    <input type="text" name="category" id="category" 
                           value="{{ old('category', $tool->category) }}"
                           class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('category') border-red-500 @enderror">
                    @error('category')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estado -->
                <div>
                    <label for="status" class="block text-sm font-medium text-emerald-700 mb-2">Estado *</label>
                    <select name="status" id="status" required 
                            class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('status') border-red-500 @enderror">
                        <option value="">Seleccionar estado</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ (old('status') ?? $tool->status) == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total Qty -->
                <div>
                    <label for="total_qty" class="block text-sm font-medium text-emerald-700 mb-2">Cantidad Total *</label>
                    <input type="number" name="total_qty" id="total_qty" required min="0" 
                           value="{{ old('total_qty', $tool->total_qty) }}"
                           class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('total_qty') border-red-500 @enderror">
                    @error('total_qty')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Available Qty -->
            <div>
                <label for="available_qty" class="block text-sm font-medium text-emerald-700 mb-2">Cantidad Disponible *</label>
                <input type="number" name="available_qty" id="available_qty" required min="0" 
                       value="{{ old('available_qty', $tool->available_qty) }}"
                       class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('available_qty') border-red-500 @enderror">
                <p class="text-sm text-gray-600 mt-1">La cantidad disponible no puede ser mayor a la cantidad total</p>
                @error('available_qty')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('foreman.tools.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="bg-emerald-600 text-white px-6 py-2 rounded hover:bg-emerald-700 transition-colors">
                    <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                    Actualizar Herramienta
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-update available_qty when total_qty changes
document.getElementById('total_qty').addEventListener('input', function() {
    const totalQty = parseInt(this.value) || 0;
    const availableQtyInput = document.getElementById('available_qty');
    const currentAvailableQty = parseInt(availableQtyInput.value) || 0;
    
    if (currentAvailableQty > totalQty) {
        availableQtyInput.value = totalQty;
    }
    availableQtyInput.max = totalQty;
});
</script>
@endsection
