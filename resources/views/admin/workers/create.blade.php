@extends('admin.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Crear Nuevo Trabajador</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-4">
    <form method="POST" action="{{ route('admin.workers.store') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="space-y-6">
            <!-- Información Básica -->
            <div class="bg-gray-50 rounded p-4">
                <h3 class="text-md font-semibold text-emerald-700 mb-4">Información Básica</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nombre -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" 
                               class="w-full border border-emerald-200 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('name') border-red-500 @enderror" 
                               placeholder="Ingrese el nombre completo" required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" 
                               class="w-full border border-emerald-200 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('email') border-red-500 @enderror" 
                               placeholder="correo@ejemplo.com" required>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" 
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
                    </div>
                </div>
            </div>
            
            <!-- Información del Sistema -->
            <div class="bg-blue-50 rounded p-4">
                <h3 class="text-md font-semibold text-blue-700 mb-4">Información del Sistema</h3>
                
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <i data-lucide="info" class="w-5 h-5 text-blue-600 mt-0.5"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium">Contraseña Temporal</p>
                            <p>Se generará automáticamente una contraseña temporal de 8 caracteres que deberá compartir con el trabajador de forma segura.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <i data-lucide="shield-check" class="w-5 h-5 text-blue-600 mt-0.5"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium">Activación Inmediata</p>
                            <p>El trabajador será activado inmediatamente y podrá acceder al sistema con las credenciales proporcionadas.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <i data-lucide="user-check" class="w-5 h-5 text-blue-600 mt-0.5"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium">Rol de Trabajador</p>
                            <p>Se asignará automáticamente el rol de trabajador con permisos para ver y completar tareas asignadas.</p>
                        </div>
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
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded inline-flex items-center gap-2">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Crear Trabajador</span>
            </button>
        </div>
    </form>
</div>
@endsection
