@extends('admin.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Editar Trabajador</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-4">
    <form method="POST" action="{{ route('admin.workers.update', $worker) }}" enctype="multipart/form-data">
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
                        <input type="text" id="name" name="name" value="{{ old('name', $worker->name) }}" 
                               class="w-full border border-emerald-200 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('name') border-red-500 @enderror" 
                               placeholder="Ingrese el nombre completo" required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $worker->email) }}" 
                               class="w-full border border-emerald-200 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('email') border-red-500 @enderror" 
                               placeholder="correo@ejemplo.com" required>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $worker->phone) }}" 
                               class="w-full border border-emerald-200 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('phone') border-red-500 @enderror" 
                               placeholder="Ej: +57 300 000 0000">
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Foto -->
                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">Foto (opcional)</label>
                        <input type="file" id="photo" name="photo" accept="image/*"
                               class="w-full text-sm @error('photo') border-red-500 @enderror">
                        @error('photo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        @if($worker->photo)
                            <div class="mt-2">
                                <p class="text-xs text-gray-500 mb-1">Foto actual:</p>
                                <img src="{{ asset('storage/' . $worker->photo) }}" alt="Foto actual" class="h-16 w-16 object-cover rounded border border-gray-200">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Información del Sistema -->
            <div class="bg-gray-50 rounded p-4">
                <h3 class="text-md font-semibold text-emerald-700 mb-4">Información del Sistema</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Estado -->
                    <div>
                        <label class="block text-sm font-medium text-emerald-700 mb-2">Estado</label>
                        <select name="status" class="w-full border border-emerald-200 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                            <option value="active" {{ $worker->email_verified_at ? 'selected' : '' }}>Activo</option>
                            <option value="inactive" {{ !$worker->email_verified_at ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>
                    
                    <!-- Fecha de Registro -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Registro</label>
                        <p class="text-sm text-gray-900">{{ $worker->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Acciones Adicionales -->
            <div class="bg-orange-50 rounded p-4">
                <h3 class="text-md font-semibold text-orange-700 mb-4">Acciones Adicionales</h3>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-white rounded border">
                        <div>
                            <p class="font-medium text-gray-900">Recuperar Credenciales</p>
                            <p class="text-sm text-gray-600">Generar una nueva contraseña temporal para este trabajador</p>
                        </div>
                        <form method="POST" action="{{ route('admin.workers.reset-password', $worker) }}" class="inline" data-confirm="true" data-message="¿Restablecer la contraseña de este trabajador? Se generará una nueva contraseña temporal.">
                            @csrf
                            <button type="submit" class="px-3 py-2 bg-orange-100 hover:bg-orange-200 text-orange-700 border border-orange-200 rounded inline-flex items-center gap-2 transition-colors">
                                <i data-lucide="key" class="w-4 h-4"></i>
                                <span>Restablecer</span>
                            </button>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
        
        <!-- Botones de acción -->
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('admin.workers.index') }}" 
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 inline-flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Cancelar</span>
            </a>
            
            <button type="submit" 
                    class="px-4 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded inline-flex items-center gap-2 transition-colors">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Guardar Cambios</span>
            </button>
        </div>
    </form>
</div>
@endsection
