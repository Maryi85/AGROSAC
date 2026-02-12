@extends('worker.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Mis Tareas</h2>
@endsection

@section('content')
<div class="space-y-4 pb-6">
    <!-- Modern Segmented Control Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-2">
        <div class="grid grid-cols-3 gap-2 bg-gray-50 p-1 rounded-lg">
            <button onclick="filterTasks('all')" 
                    class="filter-btn px-4 py-3 rounded-lg text-sm font-semibold transition-all duration-200 bg-emerald-600 text-white shadow-sm" 
                    data-filter="all">
                <div class="flex items-center justify-center gap-2">
                    <i data-lucide="list" class="w-4 h-4"></i>
                    <span>Todas</span>
                </div>
            </button>
            <button onclick="filterTasks('pending')" 
                    class="filter-btn px-4 py-3 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-600 hover:bg-gray-100" 
                    data-filter="pending">
                <div class="flex items-center justify-center gap-2">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                    <span>Pendientes</span>
                </div>
            </button>
            <button onclick="filterTasks('completed')" 
                    class="filter-btn px-4 py-3 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-600 hover:bg-gray-100" 
                    data-filter="completed">
                <div class="flex items-center justify-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span>Completadas</span>
                </div>
            </button>
        </div>
    </div>

    <!-- Tasks Cards Grid -->
    <div class="space-y-4">
        @forelse($tasks as $task)
            <!-- Modern Task Card -->
            <div class="task-item bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200" 
                 data-status="{{ $task->status }}">
                
                <!-- Card Header -->
                <div class="p-5 pb-4 border-b border-gray-100">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <h3 class="text-lg font-bold text-gray-900 leading-tight flex-1">
                            {{ $task->description }}
                        </h3>
                        
                        <!-- Status Badge -->
                        @if($task->status === 'completed')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-green-100 text-green-800 text-sm font-semibold whitespace-nowrap">
                                <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                                Completada
                            </span>
                        @elseif($task->status === 'pending')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-yellow-100 text-yellow-800 text-sm font-semibold whitespace-nowrap">
                                <i data-lucide="clock" class="w-4 h-4"></i>
                                Pendiente
                            </span>
                        @else
                            @php
                                $statusTranslations = [
                                    'pending' => 'Pendiente',
                                    'completed' => 'Completada',
                                    'approved' => 'Aprobada',
                                    'rejected' => 'Rechazada',
                                    'invalid' => 'Inválida',
                                    'in_progress' => 'En Progreso',
                                    'cancelled' => 'Cancelada',
                                ];
                                $statusText = $statusTranslations[$task->status] ?? ucfirst($task->status);
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-gray-100 text-gray-800 text-sm font-semibold whitespace-nowrap">
                                <i data-lucide="circle" class="w-4 h-4"></i>
                                {{ $statusText }}
                            </span>
                        @endif
                    </div>
                    
                    <!-- Priority Badge (if high or medium) -->
                    @if($task->priority && in_array($task->priority, ['high', 'medium']))
                        <div class="mb-3">
                            @if($task->priority === 'high')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-red-50 text-red-700 text-xs font-semibold border border-red-200">
                                    <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                    Prioridad Alta
                                </span>
                            @elseif($task->priority === 'medium')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-orange-50 text-orange-700 text-xs font-semibold border border-orange-200">
                                    <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                                    Prioridad Media
                                </span>
                            @endif
                        </div>
                    @endif
                    
                    <!-- Info Grid -->
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="flex items-center gap-2 text-gray-700">
                            <i data-lucide="map-pin" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                            <span class="truncate">{{ $task->plot->name ?? 'Sin lote' }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-700">
                            <i data-lucide="sprout" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                            <span class="truncate">{{ $task->crop->name ?? 'Sin cultivo' }}</span>
                        </div>
                        @if($task->scheduled_for)
                            <div class="flex items-center gap-2 {{ $task->scheduled_for < now() && $task->status !== 'completed' ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                                <i data-lucide="calendar" class="w-4 h-4 {{ $task->scheduled_for < now() && $task->status !== 'completed' ? 'text-red-600' : 'text-emerald-600' }} flex-shrink-0"></i>
                                <span class="truncate">{{ $task->scheduled_for->format('d/m/Y') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Card Body (Notes & Completion Details) -->
                @if($task->notes || ($task->status === 'completed' && ($task->hours || $task->kilos)))
                    <div class="p-5 pt-4 space-y-3 bg-gray-50">
                        @if($task->notes)
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <div class="flex items-start gap-2">
                                    <i data-lucide="info" class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5"></i>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-blue-900 mb-1">Notas:</p>
                                        <p class="text-sm text-blue-800 leading-relaxed">{{ $task->notes }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($task->status === 'completed')
                            <div class="grid grid-cols-2 gap-2">
                                @if($task->hours)
                                    <div class="bg-white border border-green-200 rounded-lg p-3">
                                        <div class="flex items-center gap-2 mb-1">
                                            <i data-lucide="clock" class="w-4 h-4 text-green-600"></i>
                                            <span class="text-xs font-medium text-gray-600">Horas</span>
                                        </div>
                                        <p class="text-lg font-bold text-green-700">{{ $task->hours }}h</p>
                                    </div>
                                @endif
                                @if($task->kilos)
                                    <div class="bg-white border border-green-200 rounded-lg p-3">
                                        <div class="flex items-center gap-2 mb-1">
                                            <i data-lucide="weight" class="w-4 h-4 text-green-600"></i>
                                            <span class="text-xs font-medium text-gray-600">Recolectado</span>
                                        </div>
                                        <p class="text-lg font-bold text-green-700">{{ $task->kilos }} kg</p>
                                    </div>
                                @endif
                            </div>
                            @if($task->updated_at)
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                    <span>Completada el {{ $task->updated_at->format('d/m/Y') }} a las {{ $task->updated_at->format('H:i') }}</span>
                                </div>
                            @endif
                        @endif
                    </div>
                @endif

                <!-- Card Footer (Action Buttons) -->
                @if($task->status === 'pending')
                    <div class="p-5 pt-4 bg-gradient-to-br from-emerald-50 to-white border-t border-emerald-100">
                        <button onclick="openCompleteModal({{ $task->id }}, '{{ addslashes($task->description) }}', {{ $task->price_per_hour ?? 0 }}, {{ $task->price_per_day ?? 0 }}, {{ $task->price_per_kg ?? 0 }})" 
                                class="w-full flex items-center justify-center gap-2 px-6 py-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-xl font-bold text-base shadow-sm hover:shadow-md transition-all duration-200 touch-manipulation">
                            <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                            <span>Completar Tarea</span>
                        </button>
                    </div>
                @elseif($task->status === 'completed')
                    <div class="p-5 pt-4 bg-gray-50 border-t border-gray-100">
                        <div class="flex items-center justify-center gap-2 text-sm text-gray-500">
                            <i data-lucide="check-circle-2" class="w-4 h-4 text-green-600"></i>
                            <span>Tarea completada exitosamente</span>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="clipboard-check" class="w-8 h-8 text-emerald-600"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">No hay tareas asignadas</h3>
                <p class="text-gray-600">No tienes tareas pendientes o completadas en este momento.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($tasks->hasPages())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            {{ $tasks->links() }}
        </div>
    @endif
</div>

<!-- Complete Task Modal -->
<div id="completeModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50">
    <div class="flex items-end sm:items-center justify-center min-h-screen p-0 sm:p-4">
        <div class="bg-white rounded-t-3xl sm:rounded-2xl w-full sm:max-w-lg max-h-[90vh] overflow-y-auto" 
             x-data="{
                 hoursWorked: '',
                 quantityHarvested: '',
                 isHourlyTask: false,
                 isQuantityTask: false,
                 
                 get canComplete() {
                     if (this.isHourlyTask && (!this.hoursWorked || parseFloat(this.hoursWorked) <= 0)) {
                         return false;
                     }
                     if (this.isQuantityTask && (!this.quantityHarvested || parseFloat(this.quantityHarvested) <= 0)) {
                         return false;
                     }
                     return true;
                 },
                 
                 get showHoursError() {
                     return this.isHourlyTask && this.hoursWorked !== '' && parseFloat(this.hoursWorked) <= 0;
                 },
                 
                 get showQuantityError() {
                     return this.isQuantityTask && this.quantityHarvested !== '' && parseFloat(this.quantityHarvested) <= 0;
                 },
                 
                 setTaskType(isHourly, isQuantity) {
                     this.isHourlyTask = isHourly;
                     this.isQuantityTask = isQuantity;
                 },
                 
                 reset() {
                     this.hoursWorked = '';
                     this.quantityHarvested = '';
                     this.isHourlyTask = false;
                     this.isQuantityTask = false;
                 }
             }">
            
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 p-6 pb-4 rounded-t-3xl sm:rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-900">Completar Tarea</h3>
                    <button type="button" onclick="closeCompleteModal()" 
                            class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors touch-manipulation">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <form id="completeForm" method="POST" class="p-6">
                @csrf
                <div class="space-y-5">
                    <!-- Task Description -->
                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                        <label class="block text-xs font-semibold text-emerald-900 mb-2 uppercase tracking-wide">Tarea</label>
                        <p id="taskDescription" class="text-sm text-emerald-800 font-medium leading-relaxed"></p>
                    </div>
                    
                    <!-- Completion Notes -->
                    <div>
                        <label for="completion_notes" class="block text-sm font-semibold text-gray-900 mb-2">
                            Notas de completado
                        </label>
                        <textarea id="completion_notes" 
                                  name="completion_notes" 
                                  rows="4" 
                                  class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-base resize-none"
                                  placeholder="Describe el trabajo realizado..."></textarea>
                        @error('completion_notes')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <!-- Hours and Quantity Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Hours Worked -->
                        <div>
                            <label for="hours_worked" class="block text-sm font-semibold text-gray-900 mb-2">
                                Horas trabajadas
                                <span x-show="isHourlyTask" class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       id="hours_worked" 
                                       name="hours_worked" 
                                       x-model="hoursWorked"
                                       step="0.1" 
                                       min="0"
                                       max="24"
                                       :class="{ 'border-red-500 focus:ring-red-500': showHoursError }"
                                       class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 pr-12 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-base"
                                       placeholder="0.0">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                    <span class="text-gray-400 font-medium">h</span>
                                </div>
                            </div>
                            <p x-show="showHoursError" 
                               x-cloak
                               class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                Debe ser mayor a 0
                            </p>
                        </div>
                        
                        <!-- Quantity Harvested -->
                        <div>
                            <label for="quantity_harvested" class="block text-sm font-semibold text-gray-900 mb-2">
                                Cantidad recolectada
                                <span x-show="isQuantityTask" class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       id="quantity_harvested" 
                                       name="quantity_harvested" 
                                       x-model="quantityHarvested"
                                       step="0.1"
                                       min="0"
                                       :class="{ 'border-red-500 focus:ring-red-500': showQuantityError }"
                                       class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 pr-12 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-base"
                                       placeholder="0.0">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                    <span class="text-gray-400 font-medium">kg</span>
                                </div>
                            </div>
                            <p x-show="showQuantityError" 
                               x-cloak
                               class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                Debe ser mayor a 0
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="flex flex-col-reverse sm:flex-row gap-3 mt-6 pt-6 border-t border-gray-200">
                    <button type="button" 
                            onclick="closeCompleteModal()" 
                            class="flex-1 px-6 py-4 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors touch-manipulation">
                        Cancelar
                    </button>
                    <button type="submit"
                            :disabled="!canComplete"
                            :class="{ 'opacity-50 cursor-not-allowed': !canComplete, 'hover:bg-emerald-700 active:bg-emerald-800': canComplete }"
                            class="flex-1 px-6 py-4 bg-emerald-600 text-white rounded-xl font-bold shadow-sm hover:shadow-md transition-all duration-200 touch-manipulation">
                        Completar Tarea
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    
    /* Improve touch targets for mobile */
    @media (max-width: 640px) {
        .touch-manipulation {
            min-height: 44px;
        }
    }
</style>

<script>
function filterTasks(status) {
    // Update button styles
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('bg-emerald-600', 'text-white', 'shadow-sm');
        btn.classList.add('text-gray-600', 'hover:bg-gray-100');
    });
    
    const activeBtn = document.querySelector(`[data-filter="${status}"]`);
    activeBtn.classList.add('bg-emerald-600', 'text-white', 'shadow-sm');
    activeBtn.classList.remove('text-gray-600', 'hover:bg-gray-100');
    
    // Filter tasks
    const tasks = document.querySelectorAll('.task-item');
    tasks.forEach(task => {
        const taskStatus = task.dataset.status;
        
        if (status === 'all') {
            task.style.display = 'block';
        } else if (status === 'completed') {
            // Mostrar tanto tareas completadas como aprobadas
            if (taskStatus === 'completed' || taskStatus === 'approved') {
                task.style.display = 'block';
            } else {
                task.style.display = 'none';
            }
        } else if (taskStatus === status) {
            task.style.display = 'block';
        } else {
            task.style.display = 'none';
        }
    });
}

function openCompleteModal(taskId, description, pricePerHour = 0, pricePerDay = 0, pricePerKg = 0) {
    const modal = document.getElementById('completeModal');
    const alpineComponent = Alpine.$data(modal.querySelector('[x-data]'));
    
    // Determinar el tipo de tarea
    const isHourlyTask = pricePerHour > 0 || pricePerDay > 0;
    const isQuantityTask = pricePerKg > 0;
    
    // Configurar Alpine.js
    alpineComponent.setTaskType(isHourlyTask, isQuantityTask);
    alpineComponent.reset();
    
    document.getElementById('taskDescription').textContent = description;
    document.getElementById('completeForm').action = `/worker/tasks/${taskId}/complete`;
    modal.classList.remove('hidden');
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

function closeCompleteModal() {
    document.getElementById('completeModal').classList.add('hidden');
    document.getElementById('completeForm').reset();
    
    // Restore body scroll
    document.body.style.overflow = '';
}

// Close modal when clicking outside
document.getElementById('completeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCompleteModal();
    }
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCompleteModal();
    }
});
</script>
@endsection
