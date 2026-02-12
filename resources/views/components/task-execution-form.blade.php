{{-- 
    SNIPPET: Frontend para Ejecución de Tareas con Validación Reactiva
    Usar este código en el modal o formulario donde el trabajador reporta su ejecución
--}}

<div x-data="{
    status: '{{ old('status', $taskExecution->status ?? 'pending') }}',
    hoursWorked: {{ old('hours_worked', $taskExecution->hours_worked ?? 0) }},
    amountCollected: {{ old('amount_collected', $taskExecution->amount_collected ?? 0) }},
    paymentType: '{{ $task->payment_type }}',
    
    get canComplete() {
        if (this.status !== 'completed') return true;
        
        if (this.paymentType === 'por_horas') {
            return this.hoursWorked > 0;
        }
        
        if (this.paymentType === 'por_cantidad') {
            return this.amountCollected > 0;
        }
        
        return true;
    },
    
    get showHoursError() {
        return this.status === 'completed' && this.paymentType === 'por_horas' && this.hoursWorked <= 0;
    },
    
    get showAmountError() {
        return this.status === 'completed' && this.paymentType === 'por_cantidad' && this.amountCollected <= 0;
    }
}" class="space-y-4">

    {{-- Estado de la Tarea --}}
    <div>
        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
            Estado de la Tarea
        </label>
        <select 
            id="status" 
            name="status" 
            x-model="status"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
            required
        >
            <option value="pending">Pendiente</option>
            <option value="in_progress">En Progreso</option>
            <option value="completed">Completada</option>
        </select>
    </div>

    {{-- Horas Trabajadas (solo para tareas por_horas) --}}
    <div x-show="paymentType === 'por_horas'" x-cloak>
        <label for="hours_worked" class="block text-sm font-medium text-gray-700 mb-2">
            Horas Trabajadas
            <span x-show="status === 'completed'" class="text-red-500">*</span>
        </label>
        <input 
            type="number" 
            id="hours_worked" 
            name="hours_worked" 
            x-model.number="hoursWorked"
            step="0.1"
            min="0"
            max="24"
            placeholder="Ej: 8.5"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
            :class="{ 'border-red-500': showHoursError }"
        >
        
        {{-- Mensaje de Error --}}
        <p x-show="showHoursError" x-cloak class="mt-1 text-sm text-red-600 flex items-center gap-1">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            Debes registrar un valor mayor a 0 para finalizar
        </p>
        
        @error('hours_worked')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Cantidad Recolectada (solo para tareas por_cantidad) --}}
    <div x-show="paymentType === 'por_cantidad'" x-cloak>
        <label for="amount_collected" class="block text-sm font-medium text-gray-700 mb-2">
            Cantidad Recolectada (kg)
            <span x-show="status === 'completed'" class="text-red-500">*</span>
        </label>
        <input 
            type="number" 
            id="amount_collected" 
            name="amount_collected" 
            x-model.number="amountCollected"
            step="0.1"
            min="0"
            placeholder="Ej: 150.5"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
            :class="{ 'border-red-500': showAmountError }"
        >
        
        {{-- Mensaje de Error --}}
        <p x-show="showAmountError" x-cloak class="mt-1 text-sm text-red-600 flex items-center gap-1">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            Debes registrar un valor mayor a 0 para finalizar
        </p>
        
        @error('amount_collected')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Notas Adicionales --}}
    <div>
        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
            Notas Adicionales (Opcional)
        </label>
        <textarea 
            id="notes" 
            name="notes" 
            rows="3"
            placeholder="Agrega observaciones o comentarios sobre la ejecución..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
        >{{ old('notes', $taskExecution->notes ?? '') }}</textarea>
        
        @error('notes')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Botones de Acción --}}
    <div class="flex justify-end gap-3 pt-4 border-t">
        <button 
            type="button" 
            onclick="window.history.back()"
            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
        >
            Cancelar
        </button>
        
        <button 
            type="submit"
            :disabled="!canComplete"
            :class="{
                'opacity-50 cursor-not-allowed': !canComplete,
                'hover:bg-green-600': canComplete
            }"
            class="px-6 py-2 bg-green-500 text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <span x-show="status === 'completed'">Finalizar Tarea</span>
            <span x-show="status !== 'completed'">Guardar Cambios</span>
        </button>
    </div>

</div>

<style>
    [x-cloak] { display: none !important; }
</style>
