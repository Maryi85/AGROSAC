@extends('foreman.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Editar Tarea</h2>
    <a href="{{ route('foreman.tasks.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
        Volver
    </a>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white border rounded p-6">
        <form method="POST" action="{{ route('foreman.tasks.update', $task) }}" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tipo de Tarea -->
                <div>
                    <label for="type" class="block text-sm font-medium text-emerald-700 mb-2">Tipo de Tarea *</label>
                    <select name="type" id="type" required 
                            class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('type') border-red-500 @enderror">
                        <option value="">Seleccionar tipo</option>
                        @foreach($taskTypes as $type)
                            <option value="{{ $type }}" {{ (old('type') ?? $task->type) == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Trabajador Asignado -->
                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-emerald-700 mb-2">
                        Trabajador Asignado *
                        <button type="button" id="refresh-workers" class="ml-2 text-emerald-600 hover:text-emerald-800" title="Actualizar lista de trabajadores">
                            <i data-lucide="refresh-cw" class="w-4 h-4 inline"></i>
                        </button>
                    </label>
                    <select name="assigned_to" id="assigned_to" required 
                            class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('assigned_to') border-red-500 @enderror">
                        <option value="">Seleccionar trabajador</option>
                        @foreach($workers as $worker)
                            <option value="{{ $worker->id }}" {{ (old('assigned_to') ?? $task->assigned_to) == $worker->id ? 'selected' : '' }}>
                                {{ $worker->name }}
                            </option>
                        @endforeach
                    </select>
                    <div id="workers-loading" class="hidden text-sm text-emerald-600 mt-1">
                        <i data-lucide="loader-2" class="w-4 h-4 inline animate-spin mr-1"></i>
                        Actualizando lista de trabajadores...
                    </div>
                    @error('assigned_to')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lote -->
                <div>
                    <label for="plot_id" class="block text-sm font-medium text-emerald-700 mb-2">Lote</label>
                    <select name="plot_id" id="plot_id" 
                            class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('plot_id') border-red-500 @enderror">
                        <option value="">Seleccionar lote (opcional)</option>
                        @foreach($plots as $plot)
                            <option value="{{ $plot->id }}" {{ (old('plot_id') ?? $task->plot_id) == $plot->id ? 'selected' : '' }}>
                                {{ $plot->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('plot_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Cultivo -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="crop_id" class="block text-sm font-medium text-emerald-700">Cultivo</label>
                        <button type="button" id="refreshCropsBtn" class="text-emerald-600 hover:text-emerald-800 text-sm flex items-center gap-1" title="Actualizar cultivos">
                            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                            Actualizar
                        </button>
                    </div>
                    <div class="relative">
                        <select name="crop_id" id="crop_id" 
                                class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('crop_id') border-red-500 @enderror">
                            <option value="">Seleccionar cultivo (opcional)</option>
                            @foreach($crops as $crop)
                                <option value="{{ $crop->id }}" {{ (old('crop_id') ?? $task->crop_id) == $crop->id ? 'selected' : '' }}>
                                    {{ $crop->name }}
                                </option>
                            @endforeach
                        </select>
                        <div id="crops-loading" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                            <i data-lucide="loader-2" class="w-4 h-4 animate-spin text-emerald-600"></i>
                        </div>
                    </div>
                    @error('crop_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fecha Programada -->
                <div>
                    <label for="scheduled_for" class="block text-sm font-medium text-emerald-700 mb-2">Fecha Programada *</label>
                    <input type="date" name="scheduled_for" id="scheduled_for" required 
                           value="{{ old('scheduled_for', $task->scheduled_for?->format('Y-m-d')) }}"
                           class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('scheduled_for') border-red-500 @enderror">
                    @error('scheduled_for')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estado -->
                <div>
                    <label for="status" class="block text-sm font-medium text-emerald-700 mb-2">Estado</label>
                    <select name="status" id="status" 
                            class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('status') border-red-500 @enderror">
                        <option value="pending" {{ (old('status') ?? $task->status) == 'pending' ? 'selected' : '' }}>Pendiente</option>
                        <option value="in_progress" {{ (old('status') ?? $task->status) == 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                        <option value="completed" {{ (old('status') ?? $task->status) == 'completed' ? 'selected' : '' }}>Completada</option>
                        <option value="approved" {{ (old('status') ?? $task->status) == 'approved' ? 'selected' : '' }}>Aprobada</option>
                        <option value="rejected" {{ (old('status') ?? $task->status) == 'rejected' ? 'selected' : '' }}>Rechazada</option>
                        <option value="invalid" {{ (old('status') ?? $task->status) == 'invalid' ? 'selected' : '' }}>Inválida</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Descripción -->
            <div>
                <label for="description" class="block text-sm font-medium text-emerald-700 mb-2">Descripción de la Tarea *</label>
                <textarea name="description" id="description" rows="4" required 
                          placeholder="Describe detalladamente la tarea a realizar..."
                          class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('description') border-red-500 @enderror">{{ old('description', $task->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Información Adicional -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Horas Estimadas -->
                <div>
                    <label for="hours" class="block text-sm font-medium text-emerald-700 mb-2">Horas Estimadas</label>
                    <input type="number" name="hours" id="hours" step="0.5" min="0" max="24" 
                           value="{{ old('hours', $task->hours) }}"
                           placeholder="Ej: 2.5"
                           class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('hours') border-red-500 @enderror">
                    @error('hours')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kilos Estimados -->
                <div>
                    <label for="kilos" class="block text-sm font-medium text-emerald-700 mb-2">Kilos Estimados</label>
                    <input type="number" name="kilos" id="kilos" step="0.1" min="0" 
                           value="{{ old('kilos', $task->kilos) }}"
                           placeholder="Ej: 50.5"
                           class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('kilos') border-red-500 @enderror">
                    @error('kilos')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('foreman.tasks.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="bg-emerald-600 text-white px-6 py-2 rounded hover:bg-emerald-700 transition-colors">
                    <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                    Actualizar Tarea
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const refreshWorkersBtn = document.getElementById('refresh-workers');
    const workersSelect = document.getElementById('assigned_to');
    const workersLoading = document.getElementById('workers-loading');

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
                cropsSelect.innerHTML = '<option value="">Seleccionar cultivo (opcional)</option>';
                
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
});
</script>
@endsection
