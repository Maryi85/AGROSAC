@extends('admin.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Editar Tarea</h2>
</div>
@endsection

@section('content')
<div class="bg-white border rounded p-6">
    <form method="POST" action="{{ route('admin.tasks.update', $task) }}" class="space-y-6">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">¡Ups! Algo salió mal.</strong>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Tipo de tarea -->
            <div>
                <label for="type" class="block text-sm font-medium text-emerald-800 mb-2">Tipo de Tarea *</label>
                <select name="type" id="type" class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                    <option value="">Seleccionar tipo</option>
                    <option value="siembra" {{ old('type', $task->type) === 'siembra' ? 'selected' : '' }}>Siembra</option>
                    <option value="riego" {{ old('type', $task->type) === 'riego' ? 'selected' : '' }}>Riego</option>
                    <option value="fertilizacion" {{ old('type', $task->type) === 'fertilizacion' ? 'selected' : '' }}>Fertilización</option>
                    <option value="cosecha" {{ old('type', $task->type) === 'cosecha' ? 'selected' : '' }}>Cosecha</option>
                    <option value="mantenimiento" {{ old('type', $task->type) === 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                    <option value="limpieza" {{ old('type', $task->type) === 'limpieza' ? 'selected' : '' }}>Limpieza</option>
                    <option value="otro" {{ old('type', $task->type) === 'otro' ? 'selected' : '' }}>Otro</option>
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
                        <option value="{{ $worker->id }}" {{ old('assigned_to', $task->assigned_to) == $worker->id ? 'selected' : '' }}>
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
            <textarea name="description" id="description" rows="3" class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Describe detalladamente la tarea a realizar..." required>{{ old('description', $task->description) }}</textarea>
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
                        <option value="{{ $plot->id }}" {{ old('plot_id', $task->plot_id) == $plot->id ? 'selected' : '' }}>
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
                            <option value="{{ $crop->id }}" data-plot-id="{{ $crop->plot_id }}" {{ old('crop_id', $task->crop_id) == $crop->id ? 'selected' : '' }}>
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
            <input type="date" name="scheduled_for" id="scheduled_for" value="{{ old('scheduled_for', $task->scheduled_for?->format('Y-m-d')) }}" class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            @error('scheduled_for')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tipo de pago -->
        <div>
            <label class="block text-sm font-medium text-emerald-800 mb-2">Forma de Pago *</label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @php
                    $currentPaymentType = 'hours';
                    if ($task->kilos > 0) {
                        $currentPaymentType = 'quantity';
                    } elseif ($task->hours > 0 && $task->hours % 8 === 0) {
                        $currentPaymentType = 'days';
                    }
                @endphp
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="radio" name="payment_type" value="hours" {{ old('payment_type', $currentPaymentType) === 'hours' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                    <span class="text-sm text-emerald-800">Por Horas</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="radio" name="payment_type" value="days" {{ old('payment_type', $currentPaymentType) === 'days' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                    <span class="text-sm text-emerald-800">Por Días</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="radio" name="payment_type" value="quantity" {{ old('payment_type', $currentPaymentType) === 'quantity' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
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
            <div id="hours-field" class="hidden grid grid-cols-2 gap-4">
                <div>
                    <label for="hours" class="block text-sm font-medium text-emerald-800 mb-2">Horas Estimadas</label>
                    <input type="number" name="hours" id="hours" value="{{ old('hours', $task->hours) }}" step="1" min="0" class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Ej: 8.5">
                    @error('hours')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="price_per_hour" class="block text-sm font-medium text-emerald-800 mb-2">Precio por Hora ($)</label>
                    <input type="number" name="price_per_hour" id="price_per_hour" value="{{ old('price_per_hour', (int)$task->price_per_hour) }}" step="1" min="0" class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Ej: 5000">
                    @error('price_per_hour')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Días -->
            <div id="days-field" class="hidden grid grid-cols-2 gap-4">
                <div>
                    <label for="days" class="block text-sm font-medium text-emerald-800 mb-2">Días Estimados</label>
                    <input type="number" name="days" id="days" value="{{ old('days', $task->hours > 0 ? $task->hours / 8 : 1) }}" min="1" class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Ej: 3">
                    @error('days')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="price_per_day" class="block text-sm font-medium text-emerald-800 mb-2">Precio por Día ($)</label>
                    <input type="number" name="price_per_day" id="price_per_day" value="{{ old('price_per_day', (int)$task->price_per_day) }}" step="1" min="0" class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Ej: 40000">
                    @error('price_per_day')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Cantidad -->
            <div id="quantity-field" class="hidden grid grid-cols-2 gap-4">
                <div>
                    <label for="kilos" class="block text-sm font-medium text-emerald-800 mb-2">Cantidad (kg)</label>
                    <input type="number" name="kilos" id="kilos" value="{{ old('kilos', $task->kilos) }}" step="0.1" min="0" class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Ej: 50.5">
                    @error('kilos')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="price_per_kg" class="block text-sm font-medium text-emerald-800 mb-2">Precio por kg ($)</label>
                    <input type="number" name="price_per_kg" id="price_per_kg" value="{{ old('price_per_kg', (int)$task->price_per_kg) }}" step="1" min="0" class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Ej: 500">
                    @error('price_per_kg')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Estado actual -->
        <div class="bg-gray-50 p-4 rounded">
            <h3 class="text-sm font-medium text-gray-800 mb-2">Estado Actual</h3>
            <div class="flex items-center gap-2">
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-700',
                        'in_progress' => 'bg-blue-100 text-blue-700',
                        'completed' => 'bg-green-100 text-green-700',
                        'approved' => 'bg-emerald-100 text-emerald-700',
                        'rejected' => 'bg-red-100 text-red-700',
                        'invalid' => 'bg-gray-100 text-gray-700',
                    ];
                    $statusLabels = [
                        'pending' => 'Pendiente',
                        'in_progress' => 'En Progreso',
                        'completed' => 'Completada',
                        'approved' => 'Aprobada',
                        'rejected' => 'Rechazada',
                        'invalid' => 'Inválida',
                    ];
                @endphp
                <span class="px-2 py-1 text-xs rounded {{ $statusColors[$task->status] ?? 'bg-gray-100 text-gray-700' }}">
                    {{ $statusLabels[$task->status] ?? ucfirst($task->status) }}
                </span>
                @if($task->approved_at)
                    <span class="text-xs text-gray-600">
                        (Aprobada el {{ $task->approved_at->format('d/m/Y H:i') }})
                    </span>
                @endif
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
                <span>Actualizar Tarea</span>
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
        
        // Ocultar todos los div
        hoursField.classList.add('hidden');
        daysField.classList.add('hidden');
        quantityField.classList.add('hidden');
        
        // Deshabilitar TODOS los inputs primero para que no se envíen
        const allInputs = document.querySelectorAll('#hours-field input, #days-field input, #quantity-field input');
        allInputs.forEach(input => input.disabled = true);
        
        // Mostrar y habilitar el campo correspondiente
        if (selectedType === 'hours') {
            hoursField.classList.remove('hidden');
            hoursField.querySelectorAll('input').forEach(input => input.disabled = false);
        } else if (selectedType === 'days') {
            daysField.classList.remove('hidden');
            daysField.querySelectorAll('input').forEach(input => input.disabled = false);
        } else if (selectedType === 'quantity') {
            quantityField.classList.remove('hidden');
            quantityField.querySelectorAll('input').forEach(input => input.disabled = false);
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

    if (refreshWorkersBtn) {
        refreshWorkersBtn.addEventListener('click', refreshWorkers);
    }

    // Actualizar trabajadores automáticamente cada 30 segundos
    // setInterval(refreshWorkers, 30000);

    // Funcionalidad para cultivos
    const cropsSelect = document.getElementById('crop_id');
    const plotSelect = document.getElementById('plot_id');
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
                    
                    if (crop.plot_id) {
                        option.setAttribute('data-plot-id', crop.plot_id);
                    }
                    
                    cropsSelect.appendChild(option);
                });
                
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

    // Cargar cultivos al cargar la página
    loadCrops();

    if (cropsSelect && plotSelect) {
        cropsSelect.addEventListener('change', function() {
            const selectedCrop = this.options[this.selectedIndex];
            const plotId = selectedCrop.getAttribute('data-plot-id');
            if (plotId) plotSelect.value = plotId;
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
    }

    // Actualizar cultivos cada 30 segundos
    // setInterval(loadCrops, 30000);

    // Función para calcular el total del pago
    function calculateTotal() {
        // Obtener valores de los inputs
        const hours = parseFloat(document.getElementById('hours')?.value || 0);
        const pricePerHour = parseFloat(document.getElementById('price_per_hour')?.value || 0);
        
        const days = parseFloat(document.getElementById('days')?.value || 0);
        const pricePerDay = parseFloat(document.getElementById('price_per_day')?.value || 0);
        
        const kilos = parseFloat(document.getElementById('kilos')?.value || 0);
        const pricePerKg = parseFloat(document.getElementById('price_per_kg')?.value || 0);
        
        const selectedType = document.querySelector('input[name="payment_type"]:checked')?.value;
        let total = 0;

        if (selectedType === 'hours') {
            total = hours * pricePerHour;
        } else if (selectedType === 'days') {
            total = days * pricePerDay;
        } else if (selectedType === 'quantity') {
            total = kilos * pricePerKg;
        }
        
        // Actualizar visualización (si existiera un campo de total visual, pero por ahora solo aseguramos que los inputs existan)
        // Como no agregamos un campo visual de total en el HTML, esto es principalmente para asegurar que la lógica de cálculo esté lista si se necesita
    }

    // Agregar listeners para calcular el total cuando cambien los valores
    const inputs = ['hours', 'price_per_hour', 'days', 'price_per_day', 'kilos', 'price_per_kg'];
    inputs.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', calculateTotal);
    });

    // Ejecutar al cargar la página
    togglePaymentFields();
    calculateTotal();
});
</script>

@push('scripts')
<script>
function suppliesRepeater() {
    return {
        rows: [],
        init() {
            const oldSuppliesObj = @json(old('supplies_data', []));
            const oldSupplies = Array.isArray(oldSuppliesObj) ? oldSuppliesObj : Object.values(oldSuppliesObj);
            
            const taskSuppliesObj = @json($task->supplies_data ?? []);
            const taskSupplies = Array.isArray(taskSuppliesObj) ? taskSuppliesObj : Object.values(taskSuppliesObj);
            
            const hasOldData = "{{ old('type') }}" !== "";
            const source = hasOldData ? oldSupplies : taskSupplies;
            
            if (source.length > 0) {
                this.rows = source.map(item => ({
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
            const supply = window.editSuppliesMap[supplyId];
            return supply ? supply.unit : '';
        }
    };
}
window.editSuppliesMap = {};
@foreach($supplies as $supply)
    window.editSuppliesMap[{{ $supply->id }}] = { unit: '{{ $supply->unit }}', name: '{{ addslashes($supply->name) }}' };
@endforeach
</script>
@endpush
@endsection
