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
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($availableTools as $tool)
                        <div class="border border-emerald-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-3">
                                <h4 class="font-semibold text-emerald-800">{{ $tool->name }}</h4>
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $tool->status === 'operational' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($tool->status) }}
                                </span>
                            </div>
                            
                            <div class="space-y-2 text-sm text-emerald-600">
                                <div class="flex justify-between">
                                    <span>Disponibles:</span>
                                    <span class="font-medium">{{ $tool->available_qty }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Total:</span>
                                    <span class="font-medium">{{ $tool->total_qty }}</span>
                                </div>
                                @if($tool->description)
                                    <div class="mt-2 p-2 bg-emerald-50 rounded text-xs">
                                        {{ $tool->description }}
                                    </div>
                                @endif
                            </div>
                            
                            <button onclick="openRequestModal()" 
                                    data-tool-id="{{ $tool->id }}"
                                    data-tool-name="{{ $tool->name }}"
                                    class="w-full mt-3 px-3 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors text-sm">
                                Solicitar
                            </button>
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
                <div class="space-y-4">
                    @foreach($myLoans as $loan)
                        <div class="border border-emerald-200 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h4 class="font-semibold text-emerald-800">{{ $loan->tool->name }}</h4>
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            {{ $loan->status === 'out' ? 'bg-blue-100 text-blue-800' : 
                                               ($loan->status === 'returned' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ $loan->status === 'out' ? 'Prestada' : ucfirst($loan->status) }}
                                        </span>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-emerald-600">
                                        <div>
                                            <span class="font-medium">Prestada:</span> {{ $loan->out_at->format('d/m/Y H:i') }}
                                        </div>
                                        @if($loan->due_at)
                                            <div>
                                                <span class="font-medium">Vence:</span> {{ $loan->due_at->format('d/m/Y H:i') }}
                                            </div>
                                        @endif
                                        @if($loan->returned_at)
                                            <div>
                                                <span class="font-medium">Devuelta:</span> {{ $loan->returned_at->format('d/m/Y H:i') }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    @if($loan->condition_return)
                                        <div class="mt-3 p-2 bg-emerald-50 rounded text-sm">
                                            <span class="font-medium text-emerald-700">Condición al devolver:</span>
                                            <span class="text-emerald-600">{{ $loan->condition_return }}</span>
                                        </div>
                                    @endif
                                </div>
                                
                                @if($loan->status === 'out')
                                    <div class="ml-4">
                                        <form method="POST" action="{{ route('worker.tools.return', $loan) }}" 
                                              data-confirm="true" data-message="¿Confirmar devolución de esta herramienta?">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors">
                                                <i data-lucide="undo" class="w-4 h-4 inline mr-1"></i>
                                                Devolver
                                            </button>
                                        </form>
                                    </div>
                                @endif
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
