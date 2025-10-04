@extends('foreman.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Editar Trabajador</h2>
    <a href="{{ route('foreman.workers.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
        Volver
    </a>
</div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white border rounded p-6">
        <form method="POST" action="{{ route('foreman.workers.update', $worker) }}" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div>
                    <label for="name" class="block text-sm font-medium text-emerald-700 mb-2">Nombre Completo *</label>
                    <input type="text" name="name" id="name" required 
                           value="{{ old('name', $worker->name) }}"
                           class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-emerald-700 mb-2">Email *</label>
                    <input type="email" name="email" id="email" required 
                           value="{{ old('email', $worker->email) }}"
                           class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="bg-gray-50 rounded p-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Información del Sistema</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                    <div>
                        <span class="font-medium">Estado:</span>
                        <span class="ml-2 px-2 py-1 text-xs rounded-full 
                            {{ $worker->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $worker->email_verified_at ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                    <div>
                        <span class="font-medium">Registrado:</span>
                        <span class="ml-2">{{ $worker->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('foreman.workers.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="bg-emerald-600 text-white px-6 py-2 rounded hover:bg-emerald-700 transition-colors">
                    <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                    Actualizar Trabajador
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
