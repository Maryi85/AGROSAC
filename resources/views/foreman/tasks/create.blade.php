@extends('foreman.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Asignar Nueva Tarea</h2>
</div>
@endsection

@section('content')
<div class="bg-white border rounded p-6">
    <form method="POST" action="{{ route('foreman.tasks.store') }}" class="space-y-6">
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
                            <option value="{{ $crop->id }}" 
                                    data-plot-id="{{ $crop->plot_id }}"
                                    {{ old('crop_id') == $crop->id ? 'selected' : '' }}>
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Campo de cantidad (horas, días o kg) -->
                <div id="quantity-field-wrapper" class="hidden">
                    <label id="quantity-label" class="block text-sm font-medium text-emerald-800 mb-2"></label>
                    <input type="number" id="quantity-input" step="0.1" min="0" class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="">
                    @error('hours')
                        <p class="text-red-600 text-sm mt-1" id="hours-error">{{ $message }}</p>
                    @enderror
                    @error('days')
                        <p class="text-red-600 text-sm mt-1" id="days-error">{{ $message }}</p>
                    @enderror
                    @error('kilos')
                        <p class="text-red-600 text-sm mt-1" id="kilos-error">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Campo de precio -->
                <div id="price-field-wrapper" class="hidden">
                    <label id="price-label" class="block text-sm font-medium text-emerald-800 mb-2"></label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">$</span>
                        <input type="number" id="price-input" step="1" min="0" class="w-full border border-emerald-200 rounded px-3 py-2 pl-8 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Ej: 5000">
                    </div>
                    @error('price_per_hour')
                        <p class="text-red-600 text-sm mt-1" id="price_per_hour-error">{{ $message }}</p>
                    @enderror
                    @error('price_per_day')
                        <p class="text-red-600 text-sm mt-1" id="price_per_day-error">{{ $message }}</p>
                    @enderror
                    @error('price_per_kg')
                        <p class="text-red-600 text-sm mt-1" id="price_per_kg-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Campos ocultos para enviar al servidor -->
            <input type="hidden" name="hours" id="hours" value="{{ old('hours') }}">
            <input type="hidden" name="days" id="days" value="{{ old('days') }}">
            <input type="hidden" name="kilos" id="kilos" value="{{ old('kilos') }}">
            <input type="hidden" name="price_per_hour" id="price_per_hour" value="{{ old('price_per_hour') }}">
            <input type="hidden" name="price_per_day" id="price_per_day" value="{{ old('price_per_day') }}">
            <input type="hidden" name="price_per_kg" id="price_per_kg" value="{{ old('price_per_kg') }}">
            
            <!-- Total del Pago -->
            <div class="bg-emerald-50 border border-emerald-200 rounded p-4">
                <label for="total_payment" class="block text-sm font-medium text-emerald-800 mb-2">Total del Pago</label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500 font-semibold">$</span>
                    <input type="text" id="total_payment_display" readonly value="$0.00" class="w-full border border-emerald-200 rounded px-3 py-2 pl-8 bg-white font-semibold text-emerald-700 text-lg">
                    <input type="hidden" name="total_payment" id="total_payment" value="0">
                </div>
                <p class="text-xs text-gray-500 mt-1">Calculado automáticamente según la cantidad y el precio establecido</p>
            </div>
        </div>

        <!-- Insumos Requeridos -->
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200" x-data="suppliesRepeater()">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-md font-medium text-emerald-800 flex items-center gap-2">
                    <i data-lucide="package" class="w-5 h-5"></i>
                    Insumos Requeridos
                </h3>
                <button type="button" @click="addSupply()" class="text-sm text-emerald-600 hover:text-emerald-800 flex items-center gap-1">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    Agregar Insumo
                </button>
            </div>

            <div class="space-y-3">
            <template x-for="(row, index) in rows" :key="row.id">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end bg-white p-3 rounded shadow-sm">
                        
                        <!-- Selección de Insumo -->
                        <div class="md:col-span-7">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Insumo</label>
                            <select :name="`supplies_data[${index}][supply_id]`" x-model="row.supply_id" class="w-full border border-gray-300 rounded px-2 py-1.5 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                                <option value="">Seleccionar...</option>
                                @foreach($supplies as $supply)
                                    <option value="{{ $supply->id }}" data-unit="{{ $supply->unit }}">
                                        {{ $supply->name }} (Min: {{ $supply->min_stock }} {{ $supply->unit }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Cantidad -->
                        <div class="md:col-span-4">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Cantidad</label>
                            <div class="relative">
                                <input type="number" :name="`supplies_data[${index}][quantity]`" x-model="row.quantity" step="1" min="0" class="w-full border border-gray-300 rounded px-2 py-1.5 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                                <span class="absolute right-8 top-1.5 text-xs text-gray-500" x-text="getUnit(row.supply_id)"></span>
                            </div>
                        </div>

                        <!-- Botón Eliminar -->
                        <div class="md:col-span-1 text-right pb-1">
                            <button type="button" @click="removeSupply(row.id)" class="text-red-500 hover:text-red-700 p-1" title="Quitar insumo">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" x2="10" y1="11" y2="17"></line><line x1="14" x2="14" y1="11" y2="17"></line></svg>
                            </button>
                        </div>
                    </div>
                </template>


                <div x-show="rows.length === 0" class="text-center py-4 text-gray-500 text-sm italic">
                    No hay insumos asignados a esta tarea.
                </div>
            </div>
            
            @if($errors->has('supplies_data'))
                <div class="mt-2 text-red-600 text-sm">{{ $errors->first('supplies_data') }}</div>
            @endif
        </div>



        <!-- Botones -->
        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="px-6 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded inline-flex items-center gap-2 transition-colors">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Asignar Tarea</span>
            </button>
            <a href="{{ route('foreman.tasks.index') }}" class="px-6 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 inline-flex items-center gap-2">
                <i data-lucide="x" class="w-4 h-4"></i>
                <span>Cancelar</span>
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentTypeRadios = document.querySelectorAll('input[name="payment_type"]');
    const refreshWorkersBtn = document.getElementById('refresh-workers');
    const workersSelect = document.getElementById('assigned_to');
    const workersLoading = document.getElementById('workers-loading');

    const quantityFieldWrapper = document.getElementById('quantity-field-wrapper');
    const priceFieldWrapper = document.getElementById('price-field-wrapper');
    const quantityInput = document.getElementById('quantity-input');
    const priceInput = document.getElementById('price-input');
    const quantityLabel = document.getElementById('quantity-label');
    const priceLabel = document.getElementById('price-label');
    
    // Variable global para almacenar el tipo de pago antiguo
    const oldPaymentType = '{{ old("payment_type") }}';
    
    function togglePaymentFields() {
        const selectedType = document.querySelector('input[name="payment_type"]:checked')?.value;
        
        // Ocultar todos los campos
        quantityFieldWrapper.classList.add('hidden');
        priceFieldWrapper.classList.add('hidden');
        
        // Ocultar todos los mensajes de error
        const errorMessages = document.querySelectorAll('#quantity-field-wrapper p.text-red-600, #price-field-wrapper p.text-red-600');
        errorMessages.forEach(msg => msg.style.display = 'none');
        
        // Limpiar valores solo si no hay valores antiguos
        if (!oldPaymentType || oldPaymentType !== selectedType) {
            if (!oldPaymentType) { // Keep values if we are just switching back and forth in UI? No, requirement says clean.
                // But oldPaymentType logic suggests we only clean if it's a fresh change, not a re-render with validation errors.
                // The original code was correct in logic, I am just updating the min values in the else-if blocks below.
                quantityInput.value = '';
                priceInput.value = '';
            }
        }
        
        // Mostrar y configurar campos según el tipo de pago
        if (selectedType === 'hours') {
            quantityLabel.textContent = 'Horas Estimadas *';
            quantityInput.placeholder = 'Ej: 8.5';
            quantityInput.step = '0.01';
            quantityInput.min = '0.1';
            
            priceLabel.textContent = 'Precio por Hora ($) *';
            priceInput.placeholder = 'Ej: 5000';
            priceInput.min = '0'; // Update min
            
            // Mostrar errores relevantes
            const hoursError = document.getElementById('hours-error');
            const priceHourError = document.getElementById('price_per_hour-error');
            if (hoursError) hoursError.style.display = 'block';
            if (priceHourError) priceHourError.style.display = 'block';
            
            quantityFieldWrapper.classList.remove('hidden');
            priceFieldWrapper.classList.remove('hidden');
        } else if (selectedType === 'days') {
            quantityLabel.textContent = 'Días Estimados *';
            quantityInput.placeholder = 'Ej: 3';
            quantityInput.step = '1';
            quantityInput.min = '1';
            
            priceLabel.textContent = 'Precio por Día ($) *';
            priceInput.placeholder = 'Ej: 40000';
            priceInput.min = '0'; // Update min
            
            // Mostrar errores relevantes
            const daysError = document.getElementById('days-error');
            const priceDayError = document.getElementById('price_per_day-error');
            if (daysError) daysError.style.display = 'block';
            if (priceDayError) priceDayError.style.display = 'block';
            
            quantityFieldWrapper.classList.remove('hidden');
            priceFieldWrapper.classList.remove('hidden');
        } else if (selectedType === 'quantity') {
            quantityLabel.textContent = 'Cantidad (kg) *';
            quantityInput.placeholder = 'Ej: 50.5';
            quantityInput.step = '0.1';
            quantityInput.min = '0.1';
            
            priceLabel.textContent = 'Precio por kg ($) *';
            priceInput.placeholder = 'Ej: 500';
            priceInput.min = '0'; // Update min
            
            // Mostrar errores relevantes
            const kilosError = document.getElementById('kilos-error');
            const priceKgError = document.getElementById('price_per_kg-error');
            if (kilosError) kilosError.style.display = 'block';
            if (priceKgError) priceKgError.style.display = 'block';
            
            quantityFieldWrapper.classList.remove('hidden');
            priceFieldWrapper.classList.remove('hidden');
        }
        
        // Calcular el total cuando se cambia el tipo de pago
        calculateTotal();
    }
    
    // Función para calcular el total del pago
    function calculateTotal() {
        const selectedType = document.querySelector('input[name="payment_type"]:checked')?.value;
        const totalDisplay = document.getElementById('total_payment_display');
        const totalInput = document.getElementById('total_payment');
        let total = 0;
        let quantity = 0;
        let price = 0;
        
        if (selectedType) {
            quantity = parseFloat(quantityInput.value) || 0;
            price = parseFloat(priceInput.value) || 0;
            total = quantity * price;
        }
        
        // Formatear el total con 2 decimales y separador de miles
        const formattedTotal = new Intl.NumberFormat('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(total);
        
        // Actualizar el campo de visualización y el campo oculto
        totalDisplay.value = '$' + formattedTotal;
        totalInput.value = total.toFixed(0);
        
        // Actualizar campos ocultos para enviar al servidor
        updateHiddenFields();
    }
    
    // Función para actualizar los campos ocultos según el tipo de pago
    function updateHiddenFields() {
        const selectedType = document.querySelector('input[name="payment_type"]:checked')?.value;
        const quantity = parseFloat(quantityInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        
        // Limpiar todos los campos ocultos
        document.getElementById('hours').value = '';
        document.getElementById('days').value = '';
        document.getElementById('kilos').value = '';
        document.getElementById('price_per_hour').value = '';
        document.getElementById('price_per_day').value = '';
        document.getElementById('price_per_kg').value = '';
        
        // Asignar valores según el tipo de pago
        if (selectedType === 'hours') {
            document.getElementById('hours').value = quantity;
            document.getElementById('price_per_hour').value = price;
        } else if (selectedType === 'days') {
            document.getElementById('days').value = quantity;
            document.getElementById('price_per_day').value = price;
        } else if (selectedType === 'quantity') {
            document.getElementById('kilos').value = quantity;
            document.getElementById('price_per_kg').value = price;
        }
    }

    // Función para actualizar trabajadores en tiempo real
    async function refreshWorkers() {
        const currentValue = workersSelect.value;
        
        try {
            workersLoading.classList.remove('hidden');
            refreshWorkersBtn.disabled = true;
            
            const response = await fetch('{{ route("foreman.tasks.workers.list") }}', {
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
                // Notificación suprimida intencionalmente
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

    // Función para mostrar notificaciones usando SweetAlert2
    function showNotification(message, type = 'info') {
        if (window.showSuccessAlert && type === 'success') {
            window.showSuccessAlert(message);
        } else if (window.showErrorAlert && type === 'error') {
            window.showErrorAlert(message);
        } else if (window.showSuccessAlert) {
            window.showSuccessAlert(message);
        }
    }

    // Agregar event listeners
    paymentTypeRadios.forEach(radio => {
        radio.addEventListener('change', togglePaymentFields);
    });
    
    // Agregar listeners para calcular el total cuando cambien los valores
    if (quantityInput) quantityInput.addEventListener('input', calculateTotal);
    if (priceInput) priceInput.addEventListener('input', calculateTotal);

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
            const response = await fetch('{{ route("foreman.tasks.crops.list") }}', {
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
                    if (crop.plot_id) {
                        option.setAttribute('data-plot-id', crop.plot_id);
                    }
                    
                    // Restaurar selección anterior si existe
                    if (currentValue == crop.id) {
                        option.selected = true;
                    }
                    
                    cropsSelect.appendChild(option);
                });
                
                // Disparar evento change si había un valor seleccionado
                if (currentValue) {
                    cropsSelect.dispatchEvent(new Event('change'));
                }
                
                // Mostrar mensaje de éxito temporal
                // showNotification('Lista de cultivos actualizada correctamente', 'success');
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

    // Función para actualizar el lote cuando se selecciona un cultivo
    const plotSelect = document.getElementById('plot_id');
    
    cropsSelect.addEventListener('change', function() {
        const selectedCrop = this.options[this.selectedIndex];
        const plotId = selectedCrop.getAttribute('data-plot-id');
        
        if (!this.value) {
            plotSelect.value = '';
            return;
        }
        
        if (plotId) {
            plotSelect.value = plotId;
        } else {
            plotSelect.value = '';
        }
    });

    plotSelect.addEventListener('change', function() {
        const selectedPlotId = this.value;
        if (!selectedPlotId) return;

        const cropsForPlot = Array.from(cropsSelect.options).filter(opt => opt.getAttribute('data-plot-id') === selectedPlotId);
        
        if (cropsForPlot.length === 1) {
            cropsSelect.value = cropsForPlot[0].value;
        } else if (cropsForPlot.length > 1) {
            const currentCropPlotId = cropsSelect.options[cropsSelect.selectedIndex]?.getAttribute('data-plot-id');
            if (currentCropPlotId !== selectedPlotId) {
                cropsSelect.value = '';
            }
        }
    });
    
    // Si hay un valor antiguo, disparar el evento
    if (cropsSelect.value) {
        cropsSelect.dispatchEvent(new Event('change'));
    }

    // Cargar cultivos al cargar la página
    loadCrops();

    // Actualizar cultivos cada 30 segundos
    setInterval(loadCrops, 30000);

    // Ejecutar al cargar la página
    togglePaymentFields();
    
    // Si hay valores antiguos, restaurarlos
    if (oldPaymentType) {
        const radio = document.querySelector(`input[name="payment_type"][value="${oldPaymentType}"]`);
        if (radio) {
            radio.checked = true;
            togglePaymentFields();
            
            // Restaurar valores antiguos
            if (oldPaymentType === 'hours') {
                quantityInput.value = '{{ old("hours", "") }}';
                priceInput.value = '{{ old("price_per_hour", "") }}';
            } else if (oldPaymentType === 'days') {
                quantityInput.value = '{{ old("days", "") }}';
                priceInput.value = '{{ old("price_per_day", "") }}';
            } else if (oldPaymentType === 'quantity') {
                quantityInput.value = '{{ old("kilos", "") }}';
                priceInput.value = '{{ old("price_per_kg", "") }}';
            }
        }
    }
    
    // Calcular el total inicial si hay valores
    calculateTotal();
});
</script>

@push('scripts')
<script>
function suppliesRepeater() {
    return {
        rows: [],
        init() {
            const oldSupplies = @json(old('supplies_data', []));
            if (oldSupplies.length > 0) {
                this.rows = oldSupplies.map(item => ({
                    id: Date.now() + Math.random(),
                    supply_id: item.supply_id,
                    quantity: item.quantity
                }));
            }
        },
        addSupply() {
            this.rows.push({ id: Date.now(), supply_id: '', quantity: '' });
        },
        removeSupply(id) {
            this.rows = this.rows.filter(r => r.id !== id);
        },
        getUnit(supplyId) {
            if (!supplyId) return '';
            const supply = window.suppliesMap[supplyId];
            return supply ? supply.unit : '';
        }
    };
}
window.suppliesMap = {};
@foreach($supplies as $supply)
    window.suppliesMap[{{ $supply->id }}] = { unit: '{{ $supply->unit }}', name: '{{ addslashes($supply->name) }}' };
@endforeach
</script>
@endpush
@endsection

