@extends('admin.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Registrar Movimiento Contable</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-4">
    <form method="POST" action="{{ route('admin.ledger.store') }}" class="space-y-4">
        @csrf
        
        <!-- Tipo de Movimiento -->
        <div>
            <label for="type" class="block text-sm mb-1 text-emerald-800">Tipo de Movimiento</label>
            <select id="type" name="type" 
                    class="w-full border border-emerald-200 rounded px-3 py-2 @error('type') border-red-500 @enderror" 
                    required>
                <option value="">Seleccionar tipo</option>
                @foreach($types as $key => $label)
                    <option value="{{ $key }}" {{ old('type') === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('type')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Categoría -->
        <div>
            <label for="category" class="block text-sm mb-1 text-emerald-800">Categoría</label>
            <select id="category" name="category" 
                    class="w-full border border-emerald-200 rounded px-3 py-2 @error('category') border-red-500 @enderror" 
                    required>
                <option value="">Seleccionar categoría</option>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('category')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Monto -->
        <div>
            <label for="amount" class="block text-sm mb-1 text-emerald-800">Monto</label>
            <input type="number" step="0.01" min="0.01" id="amount" name="amount" value="{{ old('amount') }}" 
                   class="w-full border border-emerald-200 rounded px-3 py-2 @error('amount') border-red-500 @enderror" 
                   required />
            @error('amount')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Fecha -->
        <div>
            <label for="occurred_at" class="block text-sm mb-1 text-emerald-800">Fecha del Movimiento</label>
            <input type="date" id="occurred_at" name="occurred_at" value="{{ old('occurred_at', date('Y-m-d')) }}" 
                   class="w-full border border-emerald-200 rounded px-3 py-2 @error('occurred_at') border-red-500 @enderror" 
                   required />
            @error('occurred_at')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Cultivo -->
        <div>
            <label for="crop_id" class="block text-sm mb-1 text-emerald-800">Cultivo (Opcional)</label>
            <select id="crop_id" name="crop_id" 
                    class="w-full border border-emerald-200 rounded px-3 py-2 @error('crop_id') border-red-500 @enderror">
                <option value="">Seleccionar cultivo</option>
                @foreach($crops as $crop)
                    <option value="{{ $crop->id }}" {{ old('crop_id') == $crop->id ? 'selected' : '' }}>
                        {{ $crop->name }}
                    </option>
                @endforeach
            </select>
            @error('crop_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Lote -->
        <div>
            <label for="plot_id" class="block text-sm mb-1 text-emerald-800">Lote (Opcional)</label>
            <select id="plot_id" name="plot_id" 
                    class="w-full border border-emerald-200 rounded px-3 py-2 @error('plot_id') border-red-500 @enderror">
                <option value="">Seleccionar lote</option>
                @foreach($plots as $plot)
                    <option value="{{ $plot->id }}" {{ old('plot_id') == $plot->id ? 'selected' : '' }}>
                        {{ $plot->name }}
                    </option>
                @endforeach
            </select>
            @error('plot_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Referencia -->
        <div>
            <label for="reference" class="block text-sm mb-1 text-emerald-800">Referencia (Opcional)</label>
            <input type="text" id="reference" name="reference" value="{{ old('reference') }}" 
                   placeholder="Ej: Factura #123, Recibo de pago, etc."
                   class="w-full border border-emerald-200 rounded px-3 py-2 @error('reference') border-red-500 @enderror" />
            <p class="text-xs text-gray-500 mt-1">Puede incluir número de factura, recibo o cualquier referencia útil</p>
            @error('reference')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Información adicional -->
        <div class="bg-blue-50 border border-blue-200 rounded p-4">
            <h4 class="text-sm font-semibold text-blue-800 mb-2">Información Importante</h4>
            <ul class="text-xs text-blue-700 space-y-1">
                <li>• Los ingresos representan dinero que entra a la finca (ventas, subsidios, etc.)</li>
                <li>• Los gastos representan dinero que sale de la finca (insumos, mano de obra, etc.)</li>
                <li>• Puede asociar el movimiento a un cultivo o lote específico</li>
                <li>• La referencia es útil para documentar el movimiento (facturas, recibos, etc.)</li>
                <li>• Esta información será utilizada para generar reportes y análisis financieros</li>
            </ul>
        </div>
        
        <!-- Botones -->
        <div class="flex items-center gap-2 pt-4">
            <a href="{{ route('admin.ledger.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 inline-flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Volver</span>
            </a>
            <button type="submit" 
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded inline-flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Registrar Movimiento</span>
            </button>
        </div>
    </form>
</div>
@endsection
