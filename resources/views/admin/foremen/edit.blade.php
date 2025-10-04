@extends('admin.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Editar Mayordomo</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-4">
    <form method="POST" action="{{ route('admin.foremen.update', $foreman) }}">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <!-- Información Básica -->
            <div class="bg-gray-50 rounded p-4">
                <h3 class="text-md font-semibold text-emerald-700 mb-4">Información Básica</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nombre -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $foreman->name) }}" 
                               class="w-full border border-emerald-200 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('name') border-red-500 @enderror" 
                               placeholder="Ingrese el nombre completo" required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $foreman->email) }}" 
                               class="w-full border border-emerald-200 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('email') border-red-500 @enderror" 
                               placeholder="correo@ejemplo.com" required>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Información del Sistema -->
            <div class="bg-gray-50 rounded p-4">
                <h3 class="text-md font-semibold text-emerald-700 mb-4">Información del Sistema</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Estado -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado Actual</label>
                        <div class="flex items-center gap-2">
                            @if($foreman->email_verified_at)
                                <span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Activo</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Inactivo</span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Fecha de Registro -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Registro</label>
                        <p class="text-sm text-gray-900">{{ $foreman->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Acciones Adicionales -->
            <div class="bg-orange-50 rounded p-4">
                <h3 class="text-md font-semibold text-orange-700 mb-4">Acciones Adicionales</h3>
                
                <div class="space-y-3">
                    
                    <div class="flex items-center justify-between p-3 bg-white rounded border">
                        <div>
                            <p class="font-medium text-gray-900">Cambiar Estado</p>
                            <p class="text-sm text-gray-600">{{ $foreman->email_verified_at ? 'Desactivar' : 'Activar' }} la cuenta de este mayordomo</p>
                        </div>
                        <form method="POST" action="{{ route('admin.foremen.toggle-status', $foreman) }}" class="inline" data-confirm="true" data-message="{{ $foreman->email_verified_at ? '¿Desactivar este mayordomo?' : '¿Activar este mayordomo?' }}">
                            @csrf
                            <button type="submit" class="px-3 py-2 {{ $foreman->email_verified_at ? 'bg-red-600 hover:bg-red-700' : 'bg-emerald-600 hover:bg-emerald-700' }} text-white rounded inline-flex items-center gap-2">
                                <i data-lucide="{{ $foreman->email_verified_at ? 'user-x' : 'user-check' }}" class="w-4 h-4"></i>
                                <span>{{ $foreman->email_verified_at ? 'Desactivar' : 'Activar' }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Botones de acción -->
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('admin.foremen.index') }}" 
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 inline-flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Cancelar</span>
            </a>
            
            <button type="submit" 
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded inline-flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Guardar Cambios</span>
            </button>
        </div>
    </form>
</div>
@endsection
