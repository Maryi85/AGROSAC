@extends('worker.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Mis Tareas</h2>
@endsection

@section('content')
<div class="space-y-4 pb-6">
    <!-- Filter Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-2">
        <div class="grid grid-cols-3 gap-1">
            <button onclick="filterTasks('all')"
                    class="filter-btn px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 bg-emerald-600 text-white"
                    data-filter="all">
                <i data-lucide="list" class="w-4 h-4 inline mr-1"></i> Todas
            </button>
            <button onclick="filterTasks('pending')"
                    class="filter-btn px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-600 hover:bg-gray-50"
                    data-filter="pending">
                <i data-lucide="clock" class="w-4 h-4 inline mr-1"></i> Pendientes
            </button>
            <button onclick="filterTasks('completed')"
                    class="filter-btn px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-600 hover:bg-gray-50"
                    data-filter="completed">
                <i data-lucide="check-circle" class="w-4 h-4 inline mr-1"></i> Completadas
            </button>
        </div>
    </div>

    <!-- Tasks Cards Grid (2 columns) -->
    <div id="tasks-grid" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($tasks as $task)
            <!-- Task Card -->
            <div id="task-card-{{ $task->id }}" class="task-item bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200"
                 data-status="{{ $task->status }}">

                <div class="p-5 space-y-3">
                    {{-- Title + Status Badge --}}
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="text-base font-bold text-gray-900 leading-tight flex-1">
                            {{ $task->description }}
                        </h3>
                        @php
                            $statusMap = [
                                'pending'     => ['label' => 'Pendiente',  'class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'clock'],
                                'completed'   => ['label' => 'Completada', 'class' => 'bg-green-100 text-green-800',  'icon' => 'check-circle-2'],
                                'approved'    => ['label' => 'Aprobada',   'class' => 'bg-gray-100 text-gray-700',   'icon' => 'circle'],
                                'rejected'    => ['label' => 'Rechazada',  'class' => 'bg-red-100 text-red-700',     'icon' => 'x-circle'],
                                'invalid'     => ['label' => 'Inválida',   'class' => 'bg-red-100 text-red-700',     'icon' => 'x-circle'],
                                'in_progress' => ['label' => 'En Progreso','class' => 'bg-blue-100 text-blue-700',   'icon' => 'loader'],
                                'cancelled'   => ['label' => 'Cancelada',  'class' => 'bg-gray-100 text-gray-600',   'icon' => 'ban'],
                            ];
                            $s = $statusMap[$task->status] ?? ['label' => ucfirst($task->status), 'class' => 'bg-gray-100 text-gray-700', 'icon' => 'circle'];
                        @endphp
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap {{ $s['class'] }}">
                            <i data-lucide="{{ $s['icon'] }}" class="w-3.5 h-3.5"></i>
                            {{ $s['label'] }}
                        </span>
                    </div>

                    {{-- Location + Crop --}}
                    <div class="flex items-center gap-4 text-sm text-gray-700">
                        <span class="flex items-center gap-1">
                            <i data-lucide="map-pin" class="w-4 h-4 text-emerald-600"></i>
                            {{ $task->plot->name ?? 'Sin lote' }}
                        </span>
                        <span class="flex items-center gap-1">
                            <i data-lucide="sprout" class="w-4 h-4 text-emerald-600"></i>
                            {{ $task->crop->name ?? 'Sin cultivo' }}
                        </span>
                    </div>

                    {{-- Date --}}
                    @if($task->scheduled_for)
                        <div class="flex items-center gap-1 text-sm font-medium {{ $task->scheduled_for < now() && $task->status === 'pending' ? 'text-red-600' : 'text-red-500' }}">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                            {{ $task->scheduled_for->format('d/m/Y') }}
                        </div>
                    @endif

                    {{-- Ver detalles --}}
                    <button
                        onclick="openDetailsModal(this)"
                        data-description="{{ addslashes($task->description) }}"
                        data-type="{{ $task->type ?? '—' }}"
                        data-status="{{ $s['label'] }}"
                        data-status-class="{{ $s['class'] }}"
                        data-date="{{ $task->scheduled_for ? $task->scheduled_for->format('d/m/Y') : '—' }}"
                        data-plot="{{ $task->plot->name ?? 'Sin lote' }}"
                        data-crop="{{ $task->crop->name ?? 'Sin cultivo' }}"
                        data-price-hour="{{ (int)$task->price_per_hour ?? 0 }}"
                        data-price-day="{{ (int)$task->price_per_day ?? 0 }}"
                        data-price-kg="{{ (int)$task->price_per_kg ?? 0 }}"
                        data-hours="{{ $task->hours ?? 0 }}"
                        data-kilos="{{ $task->kilos ?? 0 }}"
                        data-total-payment="{{ $task->total_payment ?? 0 }}"
                        data-estimated-hours="{{ ($task->estimated_hours > 0) ? $task->estimated_hours : (((int)$task->price_per_hour > 0) ? ($task->total_payment / (int)$task->price_per_hour) : 0) }}"
                        data-estimated-total="{{ ($task->estimated_total_payment > 0) ? $task->estimated_total_payment : $task->total_payment }}"
                        data-creator-name="{{ $task->creator->name ?? 'Administración' }}"
                        data-raw-status="{{ $task->status }}"
                        data-notes="{{ addslashes($task->notes ?? '') }}"
                        class="inline-flex items-center gap-1 text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                        Ver todos los detalles
                    </button>

                    {{-- Completar Tarea (solo pendientes) --}}
                    @if($task->status === 'pending')
                        <button onclick="openCompleteModal({{ $task->id }}, '{{ addslashes($task->description) }}', {{ (int)$task->price_per_hour ?? 0 }}, {{ (int)$task->price_per_day ?? 0 }}, {{ (int)$task->price_per_kg ?? 0 }})"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm shadow-sm transition-all duration-200">
                            <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                            Completar Tarea
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="md:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
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

<!-- Task Details Modal -->
<div id="detailsModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto shadow-xl">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">Detalles de la tarea</h3>
            <button onclick="closeDetailsModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-5">
            <!-- Información General -->
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Información General</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Tipo:</p>
                        <p id="detail-type" class="text-sm font-semibold text-gray-900">—</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Estado:</p>
                        <span id="detail-status" class="text-sm font-semibold">—</span>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500 mb-0.5">Fecha programada:</p>
                        <p id="detail-date" class="text-sm font-semibold text-gray-900">—</p>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            <!-- Ubicación y Cultivo -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Lote</p>
                    <p id="detail-plot" class="text-sm font-bold text-gray-900">—</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Cultivo</p>
                    <p id="detail-crop" class="text-sm font-bold text-gray-900">—</p>
                </div>
            </div>

            <hr class="border-gray-100">

            <!-- Descripción -->
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Descripción</p>
                <p id="detail-description" class="text-sm text-gray-700 leading-relaxed italic">—</p>
            </div>

            <hr id="payment-divider" class="border-gray-100">

            <!-- Información de Pago (Aumentada para vista comparativa) -->
            <div id="detail-payment-section">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Información de Pago</p>
                <div id="detail-payment-grid" class="space-y-4">
                    <!-- Contenido dinámico inyectado por JS -->
                </div>
            </div>

            <!-- Notas (si aplica) -->
            <div id="detail-notes-section" class="hidden">
                <hr class="border-gray-100 mb-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Notas del trabajador</p>
                <p id="detail-notes" class="text-sm text-gray-700 leading-relaxed bg-gray-50 p-3 rounded-xl border border-gray-100">—</p>
            </div>
        </div>
    </div>
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
// ---- Details Modal ----
function openDetailsModal(btn) {
    const d = btn.dataset;

    // Información general
    document.getElementById('detail-type').textContent        = d.type || '—';
    document.getElementById('detail-date').textContent        = d.date || '—';
    document.getElementById('detail-description').textContent = d.description || '—';
    document.getElementById('detail-plot').textContent        = d.plot || '—';
    document.getElementById('detail-crop').textContent        = d.crop || '—';

    // Estado con color
    const statusEl = document.getElementById('detail-status');
    statusEl.textContent = d.status || '—';
    // Limpiar clases de color anteriores y aplicar las nuevas
    statusEl.className = 'text-sm font-semibold px-2 py-0.5 rounded-full ' + (d.statusClass || '');

    // Información de pago
    const priceHour = parseFloat(d.priceHour) || 0;
    const priceDay  = parseFloat(d.priceDay)  || 0;
    const priceKg   = parseFloat(d.priceKg)   || 0;
    const hours     = parseFloat(d.hours)     || 0;
    const kilos     = parseFloat(d.kilos)     || 0;
    const estHours  = parseFloat(d.estimatedHours) || 0;
    const estTotal  = parseFloat(d.estimatedTotal) || 0;
    const creator   = d.creatorName || 'Administración';

    const paymentSection = document.getElementById('detail-payment-section');
    const paymentGrid    = document.getElementById('detail-payment-grid');
    paymentGrid.innerHTML = '';

    const fmt = n => new Intl.NumberFormat('es-CO', { minimumFractionDigits: 0 }).format(n);

    let hasPayment = false;
    const totalReal = parseFloat(d.totalPayment) || 0;
    const isFinalized = d.rawStatus === 'completed' || d.rawStatus === 'approved';

    // Fallback logic for older tasks
    const finalEstHours = estHours || (priceHour > 0 ? (totalReal / priceHour) : 0);
    const finalEstTotal = estTotal || totalReal;

    if (priceHour > 0) {
        hasPayment = true;
        if (isFinalized) {
            // Vista Comparativa
            paymentGrid.innerHTML = `
                <div class="grid grid-cols-1 gap-4">
                    <!-- Asignación -->
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3 italic">Establecido por ${creator}</p>
                        <div class="space-y-2">
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-400">Precio/h</span>
                                <span class="font-bold text-gray-700">$${fmt(priceHour)}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-400">Horas Est.</span>
                                <span class="font-bold text-gray-700">${finalEstHours.toFixed(0)}h</span>
                            </div>
                            <div class="pt-2 border-t border-gray-200/50 flex justify-between items-center">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Total Est.</span>
                                <span class="text-sm font-black text-gray-900">$${fmt(finalEstTotal)}</span>
                            </div>
                        </div>
                    </div>
                    <!-- Registro del Trabajador -->
                    <div class="bg-emerald-50/50 p-4 rounded-xl border border-emerald-100">
                        <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest mb-3">Tu Registro</p>
                        <div class="space-y-2">
                            <div class="flex justify-between text-xs">
                                <span class="text-emerald-600/60">Precio establecido</span>
                                <span class="font-bold text-emerald-900">$${fmt(priceHour)}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-emerald-600/60">Horas Reg.</span>
                                <span class="font-bold text-emerald-900">${hours}h</span>
                            </div>
                            <div class="pt-2 border-t border-emerald-100 flex justify-between items-center">
                                <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">Total Reg.</span>
                                <span class="text-sm font-black text-emerald-700">$${fmt(hours * priceHour)}</span>
                            </div>
                        </div>
                    </div>
                </div>
                ${d.rawStatus === 'approved' ? `
                    <div class="mt-4 p-4 bg-emerald-600 rounded-xl text-white shadow-sm">
                        <p class="text-[10px] font-black uppercase tracking-widest opacity-80 mb-1">Total Liquidado</p>
                        <p class="text-2xl font-black">$${fmt(totalReal)}</p>
                    </div>
                ` : ''}
            `;
        } else {
            // Vista Simple
            paymentGrid.innerHTML = `
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Precio por hora</p>
                        <p class="text-sm font-bold text-gray-900">$${fmt(priceHour)}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Horas estimadas</p>
                        <p class="text-sm font-bold text-gray-900">${estHours.toFixed(0)}h</p>
                    </div>
                    <div class="col-span-2 pt-3 border-t border-gray-200/50">
                        <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest mb-1">Total estimado</p>
                        <p class="text-xl font-black text-emerald-700">$${fmt(estTotal)}</p>
                    </div>
                </div>
            `;
        }
    } else if (priceDay > 0) {
        hasPayment = true;
        paymentGrid.innerHTML = `
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Precio por día</span>
                    <span class="font-bold text-gray-900">$${fmt(priceDay)}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">${isFinalized ? 'Horas trabajadas' : 'Horas estimadas'}</span>
                    <span class="font-bold text-gray-900">${hours}h</span>
                </div>
                <div class="pt-3 border-t border-gray-200/50 flex justify-between items-center">
                    <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">${isFinalized ? 'Total a pagar' : 'Total estimado'}</span>
                    <span class="text-lg font-black text-emerald-700">$${fmt(isFinalized && totalReal > 0 ? totalReal : (hours / 8) * priceDay)}</span>
                </div>
            </div>
        `;
    } else if (priceKg > 0) {
        hasPayment = true;
        paymentGrid.innerHTML = `
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Precio por kg</span>
                    <span class="font-bold text-gray-900">$${fmt(priceKg)}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">${isFinalized ? 'Cantidad real' : 'Kilos estimados'}</span>
                    <span class="font-bold text-gray-900">${kilos} kg</span>
                </div>
                <div class="pt-3 border-t border-gray-200/50 flex justify-between items-center">
                    <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">${isFinalized ? 'Total a pagar' : 'Total estimado'}</span>
                    <span class="text-lg font-black text-emerald-700">$${fmt(isFinalized && totalReal > 0 ? totalReal : priceKg * kilos)}</span>
                </div>
            </div>
        `;
    }
    paymentSection.style.display = hasPayment ? 'block' : 'none';
    document.getElementById('payment-divider').style.display = hasPayment ? 'block' : 'none';

    // Notas
    const notesSection = document.getElementById('detail-notes-section');
    if (d.notes && d.notes.trim() !== '') {
        document.getElementById('detail-notes').textContent = d.notes;
        notesSection.classList.remove('hidden');
    } else {
        notesSection.classList.add('hidden');
    }

    // Mostrar modal
    const modal = document.getElementById('detailsModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    if (window.lucide) window.lucide.createIcons();
}

function closeDetailsModal() {
    document.getElementById('detailsModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Cerrar al hacer clic fuera
document.getElementById('detailsModal').addEventListener('click', function(e) {
    if (e.target === this) closeDetailsModal();
});

// ---- Filter ----
function filterTasks(status) {
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('bg-emerald-600', 'text-white');
        btn.classList.add('text-gray-600', 'hover:bg-gray-50');
    });

    const activeBtn = document.querySelector(`[data-filter="${status}"]`);
    activeBtn.classList.add('bg-emerald-600', 'text-white');
    activeBtn.classList.remove('text-gray-600', 'hover:bg-gray-50');

    const tasks = document.querySelectorAll('.task-item');
    tasks.forEach(task => {
        const taskStatus = task.dataset.status;
        if (status === 'all') {
            task.style.display = 'block';
        } else if (status === 'completed') {
            task.style.display = (taskStatus === 'completed' || taskStatus === 'approved') ? 'block' : 'none';
        } else {
            task.style.display = taskStatus === status ? 'block' : 'none';
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
        closeDetailsModal();
    }
});


// ---- Complete Task Form AJAX ----
document.addEventListener('submit', function(e) {
    if (e.target.id === 'completeForm') {
        e.preventDefault();
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Disable button and show loading logic
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i data-lucide="loader" class="w-5 h-5 animate-spin mx-auto"></i>';
        if (window.lucide) lucide.createIcons();

        const url = form.action;
        const formData = new FormData(form);

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeCompleteModal();
                
                // Show success toast
                if (window.showSuccessAlert) {
                    showSuccessAlert(data.message);
                } else {
                    Swal.fire('¡Éxito!', data.message, 'success');
                }

                // Instant UI update
                const taskId = data.task ? data.task.id : (url.match(/tasks\/(\d+)/)?.[1]);
                if (taskId) {
                    const card = document.getElementById(`task-card-${taskId}`);
                    if (card) {
                        // Change status in UI instead of removing (to show it completed in "All" view)
                        card.dataset.status = 'completed';
                        const badgeContainer = card.querySelector('.status-badge') || card.querySelector('span.inline-flex');
                        if (badgeContainer) {
                            badgeContainer.className = 'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap bg-green-100 text-green-800';
                            badgeContainer.innerHTML = '<i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i> Completada';
                        }
                        // Remove "Completar" button
                        const completeBtn = card.querySelector('button[onclick^="openCompleteModal"]');
                        if (completeBtn) completeBtn.remove();
                        
                        if (window.lucide) lucide.createIcons();
                        
                        // Apply current filter
                        const activeFilter = document.querySelector('.filter-btn.bg-emerald-600').dataset.filter;
                        filterTasks(activeFilter);
                    }
                }
            } else {
                Swal.fire('Error', data.message || 'Error al completar la tarea', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                if (window.lucide) lucide.createIcons();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Ocurrió un error inesperado', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            if (window.lucide) lucide.createIcons();
        });
    }
});

function refreshTasksList() {
    if (isModalOpen) return; // Don't refresh if user is interacting with a modal

    fetch(window.location.href, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.data) {
            renderTaskCards(data.data);
            // Re-apply current filter
            const activeFilterElement = document.querySelector('.filter-btn.bg-emerald-600');
            const activeFilter = activeFilterElement ? activeFilterElement.dataset.filter : 'all';
            filterTasks(activeFilter);
        }
    })
    .catch(error => console.error('Error refreshing tasks:', error));
}

function renderTaskCards(tasks) {
    const grid = document.getElementById('tasks-grid');
    if (tasks.length === 0) {
        grid.innerHTML = `
            <div class="md:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="clipboard-check" class="w-8 h-8 text-emerald-600"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">No hay tareas asignadas</h3>
                <p class="text-gray-600">No tienes tareas pendientes o completadas en este momento.</p>
            </div>
        `;
    } else {
        grid.innerHTML = tasks.map(task => {
            const dateClass = task.is_overdue ? 'text-red-600' : 'text-red-500';
            const completeBtn = task.status === 'pending' ? `
                <button onclick="openCompleteModal(${task.id}, '${task.description.replace(/'/g, "\\'")}', ${task.price_per_hour}, ${task.price_per_day}, ${task.price_per_kg})"
                        class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm shadow-sm transition-all duration-200">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                    Completar Tarea
                </button>
            ` : '';

            return `
                <div class="task-item bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200"
                     data-status="${task.status}">

                    <div class="p-5 space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="text-base font-bold text-gray-900 leading-tight flex-1">
                                ${task.description}
                            </h3>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap ${task.status_class}">
                                <i data-lucide="${task.status_icon}" class="w-3.5 h-3.5"></i>
                                ${task.status_label}
                            </span>
                        </div>

                        <div class="flex items-center gap-4 text-sm text-gray-700">
                            <span class="flex items-center gap-1">
                                <i data-lucide="map-pin" class="w-4 h-4 text-emerald-600"></i>
                                ${task.plot_name}
                            </span>
                            <span class="flex items-center gap-1">
                                <i data-lucide="sprout" class="w-4 h-4 text-emerald-600"></i>
                                ${task.crop_name}
                            </span>
                        </div>

                        ${task.scheduled_for ? `
                            <div class="flex items-center gap-1 text-sm font-medium ${dateClass}">
                                <i data-lucide="calendar" class="w-4 h-4"></i>
                                ${task.scheduled_for}
                            </div>
                        ` : ''}

                        <button
                            onclick="openDetailsModal(this)"
                            data-description="${task.description.replace(/"/g, '&quot;')}"
                            data-type="${task.type}"
                            data-status="${task.status_label}"
                            data-status-class="${task.status_class}"
                            data-date="${task.scheduled_for || '—'}"
                            data-plot="${task.plot_name}"
                            data-crop="${task.crop_name}"
                            data-price-hour="${task.price_per_hour}"
                            data-price-day="${task.price_per_day}"
                            data-price-kg="${task.price_per_kg}"
                            data-hours="${task.hours}"
                            data-kilos="${task.kilos}"
                            data-total-payment="${task.total_payment}"
                            data-estimated-hours="${task.estimated_hours || (task.price_per_hour > 0 ? (task.total_payment / task.price_per_hour) : 0)}"
                            data-estimated-total="${task.estimated_total_payment || task.total_payment}"
                            data-creator-name="${task.creator_name || 'Administración'}"
                            data-raw-status="${task.status}"
                            data-notes="${(task.notes || '').replace(/"/g, '&quot;')}"
                            class="inline-flex items-center gap-1 text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            Ver todos los detalles
                        </button>

                        ${completeBtn}
                    </div>
                </div>
            `;
        }).join('');
    }

    if (window.lucide) window.lucide.createIcons();
}

// Start polling every 3 seconds
setInterval(refreshTasksList, 3000);

</script>
@endsection
