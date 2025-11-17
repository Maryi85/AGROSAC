@extends('worker.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Devolver Herramienta</h2>
    <a href="{{ route('worker.loans.show', $loan) }}" class="inline-flex items-center gap-2 px-4 py-2 border border-emerald-300 rounded text-emerald-700 hover:bg-emerald-100">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Volver</span>
    </a>
</div>
@endsection

@section('content')
<div class="bg-white border rounded p-6">
    <!-- Información del préstamo -->
    <div class="mb-6 p-4 bg-gray-50 rounded border">
        <h3 class="font-medium text-gray-900 mb-3">Información del Préstamo</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <span class="text-gray-600">Herramienta:</span>
                <span class="font-medium text-gray-900">{{ $loan->tool->name }}</span>
            </div>
            <div>
                <span class="text-gray-600">Cantidad:</span>
                <span class="font-medium text-gray-900">{{ $loan->quantity }}</span>
            </div>
            <div>
                <span class="text-gray-600">Fecha de Préstamo:</span>
                <span class="font-medium text-gray-900">{{ $loan->out_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('worker.loans.process-return', $loan) }}" class="space-y-6">
        @csrf
        
        <!-- Condición de devolución -->
        <div>
            <label for="condition_return" class="block text-sm font-medium text-gray-700 mb-2">
                Condición de la herramienta al devolver <span class="text-red-500">*</span>
            </label>
            <div class="space-y-3">
                <label class="flex items-center p-3 border rounded cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="condition_return" value="good" required
                           class="mr-3 text-emerald-600 focus:ring-emerald-500"
                           {{ old('condition_return') === 'good' ? 'checked' : '' }}>
                    <div>
                        <div class="font-medium text-gray-900">Buen estado</div>
                        <div class="text-sm text-gray-500">La herramienta está en perfectas condiciones</div>
                    </div>
                </label>
                
                <label class="flex items-center p-3 border rounded cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="condition_return" value="damaged" required
                           class="mr-3 text-orange-600 focus:ring-orange-500"
                           {{ old('condition_return') === 'damaged' ? 'checked' : '' }}>
                    <div>
                        <div class="font-medium text-gray-900">Dañado</div>
                        <div class="text-sm text-gray-500">La herramienta presenta algún daño o desgaste</div>
                    </div>
                </label>
                
                <label class="flex items-center p-3 border rounded cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="condition_return" value="lost" required
                           class="mr-3 text-red-600 focus:ring-red-500"
                           {{ old('condition_return') === 'lost' ? 'checked' : '' }}>
                    <div>
                        <div class="font-medium text-gray-900">Perdido</div>
                        <div class="text-sm text-gray-500">La herramienta se ha perdido o extraviado</div>
                    </div>
                </label>
            </div>
            @error('condition_return')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Notas de devolución -->
        <div>
            <label for="return_notes" class="block text-sm font-medium text-gray-700 mb-2">
                Notas de la devolución
            </label>
            <textarea name="return_notes" id="return_notes" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('return_notes') border-red-500 @enderror"
                      placeholder="Describe el estado de la herramienta o cualquier observación...">{{ old('return_notes') }}</textarea>
            @error('return_notes')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-sm text-gray-500">Opcional: Proporciona detalles sobre el estado de la herramienta</p>
        </div>

        <!-- Advertencia -->
        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Importante</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>Una vez que envíes esta devolución, el administrador deberá confirmar la recepción de la herramienta. 
                        El estado del préstamo cambiará a "Devuelto" y esperará la confirmación final del administrador.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-end gap-4 pt-6 border-t">
            <a href="{{ route('worker.loans.show', $loan) }}" 
               class="px-6 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
                Devolver Herramienta
            </button>
        </div>
    </form>
</div>
@endsection
