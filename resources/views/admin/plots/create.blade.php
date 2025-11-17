@extends('admin.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Nuevo Lote</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-6 max-w-4xl">
    <form method="POST" action="{{ route('admin.plots.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Nombre <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full border border-emerald-200 rounded px-3 py-2" required />
            @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1 text-emerald-800">Ubicación <span class="text-red-500">*</span></label>
                <input type="text" name="location" value="{{ old('location') }}" class="w-full border border-emerald-200 rounded px-3 py-2" required />
                @error('location')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm mb-1 text-emerald-800">Área (ha) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="1" name="area" value="{{ old('area') }}" class="w-full border border-emerald-200 rounded px-3 py-2" required />
                @error('area')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Estado <span class="text-red-500">*</span></label>
            <select name="status" class="w-full border border-emerald-200 rounded px-3 py-2" required>
                <option value="active" {{ old('status', 'active')==='active' ? 'selected' : '' }}>Activo</option>
                <option value="inactive" {{ old('status')==='inactive' ? 'selected' : '' }}>Inactivo</option>
            </select>
            @error('status')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.plots.index') }}" class="px-3 py-2 border rounded inline-flex items-center gap-2"><i data-lucide="arrow-left" class="w-4 h-4"></i><span>Cancelar</span></a>
            <button class="px-3 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded inline-flex items-center gap-2 transition-colors"><i data-lucide="save" class="w-4 h-4"></i><span>Guardar</span></button>
        </div>
    </form>
</div>
@endsection


