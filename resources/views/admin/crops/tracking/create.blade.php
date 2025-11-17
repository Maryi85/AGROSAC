@extends('admin.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Nuevo Seguimiento de Cultivo</h2>
    <a href="{{ route('admin.crop-tracking.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-emerald-300 rounded text-emerald-700 hover:bg-emerald-100">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Volver</span>
    </a>
</div>
@endsection

@section('content')
<div class="bg-white border rounded p-6">
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.crop-tracking.store') }}" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Fecha de Seguimiento -->
            <div>
                <label for="tracking_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha de Seguimiento <span class="text-red-500">*</span>
                </label>
                <input type="date" 
                       id="tracking_date" 
                       name="tracking_date" 
                       value="{{ old('tracking_date', date('Y-m-d')) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       required>
            </div>

            <!-- Cultivo -->
            <div>
                <label for="crop_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Cultivo <span class="text-red-500">*</span>
                </label>
                <select id="crop_id" 
                        name="crop_id" 
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        required>
                    <option value="">Seleccionar cultivo</option>
                    @foreach($crops as $crop)
                        <option value="{{ $crop->id }}" 
                                data-plot-id="{{ $crop->plot_id }}"
                                {{ old('crop_id') == $crop->id ? 'selected' : '' }}>
                            {{ $crop->name }} - {{ $crop->variety ?? 'Sin variedad' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Lote (se auto-completa) -->
            <div>
                <label for="plot_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Lote <span class="text-red-500">*</span>
                </label>
                <select id="plot_id" 
                        name="plot_id" 
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50"
                        required
                        readonly>
                    <option value="">Seleccione primero un cultivo</option>
                </select>
            </div>

            <!-- Fase -->
            <div>
                <label for="phase" class="block text-sm font-medium text-gray-700 mb-2">
                    Fase
                </label>
                <select id="phase" 
                        name="phase" 
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Seleccionar fase</option>
                    @foreach($phases as $key => $label)
                        <option value="{{ $key }}" {{ old('phase') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Fecha de Corte -->
        <div>
            <label for="cut_date" class="block text-sm font-medium text-gray-700 mb-2">
                Fecha de Corte
            </label>
            <input type="date" 
                   id="cut_date" 
                   name="cut_date" 
                   value="{{ old('cut_date') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>

        <!-- Botones de acción -->
        <div class="flex justify-end gap-4 pt-6 border-t">
            <a href="{{ route('admin.crop-tracking.index') }}" 
               class="px-6 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded transition-colors">
                <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                Guardar Seguimiento
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cropSelect = document.getElementById('crop_id');
    const plotSelect = document.getElementById('plot_id');
    
    // Cargar todos los lotes disponibles
    const plots = @json(\App\Models\Plot::all()->mapWithKeys(function($plot) {
        return [$plot->id => ['id' => $plot->id, 'name' => $plot->name, 'area' => $plot->area]];
    }));
    
    // Función para actualizar el lote cuando se selecciona un cultivo
    cropSelect.addEventListener('change', function() {
        const selectedCrop = this.options[this.selectedIndex];
        const plotId = selectedCrop.getAttribute('data-plot-id');
        
        plotSelect.innerHTML = '<option value="">Seleccione primero un cultivo</option>';
        
        if (plotId && plots[plotId]) {
            const plot = plots[plotId];
            plotSelect.innerHTML = `<option value="${plot.id}" selected>${plot.name} - ${plot.area} ha</option>`;
        } else if (plotId) {
            // Si el plot_id existe pero no está en la lista, intentar cargarlo
            fetch(`/admin/plots/${plotId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.plot) {
                        plotSelect.innerHTML = `<option value="${data.plot.id}" selected>${data.plot.name} - ${data.plot.area} ha</option>`;
                    }
                })
                .catch(error => {
                    console.error('Error al cargar el lote:', error);
                });
        }
    });
    
    // Si hay un valor antiguo, disparar el evento
    if (cropSelect.value) {
        cropSelect.dispatchEvent(new Event('change'));
    }
    
    // Validación de fechas
    const trackingDateInput = document.getElementById('tracking_date');
    const cutDateInput = document.getElementById('cut_date');
    
    function validateDates() {
        const trackingDate = new Date(trackingDateInput.value);
        
        if (cutDateInput.value) {
            const cutDate = new Date(cutDateInput.value);
            if (cutDate < trackingDate) {
                cutDateInput.setCustomValidity('La fecha de corte debe ser posterior o igual a la fecha de seguimiento');
            } else {
                cutDateInput.setCustomValidity('');
            }
        }
    }
    
    trackingDateInput.addEventListener('change', validateDates);
    cutDateInput.addEventListener('change', validateDates);
});
</script>
@endsection
