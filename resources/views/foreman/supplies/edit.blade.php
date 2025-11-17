@extends('layouts.app')

@section('content')
<div class="bg-white border rounded p-4">
    <div class="mb-4">
        <h3 class="text-lg font-semibold text-emerald-700">Editar Insumo</h3>
    </div>

    <form action="{{ route('foreman.supplies.update', $supply) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <!-- Nombre -->
        <div>
            <label for="name" class="block text-sm mb-1 text-emerald-800">Nombre del Insumo</label>
            <input type="text" name="name" id="name" class="w-full border border-emerald-200 rounded px-3 py-2" value="{{ old('name', $supply->name) }}" required>
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Unidad y Costo -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="unit" class="block text-sm mb-1 text-emerald-800">Unidad de Medida</label>
                <select name="unit" id="unit" class="w-full border border-emerald-200 rounded px-3 py-2" required>
                    @foreach($units as $value => $label)
                        <option value="{{ $value }}" {{ old('unit', $supply->unit) == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('unit')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="unit_cost" class="block text-sm mb-1 text-emerald-800">Costo por Unidad</label>
                <input type="number" step="0.01" min="0" name="unit_cost" id="unit_cost" class="w-full border border-emerald-200 rounded px-3 py-2" value="{{ old('unit_cost', $supply->unit_cost) }}" required>
                @error('unit_cost')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Stock Mínimo -->
        <div>
            <label for="min_stock" class="block text-sm mb-1 text-emerald-800">Stock Mínimo</label>
            <input type="number" step="0.01" min="0" name="min_stock" id="min_stock" class="w-full border border-emerald-200 rounded px-3 py-2" value="{{ old('min_stock', $supply->min_stock) }}" required>
            @error('min_stock')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Foto -->
        <div>
            <label for="photo" class="block text-sm mb-1 text-emerald-800">Foto</label>
            @if($supply->photo)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $supply->photo) }}" alt="{{ $supply->name }}" class="w-32 h-32 object-cover rounded">
                </div>
            @endif
            <input type="file" name="photo" id="photo" class="w-full border border-emerald-200 rounded px-3 py-2" accept="image/*">
            @error('photo')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Estado -->
        <div>
            <label for="status" class="block text-sm mb-1 text-emerald-800">Estado</label>
            <select name="status" id="status" class="w-full border border-emerald-200 rounded px-3 py-2">
                <option value="active" {{ old('status', $supply->status) == 'active' ? 'selected' : '' }}>Activo</option>
                <option value="inactive" {{ old('status', $supply->status) == 'inactive' ? 'selected' : '' }}>Inactivo</option>
            </select>
            @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Botones -->
        <div class="flex items-center gap-2">
            <a href="{{ route('foreman.supplies.index') }}" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
                <i data-lucide="x" class="w-4 h-4 inline mr-2"></i>
                Cancelar
            </a>
            <button type="submit" class="px-4 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded transition-colors">
                <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                Actualizar
            </button>
        </div>
    </form>
</div>
@endsection