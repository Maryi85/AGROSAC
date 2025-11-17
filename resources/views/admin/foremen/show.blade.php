@extends('admin.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Detalles del Mayordomo</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-4">
    <div class="space-y-6">
        <!-- Información Básica -->
        <div class="bg-gray-50 rounded p-4">
            <h3 class="text-md font-semibold text-emerald-700 mb-4">Información Básica</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                    <p class="text-sm text-gray-900 mt-1">{{ $foreman->name }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                    <p class="text-sm text-gray-900 mt-1">{{ $foreman->email }}</p>
                </div>
            </div>
        </div>
        
        <!-- Información del Sistema -->
        <div class="bg-gray-50 rounded p-4">
            <h3 class="text-md font-semibold text-emerald-700 mb-4">Información del Sistema</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                    <div class="mt-1">
                        @if($foreman->email_verified_at)
                            <span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Activo</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Inactivo</span>
                        @endif
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Rol</label>
                    <p class="text-sm text-gray-900 mt-1">Mayordomo</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de Registro</label>
                    <p class="text-sm text-gray-900 mt-1">{{ $foreman->created_at->format('d/m/Y H:i') }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Última Actualización</label>
                    <p class="text-sm text-gray-900 mt-1">{{ $foreman->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
        
        <!-- Acciones Disponibles -->
        <div class="bg-blue-50 rounded p-4">
            <h3 class="text-md font-semibold text-blue-700 mb-4">Acciones Disponibles</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Editar Información -->
                <div class="bg-white rounded p-4 border">
                    <div class="flex items-center gap-3 mb-2">
                        <i data-lucide="edit" class="w-5 h-5 text-emerald-600"></i>
                        <h4 class="font-medium text-gray-900">Editar Información</h4>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">Modificar los datos básicos del mayordomo</p>
                    <a href="{{ route('admin.foremen.edit', $foreman) }}" 
                       class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded text-sm transition-colors">
                        <i data-lucide="pencil" class="w-4 h-4"></i>
                        <span>Editar</span>
                    </a>
                </div>
                
                
                <!-- Cambiar Estado -->
                <div class="bg-white rounded p-4 border">
                    <div class="flex items-center gap-3 mb-2">
                        <i data-lucide="{{ $foreman->email_verified_at ? 'user-x' : 'user-check' }}" class="w-5 h-5 {{ $foreman->email_verified_at ? 'text-red-600' : 'text-emerald-600' }}"></i>
                        <h4 class="font-medium text-gray-900">Cambiar Estado</h4>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">{{ $foreman->email_verified_at ? 'Desactivar' : 'Activar' }} la cuenta del mayordomo</p>
                    <form method="POST" action="{{ route('admin.foremen.toggle-status', $foreman) }}" class="inline" data-confirm="true" data-message="{{ $foreman->email_verified_at ? '¿Desactivar este mayordomo?' : '¿Activar este mayordomo?' }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 {{ $foreman->email_verified_at ? 'bg-red-100 hover:bg-red-200 text-red-700 border border-red-200' : 'bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200' }} rounded text-sm transition-colors">
                            <i data-lucide="{{ $foreman->email_verified_at ? 'user-x' : 'user-check' }}" class="w-4 h-4"></i>
                            <span>{{ $foreman->email_verified_at ? 'Desactivar' : 'Activar' }}</span>
                        </button>
                    </form>
                </div>
                
                <!-- Eliminar Mayordomo -->
                <div class="bg-white rounded p-4 border">
                    <div class="flex items-center gap-3 mb-2">
                        <i data-lucide="trash" class="w-5 h-5 text-red-600"></i>
                        <h4 class="font-medium text-gray-900">Eliminar Mayordomo</h4>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">Eliminar permanentemente del sistema</p>
                    <form method="POST" action="{{ route('admin.foremen.destroy', $foreman) }}" class="inline" data-confirm="true" data-message="¿Eliminar este mayordomo? Esta acción no se puede deshacer.">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 border border-red-200 rounded text-sm transition-colors">
                            <i data-lucide="trash" class="w-4 h-4"></i>
                            <span>Eliminar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Botones de navegación -->
    <div class="mt-6 flex justify-between">
        <a href="{{ route('admin.foremen.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Volver al Listado</span>
        </a>
        
        <a href="{{ route('admin.foremen.edit', $foreman) }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded transition-colors">
            <i data-lucide="edit" class="w-4 h-4"></i>
            <span>Editar Mayordomo</span>
        </a>
    </div>
</div>
@endsection
