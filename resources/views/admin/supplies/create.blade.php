@extends('admin.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Registrar Nuevo Insumo</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-4">
    <form method="POST" action="{{ route('admin.supplies.store') }}" class="space-y-4">
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
        
        <!-- Unidad y Costo -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="unit" class="block text-sm mb-1 text-emerald-800">Unidad de Medida</label>
                <select id="unit" name="unit" 
                        class="w-full border border-emerald-200 rounded px-3 py-2 @error('unit') border-red-500 @enderror" 
                        required>
                    <option value="">Seleccionar unidad</option>
                    @foreach($units as $key => $label)
                        <option value="{{ $key }}" {{ old('unit') === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('unit')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="unit_cost" class="block text-sm mb-1 text-emerald-800">Costo por Unidad</label>
                <input type="number" step="0.01" min="0" id="unit_cost" name="unit_cost" value="{{ old('unit_cost', 0) }}" 
                       class="w-full border border-emerald-200 rounded px-3 py-2 @error('unit_cost') border-red-500 @enderror" 
                       required />
                @error('unit_cost')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Estado -->
        <div>
            <label for="status" class="block text-sm mb-1 text-emerald-800">Estado</label>
            <select id="status" name="status" 
                    class="w-full border border-emerald-200 rounded px-3 py-2 @error('status') border-red-500 @enderror" 
                    required>
                <option value="">Seleccionar estado</option>
                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Activo</option>
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
                <li>• El costo por unidad se utilizará para calcular el costo total de los consumos</li>
                <li>• Los insumos activos estarán disponibles para registrar consumos</li>
                <li>• Una vez registrado, podrá crear consumos de este insumo en el módulo correspondiente</li>
            </ul>
        </div>
        
        <!-- Botones -->
        <div class="flex items-center gap-2 pt-4">
            <a href="{{ route('admin.supplies.index') }}" 
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
@endsection
