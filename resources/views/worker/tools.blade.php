@extends('worker.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Gestión de Herramientas</h2>
@endsection

@section('content')
<div class="space-y-4 pb-6">
    <!-- Modern Segmented Control Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-2">
        <div class="grid grid-cols-2 gap-2 bg-gray-50 p-1 rounded-lg">
            <button onclick="showSection('available')" 
                    class="tab-btn px-4 py-3 rounded-lg text-sm font-semibold transition-all duration-200 bg-emerald-600 text-white shadow-sm" 
                    data-tab="available">
                <div class="flex items-center justify-center gap-2">
                    <i data-lucide="wrench" class="w-4 h-4"></i>
                    <span>Disponibles</span>
                </div>
            </button>
            <button onclick="showSection('my-loans')" 
                    class="tab-btn px-4 py-3 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-600 hover:bg-gray-100" 
                    data-tab="my-loans">
                <div class="flex items-center justify-center gap-2">
                    <i data-lucide="package" class="w-4 h-4"></i>
                    <span>Mis Préstamos</span>
                </div>
            </button>
        </div>
    </div>

    <!-- Available Tools Section -->
    <div id="available-section" class="section">
        <!-- Request Button (Mobile-First) -->
        <div class="mb-4">
            <button onclick="openRequestModal()" 
                    class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-xl font-bold text-base shadow-sm hover:shadow-md transition-all duration-200 touch-manipulation">
                <i data-lucide="plus-circle" class="w-5 h-5"></i>
                <span>Solicitar Herramienta</span>
            </button>
        </div>

        <div id="available-tools-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($availableTools as $tool)
                    <!-- Modern Tool Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                        <!-- Image -->
                        <div class="relative h-48 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                            @if($tool->photo)
                                <img src="{{ storage_asset($tool->photo) }}" 
                                     alt="{{ $tool->name }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i data-lucide="wrench" class="w-16 h-16 text-gray-400"></i>
                                </div>
                            @endif
                            
                            <!-- Status Badge -->
                            <div class="absolute top-3 right-3">
                                @if($tool->status === 'operational')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-green-500 text-white text-xs font-semibold shadow-lg">
                                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                        Operacional
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-yellow-500 text-white text-xs font-semibold shadow-lg">
                                        <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                                        {{ ucfirst($tool->status) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div class="p-4">
                            <h4 class="font-bold text-gray-900 mb-3 text-base truncate">{{ $tool->name }}</h4>
                            
                            <!-- Stats Grid -->
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-3">
                                    <div class="flex items-center gap-2 mb-1">
                                        <i data-lucide="check-square" class="w-4 h-4 text-emerald-600"></i>
                                        <span class="text-xs font-medium text-gray-600">Disponibles</span>
                                    </div>
                                    <p class="text-xl font-bold text-emerald-700">{{ $tool->available_qty }}</p>
                                </div>
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <div class="flex items-center gap-2 mb-1">
                                        <i data-lucide="package" class="w-4 h-4 text-gray-600"></i>
                                        <span class="text-xs font-medium text-gray-600">Total</span>
                                    </div>
                                    <p class="text-xl font-bold text-gray-700">{{ $tool->total_entries }}</p>
                                </div>
                            </div>
                            
                            <!-- Request Button -->
                            <button onclick="openRequestModal()" 
                                    data-tool-id="{{ $tool->id }}"
                                    data-tool-name="{{ $tool->name }}"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-lg font-semibold text-sm shadow-sm hover:shadow transition-all duration-200 touch-manipulation">
                                <i data-lucide="hand" class="w-4 h-4"></i>
                                <span>Solicitar</span>
                            </button>
                        </div>
                    </div>
                @endforeach
        </div>
    </div>

    <!-- My Loans Section -->
    <div id="my-loans-section" class="section hidden">
        @if($myLoans->isNotEmpty())
        <div id="my-loans-grid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($myLoans as $loan)
                    @php
                        $statusConfig = [
                            'pending'           => ['class' => 'bg-yellow-100 text-yellow-800 border border-yellow-200', 'icon' => 'clock',              'text' => 'Pendiente'],
                            'approved'          => ['class' => 'bg-blue-100 text-blue-800 border border-blue-200',       'icon' => 'check',             'text' => 'Aprobado'],
                            'rejected'          => ['class' => 'bg-red-100 text-red-800 border border-red-200',          'icon' => 'x-circle',          'text' => 'Rechazado'],
                            'out'               => ['class' => 'bg-blue-100 text-blue-800 border border-blue-200',       'icon' => 'arrow-right-circle','text' => 'Prestada'],
                            'returned_by_worker'=> ['class' => 'bg-green-100 text-green-800 border border-green-200',   'icon' => 'check-circle',      'text' => 'Devuelta (Pendiente)'],
                            'returned'          => ['class' => 'bg-green-100 text-green-800 border border-green-200',   'icon' => 'check-circle',      'text' => 'Devuelta'],
                        ];
                        $cfg = $statusConfig[$loan->status] ?? ['class' => 'bg-gray-100 text-gray-700', 'icon' => 'circle', 'text' => ucfirst($loan->status)];
                    @endphp

                    <!-- Loan Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">

                        <!-- Image + Badge -->
                        <div class="relative h-40 bg-gray-100 overflow-hidden">
                            @if($loan->tool->photo)
                                <img src="{{ storage_asset($loan->tool->photo) }}"
                                     alt="{{ $loan->tool->name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i data-lucide="wrench" class="w-14 h-14 text-gray-300"></i>
                                </div>
                            @endif

                            <!-- Status Badge overlay -->
                            <span class="absolute top-2 right-2 inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold {{ $cfg['class'] }}">
                                <i data-lucide="{{ $cfg['icon'] }}" class="w-3 h-3"></i>
                                {{ $cfg['text'] }}
                            </span>
                        </div>

                        <!-- Content -->
                        <div class="p-4 space-y-2">
                            <h4 class="font-bold text-gray-900 text-sm leading-tight truncate">{{ $loan->tool->name }}</h4>

                            <div class="flex items-center gap-1 text-xs text-gray-700">
                                <i data-lucide="hash" class="w-3.5 h-3.5 text-gray-500"></i>
                                <span>Cantidad: <strong>{{ $loan->quantity }}</strong></span>
                            </div>

                            @if($loan->task)
                                <div class="flex items-center gap-1 text-xs text-blue-700 bg-blue-50 px-2 py-1 rounded-md border border-blue-100">
                                    <i data-lucide="briefcase" class="w-3 h-3"></i>
                                    <span class="truncate">{{ $loan->task->description }}</span>
                                </div>
                            @endif

                            <div class="flex items-center gap-1 text-xs text-gray-700">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-500"></i>
                                <span>Solicitada: {{ $loan->created_at->format('d/m/Y H:i') }}</span>
                            </div>

                            <div class="space-y-2 mt-3">
                                <!-- Devolver Herramienta -->
                                @if($loan->status === 'out')
                                <form method="POST" action="{{ route('worker.tools.return', $loan) }}" class="w-full" data-confirm="true" data-message="¿Confirmar devolución de esta herramienta?">
                                    @csrf
                                    <button type="submit"
                                            class="w-full flex items-center justify-center gap-1.5 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors">
                                        <i data-lucide="arrow-left-circle" class="w-4 h-4"></i>
                                        Devolver Herramienta
                                    </button>
                                </form>
                                @endif
                                
                                <!-- Ver detalles button -->
                                <button
                                    onclick="openLoanDetailsModal(this)"
                                    data-tool-name="{{ $loan->tool->name }}"
                                    data-status="{{ $cfg['text'] }}"
                                    data-status-class="{{ $cfg['class'] }}"
                                    data-quantity="{{ $loan->quantity }}"
                                    data-requested="{{ $loan->created_at->format('d/m/Y H:i') }}"
                                    data-out-at="{{ $loan->out_at ? $loan->out_at->format('d/m/Y H:i') : '' }}"
                                    data-due-at="{{ $loan->due_at ? $loan->due_at->format('d/m/Y') : '' }}"
                                    data-returned-at="{{ $loan->returned_at ? $loan->returned_at->format('d/m/Y H:i') : '' }}"
                                    data-notes="{{ addslashes($loan->request_notes ?? '') }}"
                                    data-loan-status="{{ $loan->status }}"
                                    data-loan-id="{{ $loan->id }}"
                                    data-task-name="{{ $loan->task ? $loan->task->description . ' (' . ($loan->task->plot->name ?? 'Lote General') . ')' : '' }}"
                                    data-return-url="{{ route('worker.tools.return', $loan) }}"
                                    class="w-full flex items-center justify-center gap-1.5 mt-1 px-3 py-2 border border-gray-200 rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    Ver todos los detalles
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
        </div>

        <!-- Pagination Container -->
        <div id="loans-pagination" class="mt-4">
            @if($myLoans->hasPages())
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    {{ $myLoans->links() }}
                </div>
            @endif
        </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="package" class="w-8 h-8 text-emerald-600"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">No tienes préstamos</h3>
                <p class="text-gray-600">No has solicitado ninguna herramienta aún.</p>
            </div>
        @endif
    </div>

</div>

<!-- Loan Details Modal -->
<div id="loanDetailsModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto shadow-xl">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">Detalles del préstamo</h3>
            <button onclick="closeLoanDetailsModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-5">
            <!-- Herramienta + Estado -->
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Información General</p>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <p class="text-[9px] font-bold text-gray-400 uppercase mb-0.5">Herramienta:</p>
                        <p id="loan-detail-tool" class="text-sm font-bold text-gray-900">—</p>
                    </div>
                    <div id="loan-detail-task-row" class="col-span-2" style="display:none;">
                        <p class="text-[9px] font-bold text-gray-400 uppercase mb-0.5">Tarea relacionada:</p>
                        <div class="flex items-center gap-2 p-2 bg-blue-50 border border-blue-100 rounded-lg">
                            <i data-lucide="briefcase" class="w-4 h-4 text-blue-600"></i>
                            <p id="loan-detail-task" class="text-xs font-semibold text-blue-900">—</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-gray-400 uppercase mb-0.5">Estado:</p>
                        <span id="loan-detail-status" class="text-[10px] font-black">—</span>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-gray-400 uppercase mb-0.5">Cantidad:</p>
                        <p id="loan-detail-quantity" class="text-sm font-bold text-gray-900">—</p>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            <!-- Fechas -->
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Fechas</p>
                <div class="space-y-2 text-sm text-gray-700">
                    <div class="flex items-center gap-2">
                        <i data-lucide="calendar-plus" class="w-4 h-4 text-gray-400 flex-shrink-0"></i>
                        <span>Solicitada: <strong id="loan-detail-requested">—</strong></span>
                    </div>
                    <div id="loan-detail-out-row" class="flex items-center gap-2">
                        <i data-lucide="calendar-check" class="w-4 h-4 text-gray-400 flex-shrink-0"></i>
                        <span>Prestada: <strong id="loan-detail-out">—</strong></span>
                    </div>
                    <div id="loan-detail-due-row" class="flex items-center gap-2">
                        <i data-lucide="calendar-clock" class="w-4 h-4 text-gray-400 flex-shrink-0"></i>
                        <span>Vence: <strong id="loan-detail-due">—</strong></span>
                    </div>
                    <div id="loan-detail-ret-row" class="flex items-center gap-2">
                        <i data-lucide="calendar-check-2" class="w-4 h-4 text-gray-400 flex-shrink-0"></i>
                        <span>Devuelta: <strong id="loan-detail-ret">—</strong></span>
                    </div>
                </div>
            </div>

            <!-- Notas -->
            <div id="loan-detail-notes-section">
                <hr class="border-gray-100 mb-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Notas</p>
                <p id="loan-detail-notes" class="text-sm text-gray-700 leading-relaxed">—</p>
            </div>

            <!-- Botón devolver (Solo cuando está prestada en el Modal, duplicado como info, pero sin funcionalidad extra) -->
            <div id="loan-detail-return-btn" style="display:none;">
            </div>
        </div>
    </div>
</div>

<!-- Request Tool Modal -->

<div id="requestModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50">
    <div class="flex items-end sm:items-center justify-center min-h-screen p-0 sm:p-4">
        <div class="bg-white rounded-t-3xl sm:rounded-2xl w-full sm:max-w-lg max-h-[90vh] overflow-y-auto">
            
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 p-6 pb-4 rounded-t-3xl sm:rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-900">Solicitar Herramienta</h3>
                    <button type="button" onclick="closeRequestModal()" 
                            class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors touch-manipulation">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <form id="requestForm" method="POST" action="{{ route('worker.tools.request') }}" class="p-6">
                @csrf
                <div class="space-y-5">
                    <!-- Tool Selection -->
                    <div>
                        <label for="tool_id" class="block text-sm font-bold text-gray-900 mb-2">
                            Herramienta <span class="text-red-500">*</span>
                        </label>
                        <select id="tool_id" name="tool_id" required
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm font-medium bg-gray-50/50 appearance-none">
                            <option value="">Seleccionar herramienta...</option>
                            @foreach($availableTools as $tool)
                                <option value="{{ $tool->id }}" data-max="{{ $tool->available_qty }}">
                                    {{ $tool->name }} ({{ $tool->available_qty }} disponibles)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Task Selection -->
                    <div>
                        <label for="task_id" class="block text-sm font-bold text-gray-900 mb-2">
                            Tarea en la que se usará
                        </label>
                        <select id="task_id" name="task_id"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm font-medium bg-gray-50/50 appearance-none">
                            <option value="">Opcional. Selecciona la tarea...</option>
                            @foreach($myTasks as $task)
                                <option value="{{ $task->id }}">
                                    {{ $task->description }} — {{ $task->plot->name ?? 'Lote General' }}, {{ $task->scheduled_for ? $task->scheduled_for->format('d/m/Y') : 'S/F' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-gray-400 mt-2 flex items-center gap-1 font-medium">
                            <i data-lucide="info" class="w-3 h-3"></i>
                            Opcional. Selecciona la tarea en la que utilizarás esta herramienta.
                        </p>
                    </div>
                    
                    <!-- Quantity -->
                    <div>
                        <label for="quantity" class="block text-sm font-bold text-gray-900 mb-2">
                            Cantidad <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="quantity" name="quantity" min="1" max="1" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm font-bold bg-white"
                               placeholder="1">
                        <p id="quantity-help" class="text-[10px] text-gray-400 mt-2 flex items-center gap-1 font-medium">
                            <i data-lucide="info" class="w-3 h-3"></i>
                            Selecciona una herramienta para ver la cantidad disponible
                        </p>
                    </div>
                    
                    <!-- Due Date -->
                    <div>
                        <label for="due_at" class="block text-sm font-bold text-gray-900 mb-2">
                            Fecha de devolución (opcional)
                        </label>
                        <div class="relative">
                            <input type="date" id="due_at" name="due_at"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm font-medium bg-gray-50/50">
                        </div>
                    </div>
                    
                    <!-- Notes -->
                    <div>
                        <label for="request_notes" class="block text-sm font-bold text-gray-900 mb-2">
                            Notas (opcional)
                        </label>
                        <textarea id="request_notes" name="request_notes" rows="3"
                                  class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm font-medium bg-gray-50/50 resize-none"
                                  placeholder="hola"></textarea>
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="flex flex-col-reverse sm:flex-row gap-3 mt-8">
                    <button type="button" 
                            onclick="closeRequestModal()" 
                            class="flex-1 px-6 py-4 border border-gray-200 text-gray-500 rounded-2xl font-bold text-xs hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="flex-1 px-6 py-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-2xl font-black text-xs shadow-lg shadow-emerald-200 transition-all duration-200">
                        Solicitar Herramienta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Improve touch targets for mobile */
    @media (max-width: 640px) {
        .touch-manipulation {
            min-height: 44px;
        }
    }
</style>

<script>
function showSection(section) {
    // Update button styles
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('bg-emerald-600', 'text-white', 'shadow-sm');
        btn.classList.add('text-gray-600', 'hover:bg-gray-100');
    });
    
    const activeBtn = document.querySelector(`[data-tab="${section}"]`);
    activeBtn.classList.add('bg-emerald-600', 'text-white', 'shadow-sm');
    activeBtn.classList.remove('text-gray-600', 'hover:bg-gray-100');
    
    // Show/hide sections
    document.querySelectorAll('.section').forEach(sec => {
        sec.classList.add('hidden');
    });
    document.getElementById(`${section}-section`).classList.remove('hidden');
}

function openRequestModal() {
    const button = event.target.closest('button');
    const toolId = button?.getAttribute('data-tool-id');
    const toolName = button?.getAttribute('data-tool-name');
    
    if (toolId) {
        document.getElementById('tool_id').value = toolId;
        // Trigger change event to update quantity field
        document.getElementById('tool_id').dispatchEvent(new Event('change'));
    } else {
        document.getElementById('requestForm').reset();
    }
    document.getElementById('requestModal').classList.remove('hidden');
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

function closeRequestModal() {
    document.getElementById('requestModal').classList.add('hidden');
    document.getElementById('requestForm').reset();
    
    // Restore body scroll
    document.body.style.overflow = '';
}

// Set default due date to 7 days from now and handle quantity updates
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const nextWeek = new Date(today.getTime() + (7 * 24 * 60 * 60 * 1000));
    document.getElementById('due_at').value = nextWeek.toISOString().split('T')[0];
    
    // Update quantity max when tool changes
    document.getElementById('tool_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const maxQuantity = selectedOption.dataset.max;
        const quantityInput = document.getElementById('quantity');
        const quantityHelp = document.getElementById('quantity-help');
        
        if (maxQuantity) {
            quantityInput.max = maxQuantity;
            quantityInput.value = 1;
            quantityHelp.innerHTML = `<i data-lucide="info" class="w-3.5 h-3.5"></i> Máximo ${maxQuantity} herramientas disponibles`;
            quantityInput.disabled = false;
            // Re-initialize lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        } else {
            quantityInput.value = '';
            quantityInput.max = 1;
            quantityHelp.innerHTML = '<i data-lucide="info" class="w-3.5 h-3.5"></i> Selecciona una herramienta para ver la cantidad disponible';
            quantityInput.disabled = true;
            // Re-initialize lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    });
});

// Close modal when clicking outside
document.getElementById('requestModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRequestModal();
    }
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRequestModal();
        closeLoanDetailsModal();
    }
});

// ---- Loan Details Modal ----
function openLoanDetailsModal(btn) {
    const d = btn.dataset;

    document.getElementById('loan-detail-tool').textContent     = d.toolName  || '—';
    document.getElementById('loan-detail-quantity').textContent  = d.quantity  || '—';
    document.getElementById('loan-detail-requested').textContent = d.requested || '—';

    const statusEl = document.getElementById('loan-detail-status');
    statusEl.textContent = d.status || '—';
    statusEl.className   = 'text-[10px] font-black px-2.5 py-1 rounded-full ' + (d.statusClass || '');

    const taskRow = document.getElementById('loan-detail-task-row');
    if (d.taskName) {
        document.getElementById('loan-detail-task').textContent = d.taskName;
        taskRow.style.display = 'block';
    } else {
        taskRow.style.display = 'none';
    }

    const outRow = document.getElementById('loan-detail-out-row');
    const dueRow = document.getElementById('loan-detail-due-row');
    const retRow = document.getElementById('loan-detail-ret-row');
    outRow.style.display = d.outAt       ? 'flex' : 'none';
    dueRow.style.display = d.dueAt       ? 'flex' : 'none';
    retRow.style.display = d.returnedAt  ? 'flex' : 'none';
    document.getElementById('loan-detail-out').textContent = d.outAt      || '';
    document.getElementById('loan-detail-due').textContent = d.dueAt      || '';
    document.getElementById('loan-detail-ret').textContent = d.returnedAt || '';

    const notesSection = document.getElementById('loan-detail-notes-section');
    if (d.notes && d.notes.trim()) {
        document.getElementById('loan-detail-notes').textContent = d.notes;
        notesSection.style.display = 'block';
    } else {
        notesSection.style.display = 'none';
    }

    const returnBtn = document.getElementById('loan-detail-return-btn');
    if (d.loanStatus === 'out') {
        returnBtn.style.display = 'block';
        returnBtn.querySelector('form').action = d.returnUrl;
    } else {
        returnBtn.style.display = 'none';
    }

    document.getElementById('loanDetailsModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    if (window.lucide) window.lucide.createIcons();
}

function closeLoanDetailsModal() {
    document.getElementById('loanDetailsModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.getElementById('loanDetailsModal').addEventListener('click', function(e) {
    if (e.target === this) closeLoanDetailsModal();
});

// Polling Section
function refreshToolsData() {
    fetch(window.location.href, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        updateToolsGrid(data.availableTools);
        updateLoansGrid(data.myLoans);
        document.getElementById('loans-pagination').innerHTML = data.pagination;
    })
    .catch(error => console.error('Error refreshing tools data:', error));
}

function updateToolsGrid(tools) {
    const grid = document.getElementById('available-tools-grid');
    if (tools.length === 0) {
        grid.innerHTML = `
            <div class="col-span-full bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="wrench" class="w-8 h-8 text-emerald-600"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">No hay herramientas disponibles</h3>
                <p class="text-gray-600">Todas las herramientas están actualmente en uso.</p>
            </div>`;
    } else {
        grid.innerHTML = tools.map(tool => `
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                <div class="relative h-48 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                    ${tool.photo ? `<img src="${tool.photo}" alt="${tool.name}" class="w-full h-full object-cover">` : 
                    `<div class="w-full h-full flex items-center justify-center"><i data-lucide="wrench" class="w-16 h-16 text-gray-400"></i></div>`}
                    <div class="absolute top-3 right-3">
                        ${tool.status === 'operational' ? 
                        `<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-green-500 text-white text-xs font-semibold shadow-lg"><i data-lucide="check-circle" class="w-3.5 h-3.5"></i>Operacional</span>` : 
                        `<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-yellow-500 text-white text-xs font-semibold shadow-lg"><i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>${tool.status.charAt(0).toUpperCase() + tool.status.slice(1)}</span>`}
                    </div>
                </div>
                <div class="p-4">
                    <h4 class="font-bold text-gray-900 mb-3 text-base truncate">${tool.name}</h4>
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-3">
                            <div class="flex items-center gap-2 mb-1">
                                <i data-lucide="check-square" class="w-4 h-4 text-emerald-600"></i>
                                <span class="text-xs font-medium text-gray-600">Disponibles</span>
                            </div>
                            <p class="text-xl font-bold text-emerald-700">${tool.available_qty}</p>
                        </div>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                            <div class="flex items-center gap-2 mb-1">
                                <i data-lucide="package" class="w-4 h-4 text-gray-600"></i>
                                <span class="text-xs font-medium text-gray-600">Total</span>
                            </div>
                            <p class="text-xl font-bold text-gray-700">${tool.total_entries}</p>
                        </div>
                    </div>
                    <button onclick="openRequestModal()" data-tool-id="${tool.id}" data-tool-name="${tool.name}"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-lg font-semibold text-sm shadow-sm hover:shadow transition-all duration-200 touch-manipulation">
                        <i data-lucide="hand" class="w-4 h-4"></i>
                        <span>Solicitar</span>
                    </button>
                </div>
            </div>`).join('');
    }
    if (window.lucide) window.lucide.createIcons();
}

function updateLoansGrid(loans) {
    const grid = document.getElementById('my-loans-grid');
    if (loans.length === 0) {
        grid.innerHTML = `
            <div class="col-span-full bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="package" class="w-8 h-8 text-emerald-600"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">No tienes préstamos</h3>
                <p class="text-gray-600">No has solicitado ninguna herramienta aún.</p>
            </div>`;
    } else {
        grid.innerHTML = loans.map(loan => `
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                <div class="relative h-40 bg-gray-100 overflow-hidden">
                    ${loan.tool.photo ? `<img src="${loan.tool.photo}" alt="${loan.tool.name}" class="w-full h-full object-cover">` : 
                    `<div class="w-full h-full flex items-center justify-center"><i data-lucide="wrench" class="w-14 h-14 text-gray-300"></i></div>`}
                    <span class="absolute top-2 right-2 inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold ${loan.status_class}">
                        <i data-lucide="${loan.status_icon}" class="w-3 h-3"></i>
                        ${loan.status_text}
                    </span>
                </div>
                <div class="p-4 space-y-2">
                    <h4 class="font-bold text-gray-900 text-sm leading-tight truncate">${loan.tool.name}</h4>
                    <div class="flex items-center gap-1 text-xs text-gray-700">
                        <i data-lucide="hash" class="w-3.5 h-3.5 text-gray-500"></i>
                        <span>Cantidad: <strong>${loan.quantity}</strong></span>
                    </div>
                    ${loan.task_name ? `
                    <div class="flex items-center gap-1 text-xs text-blue-700 bg-blue-50 px-2 py-1 rounded-md border border-blue-100">
                        <i data-lucide="briefcase" class="w-3 h-3"></i>
                        <span class="truncate">${loan.task_name}</span>
                    </div>` : ''}
                    <div class="flex items-center gap-1 text-xs text-gray-700">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-500"></i>
                        <span>Solicitada: ${loan.created_at}</span>
                    </div>
                    <div class="space-y-2 mt-3">
                        ${loan.status === 'out' ? `
                        <form method="POST" action="${loan.return_url}" class="w-full" onsubmit="return confirm('¿Confirmar devolución de esta herramienta?');">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-1.5 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors">
                                <i data-lucide="arrow-left-circle" class="w-4 h-4"></i>
                                Devolver Herramienta
                            </button>
                        </form>
                        ` : ''}
                        
                        <button
                            onclick="openLoanDetailsModal(this)"
                            data-tool-name="${loan.tool.name}"
                            data-status="${loan.status_text}"
                            data-status-class="${loan.status_class}"
                            data-quantity="${loan.quantity}"
                            data-requested="${loan.created_at}"
                            data-out-at="${loan.out_at || ''}"
                            data-due-at="${loan.due_at || ''}"
                            data-returned-at="${loan.returned_at || ''}"
                            data-notes="${loan.request_notes || ''}"
                            data-loan-status="${loan.status}"
                            data-loan-id="${loan.id}"
                            data-task-name="${loan.task_name || ''}"
                            data-return-url="${loan.return_url}"
                            class="w-full flex items-center justify-center gap-1.5 mt-1 px-3 py-2 border border-gray-200 rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                            Ver todos los detalles
                        </button>
                    </div>
                </div>
            </div>`).join('');
    }
    if (window.lucide) window.lucide.createIcons();
}

// Start polling
setInterval(refreshToolsData, 3000);

</script>
@endsection
