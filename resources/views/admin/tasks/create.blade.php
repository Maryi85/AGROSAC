@extends('admin.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Asignar Nueva Tarea</h2>
    <a href="{{ route('admin.tasks.index') }}" class="inline-flex items-center gap-2 px-3 py-2 border border-emerald-300 rounded text-emerald-700 hover:bg-emerald-100">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Volver</span>
    </a>
</div>
@endsection

@section('content')
<div class="bg-white border rounded p-6">
    <form method="POST" action="{{ route('admin.tasks.store') }}" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Tipo de tarea -->
            <div>
                <label for="type" class="block text-sm font-medium text-emerald-800 mb-2">Tipo de Tarea *</label>
                <select name="type" id="type" class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                    <option value="">Seleccionar tipo</option>
                    <option value="siembra" {{ old('type') === 'siembra' ? 'selected' : '' }}>Siembra</option>
                    <option value="riego" {{ old('type') === 'riego' ? 'selected' : '' }}>Riego</option>
                    <option value="fertilizacion" {{ old('type') === 'fertilizacion' ? 'selected' : '' }}>Fertilización</option>
                    <option value="cosecha" {{ old('type') === 'cosecha' ? 'selected' : '' }}>Cosecha</option>
                    <option value="mantenimiento" {{ old('type') === 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                    <option value="limpieza" {{ old('type') === 'limpieza' ? 'selected' : '' }}>Limpieza</option>
                    <option value="otro" {{ old('type') === 'otro' ? 'selected' : '' }}>Otro</option>
                </select>
                @error('type')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Trabajador asignado -->
            <div>
                <label for="assigned_to" class="block text-sm font-medium text-emerald-800 mb-2">
                    Trabajador Asignado *
                    <button type="button" id="refresh-workers" class="ml-2 text-emerald-600 hover:text-emerald-800" title="Actualizar lista de trabajadores">
                        <i data-lucide="refresh-cw" class="w-4 h-4 inline"></i>
                    </button>
                </label>
                <select name="assigned_to" id="assigned_to" class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                    <option value="">Seleccionar trabajador</option>
                    @foreach($workers as $worker)
                        <option value="{{ $worker->id }}" {{ old('assigned_to') == $worker->id ? 'selected' : '' }}>
                            {{ $worker->name }}
                        </option>
                    @endforeach
                </select>
                <div id="workers-loading" class="hidden text-sm text-emerald-600 mt-1">
                    <i data-lucide="loader-2" class="w-4 h-4 inline animate-spin mr-1"></i>
                    Actualizando lista de trabajadores...
                </div>
                @error('assigned_to')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Descripción -->
        <div>
            <label for="description" class="block text-sm font-medium text-emerald-800 mb-2">Descripción de la Tarea *</label>
            <textarea name="description" id="description" rows="3" class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Describe detalladamente la tarea a realizar..." required>{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Lote -->
            <div>
                <label for="plot_id" class="block text-sm font-medium text-emerald-800 mb-2">Lote (Opcional)</label>
                <select name="plot_id" id="plot_id" class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Seleccionar lote</option>
                    @foreach($plots as $plot)
                        <option value="{{ $plot->id }}" {{ old('plot_id') == $plot->id ? 'selected' : '' }}>
                            {{ $plot->name }} ({{ $plot->location }})
                        </option>
                    @endforeach
                </select>
                @error('plot_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Cultivo -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="crop_id" class="block text-sm font-medium text-emerald-800">Cultivo (Opcional)</label>
                    <button type="button" id="refreshCropsBtn" class="text-emerald-600 hover:text-emerald-800 text-sm flex items-center gap-1" title="Actualizar cultivos">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                        Actualizar
                    </button>
                </div>
                <div class="relative">
                    <select name="crop_id" id="crop_id" class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Seleccionar cultivo</option>
                        @foreach($crops as $crop)
                            <option value="{{ $crop->id }}" {{ old('crop_id') == $crop->id ? 'selected' : '' }}>
                                {{ $crop->name }}
                            </option>
                        @endforeach
                    </select>
                    <div id="crops-loading" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                        <i data-lucide="loader-2" class="w-4 h-4 animate-spin text-emerald-600"></i>
                    </div>
                </div>
                @error('crop_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Fecha programada -->
        <div>
            <label for="scheduled_for" class="block text-sm font-medium text-emerald-800 mb-2">Fecha Programada *</label>
            <input type="date" name="scheduled_for" id="scheduled_for" value="{{ old('scheduled_for', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            @error('scheduled_for')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tipo de pago -->
        <div>
            <label class="block text-sm font-medium text-emerald-800 mb-2">Forma de Pago *</label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="radio" name="payment_type" value="hours" {{ old('payment_type') === 'hours' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                    <span class="text-sm text-emerald-800">Por Horas</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="radio" name="payment_type" value="days" {{ old('payment_type') === 'days' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                    <span class="text-sm text-emerald-800">Por Días</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="radio" name="payment_type" value="quantity" {{ old('payment_type') === 'quantity' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                    <span class="text-sm text-emerald-800">Por Cantidad</span>
                </label>
            </div>
            @error('payment_type')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Campos de pago dinámicos -->
        <div id="payment-fields" class="space-y-4">
            <!-- Horas -->
            <div id="hours-field" class="hidden">
                <label for="hours" class="block text-sm font-medium text-emerald-800 mb-2">Horas Estimadas</label>
                <input type="number" name="hours" id="hours" value="{{ old('hours') }}" step="0.5" min="0" class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Ej: 8.5">
                @error('hours')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Días -->
            <div id="days-field" class="hidden">
                <label for="days" class="block text-sm font-medium text-emerald-800 mb-2">Días Estimados</label>
                <input type="number" name="days" id="days" value="{{ old('days') }}" min="1" class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Ej: 3">
                @error('days')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Cantidad -->
            <div id="quantity-field" class="hidden">
                <label for="kilos" class="block text-sm font-medium text-emerald-800 mb-2">Cantidad (kg)</label>
                <input type="number" name="kilos" id="kilos" value="{{ old('kilos') }}" step="0.1" min="0" class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Ej: 50.5">
                @error('kilos')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Botones -->
        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded inline-flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Asignar Tarea</span>
            </button>
            <a href="{{ route('admin.tasks.index') }}" class="px-6 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 inline-flex items-center gap-2">
                <i data-lucide="x" class="w-4 h-4"></i>
                <span>Cancelar</span>
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentTypeRadios = document.querySelectorAll('input[name="payment_type"]');
    const hoursField = document.getElementById('hours-field');
    const daysField = document.getElementById('days-field');
    const quantityField = document.getElementById('quantity-field');
    const refreshWorkersBtn = document.getElementById('refresh-workers');
    const workersSelect = document.getElementById('assigned_to');
    const workersLoading = document.getElementById('workers-loading');

    function togglePaymentFields() {
        const selectedType = document.querySelector('input[name="payment_type"]:checked')?.value;
        
        // Ocultar todos los campos
        hoursField.classList.add('hidden');
        daysField.classList.add('hidden');
        quantityField.classList.add('hidden');
        
        // Mostrar el campo correspondiente
        if (selectedType === 'hours') {
            hoursField.classList.remove('hidden');
        } else if (selectedType === 'days') {
            daysField.classList.remove('hidden');
        } else if (selectedType === 'quantity') {
            quantityField.classList.remove('hidden');
        }
    }

    // Función para actualizar trabajadores en tiempo real
    async function refreshWorkers() {
        const currentValue = workersSelect.value;
        
        try {
            workersLoading.classList.remove('hidden');
            refreshWorkersBtn.disabled = true;
            
            const response = await fetch('{{ route("admin.tasks.workers.list") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });
            
            if (!response.ok) {
                throw new Error('Error al cargar trabajadores');
            }
            
            const data = await response.json();
            
            if (data.success) {
                // Limpiar opciones existentes excepto la primera
                workersSelect.innerHTML = '<option value="">Seleccionar trabajador</option>';
                
                // Agregar trabajadores actualizados
                data.workers.forEach(worker => {
                    const option = document.createElement('option');
                    option.value = worker.id;
                    option.textContent = worker.name;
                    
                    // Restaurar selección anterior si existe
                    if (currentValue == worker.id) {
                        option.selected = true;
                    }
                    
                    workersSelect.appendChild(option);
                });
                
                // Mostrar mensaje de éxito temporal
                showNotification('Lista de trabajadores actualizada correctamente', 'success');
            } else {
                throw new Error('Error en la respuesta del servidor');
            }
        } catch (error) {
            console.error('Error al actualizar trabajadores:', error);
            showNotification('Error al actualizar la lista de trabajadores', 'error');
        } finally {
            workersLoading.classList.add('hidden');
            refreshWorkersBtn.disabled = false;
        }
    }

    // Función para mostrar notificaciones
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-4 py-2 rounded shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Agregar event listeners
    paymentTypeRadios.forEach(radio => {
        radio.addEventListener('change', togglePaymentFields);
    });

    if (refreshWorkersBtn) {
        refreshWorkersBtn.addEventListener('click', refreshWorkers);
    }

    // Actualizar trabajadores automáticamente cada 30 segundos
    setInterval(refreshWorkers, 30000);

    // Funcionalidad para cultivos
    const cropsSelect = document.getElementById('crop_id');
    const refreshCropsBtn = document.getElementById('refreshCropsBtn');
    const cropsLoading = document.getElementById('crops-loading');

    // Cargar cultivos dinámicamente
    async function loadCrops() {
        const currentValue = cropsSelect.value;
        cropsLoading.classList.remove('hidden');
        refreshCropsBtn.disabled = true;

        try {
            const response = await fetch('{{ route("admin.tasks.crops.list") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });
            
            if (!response.ok) {
                throw new Error('Error al cargar cultivos');
            }
            
            const data = await response.json();
            
            if (data.success) {
                // Limpiar opciones existentes excepto la primera
                cropsSelect.innerHTML = '<option value="">Seleccionar cultivo</option>';
                
                // Agregar cultivos actualizados
                data.crops.forEach(crop => {
                    const option = document.createElement('option');
                    option.value = crop.id;
                    option.textContent = crop.name;
                    
                    // Restaurar selección anterior si existe
                    if (currentValue == crop.id) {
                        option.selected = true;
                    }
                    
                    cropsSelect.appendChild(option);
                });
                
                // Mostrar mensaje de éxito temporal
                showNotification('Lista de cultivos actualizada correctamente', 'success');
            } else {
                throw new Error('Error en la respuesta del servidor');
            }
        } catch (error) {
            console.error('Error al actualizar cultivos:', error);
            showNotification('Error al actualizar la lista de cultivos', 'error');
        } finally {
            cropsLoading.classList.add('hidden');
            refreshCropsBtn.disabled = false;
        }
    }

    // Event listener para el botón de actualizar cultivos
    if (refreshCropsBtn) {
        refreshCropsBtn.addEventListener('click', loadCrops);
    }

    // Cargar cultivos al cargar la página
    loadCrops();

    // Actualizar cultivos cada 30 segundos
    setInterval(loadCrops, 30000);

    // Ejecutar al cargar la página
    togglePaymentFields();
});
</script>
@endsection
