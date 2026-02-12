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

        @if($availableTools->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($availableTools as $tool)
                    <!-- Modern Tool Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                        <!-- Image -->
                        <div class="relative h-48 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                            @if($tool->photo)
                                <img src="{{ asset('storage/' . $tool->photo) }}" 
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
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="wrench" class="w-8 h-8 text-emerald-600"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">No hay herramientas disponibles</h3>
                <p class="text-gray-600">Todas las herramientas están actualmente en uso.</p>
            </div>
        @endif
    </div>

    <!-- My Loans Section -->
    <div id="my-loans-section" class="section hidden">
        @if($myLoans->count() > 0)
            <div class="space-y-4">
                @foreach($myLoans as $loan)
                    <!-- Modern Loan Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                        <div class="flex flex-col sm:flex-row">
                            <!-- Image -->
                            <div class="w-full sm:w-32 h-32 flex-shrink-0 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                                @if($loan->tool->photo)
                                    <img src="{{ asset('storage/' . $loan->tool->photo) }}" 
                                         alt="{{ $loan->tool->name }}" 
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i data-lucide="wrench" class="w-10 h-10 text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 p-5">
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-3">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <h4 class="font-bold text-gray-900 text-base">{{ $loan->tool->name }}</h4>
                                        </div>
                                        
                                        <!-- Status Badge -->
                                        @php
                                            $statusConfig = [
                                                'pending' => ['color' => 'yellow', 'icon' => 'clock', 'text' => 'Pendiente'],
                                                'approved' => ['color' => 'blue', 'icon' => 'check', 'text' => 'Aprobado'],
                                                'rejected' => ['color' => 'red', 'icon' => 'x-circle', 'text' => 'Rechazado'],
                                                'out' => ['color' => 'blue', 'icon' => 'arrow-right-circle', 'text' => 'Prestada'],
                                                'returned_by_worker' => ['color' => 'green', 'icon' => 'arrow-left-circle', 'text' => 'Devuelta (Pendiente)'],
                                                'returned' => ['color' => 'green', 'icon' => 'check-circle', 'text' => 'Devuelta'],
                                            ];
                                            $config = $statusConfig[$loan->status] ?? ['color' => 'gray', 'icon' => 'circle', 'text' => ucfirst($loan->status)];
                                        @endphp
                                        
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-{{ $config['color'] }}-100 text-{{ $config['color'] }}-800 text-sm font-semibold">
                                            <i data-lucide="{{ $config['icon'] }}" class="w-4 h-4"></i>
                                            {{ $config['text'] }}
                                        </span>
                                    </div>
                                    
                                    <!-- Action Button -->
                                    @if($loan->status === 'out')
                                        <form method="POST" action="{{ route('worker.tools.return', $loan) }}" 
                                              data-confirm="true" data-message="¿Confirmar devolución de esta herramienta?">
                                            @csrf
                                            <button type="submit" 
                                                    class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-lg font-semibold text-sm shadow-sm hover:shadow transition-all duration-200 touch-manipulation">
                                                <i data-lucide="arrow-left-circle" class="w-4 h-4"></i>
                                                <span>Devolver</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                
                                <!-- Info Grid -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                                    @if($loan->status === 'pending')
                                        <div class="flex items-center gap-2 text-gray-700">
                                            <i data-lucide="calendar-plus" class="w-4 h-4 text-gray-500"></i>
                                            <span><span class="font-medium">Solicitada:</span> {{ $loan->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    @elseif($loan->out_at)
                                        <div class="flex items-center gap-2 text-gray-700">
                                            <i data-lucide="calendar-check" class="w-4 h-4 text-gray-500"></i>
                                            <span><span class="font-medium">Prestada:</span> {{ $loan->out_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    @endif
                                    
                                    @if($loan->due_at)
                                        <div class="flex items-center gap-2 text-gray-700">
                                            <i data-lucide="calendar-clock" class="w-4 h-4 text-gray-500"></i>
                                            <span><span class="font-medium">Vence:</span> {{ $loan->due_at->format('d/m/Y') }}</span>
                                        </div>
                                    @endif
                                    
                                    @if($loan->returned_at)
                                        <div class="flex items-center gap-2 text-gray-700">
                                            <i data-lucide="calendar-check-2" class="w-4 h-4 text-gray-500"></i>
                                            <span><span class="font-medium">Devuelta:</span> {{ $loan->returned_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    @endif
                                </div>
                                
                                @if($loan->request_notes)
                                    <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
                                        <div class="flex items-start gap-2">
                                            <i data-lucide="message-square" class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5"></i>
                                            <p class="text-sm text-blue-800">{{ $loan->request_notes }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($myLoans->hasPages())
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mt-4">
                    {{ $myLoans->links() }}
                </div>
            @endif
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
                        <label for="tool_id" class="block text-sm font-semibold text-gray-900 mb-2">
                            Herramienta <span class="text-red-500">*</span>
                        </label>
                        <select id="tool_id" name="tool_id" required
                                class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-base">
                            <option value="">Seleccionar herramienta...</option>
                            @foreach($availableTools as $tool)
                                <option value="{{ $tool->id }}" data-max="{{ $tool->available_qty }}">
                                    {{ $tool->name }} ({{ $tool->available_qty }} disponibles)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Quantity -->
                    <div>
                        <label for="quantity" class="block text-sm font-semibold text-gray-900 mb-2">
                            Cantidad <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="quantity" name="quantity" min="1" max="1" required
                               class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-base"
                               placeholder="1">
                        <p id="quantity-help" class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                            <i data-lucide="info" class="w-3.5 h-3.5"></i>
                            Selecciona una herramienta para ver la cantidad disponible
                        </p>
                    </div>
                    
                    <!-- Due Date -->
                    <div>
                        <label for="due_at" class="block text-sm font-semibold text-gray-900 mb-2">
                            Fecha de devolución (opcional)
                        </label>
                        <input type="date" id="due_at" name="due_at"
                               class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-base">
                    </div>
                    
                    <!-- Notes -->
                    <div>
                        <label for="request_notes" class="block text-sm font-semibold text-gray-900 mb-2">
                            Notas (opcional)
                        </label>
                        <textarea id="request_notes" name="request_notes" rows="4"
                                  class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-base resize-none"
                                  placeholder="Información adicional sobre la solicitud..."></textarea>
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="flex flex-col-reverse sm:flex-row gap-3 mt-6 pt-6 border-t border-gray-200">
                    <button type="button" 
                            onclick="closeRequestModal()" 
                            class="flex-1 px-6 py-4 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors touch-manipulation">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="flex-1 px-6 py-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-xl font-bold shadow-sm hover:shadow-md transition-all duration-200 touch-manipulation">
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
    }
});
</script>
@endsection
