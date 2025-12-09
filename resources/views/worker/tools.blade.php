@extends('worker.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Gestión de Herramientas</h2>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Tabs -->
    <div class="bg-white border rounded p-4">
        <div class="flex space-x-1 bg-emerald-50 p-1 rounded-lg">
            <button onclick="showSection('available')" class="tab-btn px-4 py-2 rounded-md text-sm font-medium transition-colors bg-emerald-600 text-white" data-tab="available">
                Herramientas Disponibles
            </button>
            <button onclick="showSection('my-loans')" class="tab-btn px-4 py-2 rounded-md text-sm font-medium transition-colors text-emerald-700 hover:bg-emerald-100" data-tab="my-loans">
                Mis Préstamos
            </button>
        </div>
    </div>

    <!-- Available Tools Section -->
    <div id="available-section" class="section">
        <div class="bg-white border rounded p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-emerald-700">Herramientas Disponibles</h3>
                <button onclick="openRequestModal()" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors">
                    <i data-lucide="plus" class="w-4 h-4 inline mr-1"></i>
                    Solicitar Herramienta
                </button>
            </div>

            @if($availableTools->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($availableTools as $tool)
                        <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-all duration-200 bg-white">
                            <!-- Imagen -->
                            <div class="relative h-40 bg-gray-100 overflow-hidden">
                                @if($tool->photo)
                                    <img src="{{ asset('storage/' . $tool->photo) }}" 
                                         alt="{{ $tool->name }}" 
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100">
                                        <i data-lucide="image" class="w-12 h-12 text-gray-300"></i>
                                    </div>
                                @endif
                                <!-- Badge de estado -->
                                <div class="absolute top-2 right-2">
                                    <span class="px-2 py-1 text-xs font-medium rounded-md shadow-sm
                                        {{ $tool->status === 'operational' ? 'bg-green-500 text-white' : 'bg-yellow-500 text-white' }}">
                                        {{ ucfirst($tool->status) }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Contenido -->
                            <div class="p-3">
                                <h4 class="font-semibold text-gray-900 mb-2 text-sm truncate">{{ $tool->name }}</h4>
                                
                                <div class="space-y-1.5 mb-3">
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-gray-600">Disponibles:</span>
                                        <span class="font-semibold text-emerald-600">{{ $tool->available_qty }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-gray-600">Total:</span>
                                        <span class="font-semibold text-gray-700">{{ $tool->total_entries }}</span>
                                    </div>
                                </div>
                                
                                <button onclick="openRequestModal()" 
                                        data-tool-id="{{ $tool->id }}"
                                        data-tool-name="{{ $tool->name }}"
                                        class="w-full px-3 py-1.5 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 transition-colors text-xs font-medium shadow-sm">
                                    Solicitar
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i data-lucide="wrench" class="w-12 h-12 text-emerald-400 mx-auto mb-4"></i>
                    <h3 class="text-lg font-semibold text-emerald-700 mb-2">No hay herramientas disponibles</h3>
                    <p class="text-emerald-600">Todas las herramientas están actualmente en uso.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- My Loans Section -->
    <div id="my-loans-section" class="section hidden">
        <div class="bg-white border rounded p-6">
            <h3 class="text-lg font-semibold text-emerald-700 mb-6">Mis Préstamos de Herramientas</h3>

            @if($myLoans->count() > 0)
                <div class="space-y-3">
                    @foreach($myLoans as $loan)
                        <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow bg-white">
                            <div class="flex">
                                <!-- Imagen -->
                                <div class="w-24 h-24 flex-shrink-0 bg-gray-100 overflow-hidden">
                                    @if($loan->tool->photo)
                                        <img src="{{ asset('storage/' . $loan->tool->photo) }}" 
                                             alt="{{ $loan->tool->name }}" 
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100">
                                            <i data-lucide="image" class="w-8 h-8 text-gray-300"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Contenido -->
                                <div class="flex-1 p-3">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <h4 class="font-semibold text-gray-900 text-sm">{{ $loan->tool->name }}</h4>
                                                <span class="px-2 py-0.5 text-xs font-medium rounded-md
                                                    {{ $loan->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                       ($loan->status === 'approved' ? 'bg-blue-100 text-blue-800' : 
                                                       ($loan->status === 'rejected' ? 'bg-red-100 text-red-800' : 
                                                       ($loan->status === 'out' ? 'bg-blue-100 text-blue-800' : 
                                                       ($loan->status === 'returned' || $loan->status === 'returned_by_worker' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')))) }}">
                                                    @if($loan->status === 'pending')
                                                        Pendiente
                                                    @elseif($loan->status === 'approved')
                                                        Aprobado
                                                    @elseif($loan->status === 'rejected')
                                                        Rechazado
                                                    @elseif($loan->status === 'out')
                                                        Prestada
                                                    @elseif($loan->status === 'returned_by_worker')
                                                        Devuelta (Pendiente)
                                                    @else
                                                        {{ ucfirst($loan->status) }}
                                                    @endif
                                                </span>
                                            </div>
                                            
                                            <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-600 mb-2">
                                                @if($loan->status === 'pending')
                                                    <span><span class="font-medium">Solicitada:</span> {{ $loan->created_at->format('d/m/Y H:i') }}</span>
                                                @elseif($loan->out_at)
                                                    <span><span class="font-medium">Prestada:</span> {{ $loan->out_at->format('d/m/Y H:i') }}</span>
                                                @endif
                                                @if($loan->due_at)
                                                    <span><span class="font-medium">Vence:</span> {{ $loan->due_at->format('d/m/Y') }}</span>
                                                @endif
                                                @if($loan->returned_at)
                                                    <span><span class="font-medium">Devuelta:</span> {{ $loan->returned_at->format('d/m/Y H:i') }}</span>
                                                @endif
                                            </div>
                                            
                                            @if($loan->request_notes)
                                                <p class="text-xs text-gray-600 line-clamp-2">{{ $loan->request_notes }}</p>
                                            @endif
                                        </div>
                                        
                                        <!-- Acciones -->
                                        <div class="flex-shrink-0 ml-3">
                                            @if($loan->status === 'out')
                                                <form method="POST" action="{{ route('worker.tools.return', $loan) }}" 
                                                      data-confirm="true" data-message="¿Confirmar devolución de esta herramienta?">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 transition-colors text-xs font-medium shadow-sm">
                                                        <i data-lucide="undo" class="w-3 h-3 inline mr-1"></i>
                                                        Devolver
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($myLoans->hasPages())
                    <div class="mt-6">
                        {{ $myLoans->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-8">
                    <i data-lucide="arrow-left-right" class="w-12 h-12 text-emerald-400 mx-auto mb-4"></i>
                    <h3 class="text-lg font-semibold text-emerald-700 mb-2">No tienes préstamos</h3>
                    <p class="text-emerald-600">No has solicitado ninguna herramienta aún.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Request Tool Modal -->
<div id="requestModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold text-emerald-700 mb-4">Solicitar Herramienta</h3>
            <form id="requestForm" method="POST" action="{{ route('worker.tools.request') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="tool_id" class="block text-sm font-medium text-emerald-700 mb-1">Herramienta</label>
                        <select id="tool_id" name="tool_id" required
                                class="w-full border border-emerald-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">Seleccionar herramienta...</option>
                            @foreach($availableTools as $tool)
                                <option value="{{ $tool->id }}" data-max="{{ $tool->available_qty }}">
                                    {{ $tool->name }} ({{ $tool->available_qty }} disponibles)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-emerald-700 mb-1">Cantidad</label>
                        <input type="number" id="quantity" name="quantity" min="1" max="1" required
                               class="w-full border border-emerald-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="1">
                        <p id="quantity-help" class="text-xs text-emerald-600 mt-1">Selecciona una herramienta para ver la cantidad disponible</p>
                    </div>
                    
                    <div>
                        <label for="due_at" class="block text-sm font-medium text-emerald-700 mb-1">Fecha de devolución (opcional)</label>
                        <input type="date" id="due_at" name="due_at"
                               class="w-full border border-emerald-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    
                    <div>
                        <label for="request_notes" class="block text-sm font-medium text-emerald-700 mb-1">Notas (opcional)</label>
                        <textarea id="request_notes" name="request_notes" rows="3"
                                  class="w-full border border-emerald-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                  placeholder="Información adicional sobre la solicitud..."></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeRequestModal()" 
                            class="px-4 py-2 border border-emerald-300 text-emerald-700 rounded hover:bg-emerald-50">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">
                        Solicitar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showSection(section) {
    // Update button styles
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('bg-emerald-600', 'text-white');
        btn.classList.add('text-emerald-700', 'hover:bg-emerald-100');
    });
    
    document.querySelector(`[data-tab="${section}"]`).classList.add('bg-emerald-600', 'text-white');
    document.querySelector(`[data-tab="${section}"]`).classList.remove('text-emerald-700', 'hover:bg-emerald-100');
    
    // Show/hide sections
    document.querySelectorAll('.section').forEach(sec => {
        sec.classList.add('hidden');
    });
    document.getElementById(`${section}-section`).classList.remove('hidden');
}

function openRequestModal() {
    const button = event.target;
    const toolId = button.getAttribute('data-tool-id');
    const toolName = button.getAttribute('data-tool-name');
    
    if (toolId) {
        document.getElementById('tool_id').value = toolId;
        // Trigger change event to update quantity field
        document.getElementById('tool_id').dispatchEvent(new Event('change'));
    } else {
        document.getElementById('requestForm').reset();
    }
    document.getElementById('requestModal').classList.remove('hidden');
}

function closeRequestModal() {
    document.getElementById('requestModal').classList.add('hidden');
    document.getElementById('requestForm').reset();
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
            quantityHelp.textContent = `Máximo ${maxQuantity} herramientas disponibles`;
            quantityInput.disabled = false;
        } else {
            quantityInput.value = '';
            quantityInput.max = 1;
            quantityHelp.textContent = 'Selecciona una herramienta para ver la cantidad disponible';
            quantityInput.disabled = true;
        }
    });
});

// Close modal when clicking outside
document.getElementById('requestModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRequestModal();
    }
});
</script>
@endsection
