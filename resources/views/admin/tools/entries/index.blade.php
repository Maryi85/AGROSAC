@php
    $layout = route_prefix() === 'foreman.' ? 'foreman.layout' : 'admin.layout';
@endphp
@extends($layout)

@section('title', 'Entradas de Herramientas')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Entradas de Herramientas</h2>
</div>
@endsection

@section('content')
<!-- Filtros -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex items-end justify-between gap-4 mb-4">
        <h3 class="text-lg font-semibold text-emerald-700">Filtrar Entradas</h3>
        <a href="{{ route(route_prefix() . 'tool-entries.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded-lg font-medium transition-colors">
            <i data-lucide="plus" class="w-5 h-5"></i>
            <span>Nueva Entrada</span>
        </a>
    </div>
    
    <div class="mb-4">
        <x-search-bar placeholder="Buscar por herramienta..." />
    </div>
</div>

<!-- Tabla de entradas -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-emerald-800 border-b bg-gray-50">
                    <th class="py-3 px-4">Fecha</th>
                    <th class="py-3 px-4">Foto</th>
                    <th class="py-3 px-4">Herramienta</th>
                    <th class="py-3 px-4">Tipo</th>
                    <th class="py-3 px-4">Cantidad</th>
                    <th class="py-3 px-4">Costo Unitario</th>
                    <th class="py-3 px-4">Total</th>

                    <th class="py-3 px-4">Registrado por</th>
                    <th class="py-3 px-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($entries as $entry)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4">
                        <div class="font-medium text-gray-900">{{ $entry->entry_date->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $entry->created_at->format('H:i') }}</div>
                    </td>
                    <td class="py-3 px-4">
                        @if($entry->tool->photo)
                            <img src="{{ asset('storage/' . $entry->tool->photo) }}" alt="{{ $entry->tool->name }}" class="w-12 h-12 rounded-full object-cover border border-gray-200">
                        @else
                            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                <i data-lucide="image" class="w-5 h-5"></i>
                            </div>
                        @endif
                    </td>
                    <td class="py-3 px-4">
                        <div class="font-medium text-gray-900">{{ $entry->tool->name }}</div>
                        <div class="text-xs text-gray-500">{{ $entry->tool->category }}</div>
                    </td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-1 text-xs rounded {{ $entry->type === 'purchase' ? 'bg-blue-100 text-blue-700' : ($entry->type === 'donation' ? 'bg-green-100 text-green-700' : ($entry->type === 'transfer' ? 'bg-purple-100 text-purple-700' : 'bg-orange-100 text-orange-700')) }}">
                            {{ $types[$entry->type] }}
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <div class="font-medium text-gray-900">{{ number_format($entry->quantity) }}</div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="text-sm text-gray-600">
                            @if($entry->unit_cost)
                                ${{ number_format($entry->unit_cost, 2) }}
                            @else
                                —
                            @endif
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="font-semibold text-gray-900">
                            @if($entry->total_cost)
                                ${{ number_format($entry->total_cost, 2) }}
                            @else
                                —
                            @endif
                        </div>
                    </td>

                    <td class="py-3 px-4">
                        <div class="text-sm text-gray-600">{{ $entry->createdBy->name }}</div>
                    </td>
                    <td class="py-3 px-4 text-right">
                        <div class="flex items-center gap-1 justify-end">
                            <!-- Editar -->
                            <button type="button"
                                class="inline-flex items-center justify-center w-8 h-8 border border-emerald-200 rounded hover:bg-emerald-50 text-emerald-600 edit-entry-btn" 
                                data-entry-id="{{ $entry->id }}"
                                data-entry-tool-id="{{ $entry->tool_id }}"
                                data-entry-quantity="{{ $entry->quantity }}"
                                data-entry-type="{{ $entry->type }}"
                                data-entry-date="{{ $entry->entry_date->format('Y-m-d') }}"
                                data-entry-unit-cost="{{ $entry->unit_cost ?? '' }}"
                                data-entry-notes="{{ $entry->notes ?? '' }}"
                                title="Editar">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </button>
                            
                            <!-- Eliminar -->
                            <form method="POST" action="{{ route(route_prefix() . 'tool-entries.destroy', $entry) }}" class="inline" 
                                  data-confirm="true" data-message="¿Eliminar esta entrada? Esta acción no se puede deshacer.">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center justify-center w-8 h-8 border border-red-200 rounded hover:bg-red-50 text-red-600" 
                                        title="Eliminar">
                                    <i data-lucide="trash" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="py-6 text-center text-emerald-800/70">No hay entradas registradas</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    @if($entries->hasPages())
    <div class="px-6 py-4 border-t bg-gray-50">
        {{ $entries->links() }}
    </div>
    @endif
</div>

<!-- Modal de edición -->
<div id="editEntryModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 overflow-y-auto" style="display: none;">
    <div class="bg-white border rounded-lg p-6 w-full max-w-2xl mx-4 my-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-emerald-700">Editar Entrada de Herramienta</h3>
            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form id="editEntryForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="editEntryId" name="entry_id">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Herramienta -->
                <div class="md:col-span-2">
                    <label for="editToolId" class="block text-sm font-medium text-gray-700 mb-2">
                        Herramienta <span class="text-red-500">*</span>
                    </label>
                    <select name="tool_id" id="editToolId" required 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Seleccionar herramienta</option>
                        @foreach($tools as $tool)
                            <option value="{{ $tool->id }}">{{ $tool->name }} - {{ $tool->category }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Cantidad -->
                <div>
                    <label for="editQuantity" class="block text-sm font-medium text-gray-700 mb-2">
                        Cantidad <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="quantity" id="editQuantity" min="1" required 
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <!-- Tipo de entrada -->
                <div>
                    <label for="editType" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de Entrada <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="editType" required 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Seleccionar tipo</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Fecha de entrada -->
                <div>
                    <label for="editEntryDate" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha de Entrada <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="entry_date" id="editEntryDate" required 
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <!-- Costo unitario -->
                <div>
                    <label for="editUnitCost" class="block text-sm font-medium text-gray-700 mb-2">
                        Costo Unitario
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">$</span>
                        <input type="number" name="unit_cost" id="editUnitCost" step="0.01" min="0" 
                               class="w-full border border-gray-300 rounded-md pl-8 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <!-- Notas -->
                <div class="md:col-span-2">
                    <label for="editNotes" class="block text-sm font-medium text-gray-700 mb-2">
                        Notas Adicionales
                    </label>
                    <textarea name="notes" id="editNotes" rows="3" maxlength="1000"
                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                              placeholder="Información adicional sobre la entrada..."></textarea>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex items-center gap-2 justify-end pt-4 border-t">
                <button type="button" onclick="closeEditModal()" 
                        class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition-colors">
                    <i data-lucide="x" class="w-4 h-4 inline mr-2"></i>
                    Cancelar
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded transition-colors">
                    <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Abrir modal de edición
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.edit-entry-btn');
        const form = document.getElementById('editEntryForm');
        
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const entryId = this.dataset.entryId;
                const toolId = this.dataset.entryToolId;
                const quantity = this.dataset.entryQuantity;
                const type = this.dataset.entryType;
                const date = this.dataset.entryDate;
                const unitCost = this.dataset.entryUnitCost;
                const notes = this.dataset.entryNotes;
                
                // Configurar action del formulario
                const routePrefix = '{{ route_prefix() === 'foreman.' ? 'foreman' : 'admin' }}';
                form.action = `/${routePrefix}/tool-entries/${entryId}`;

                // Llenar el formulario
                document.getElementById('editEntryId').value = entryId;
                document.getElementById('editToolId').value = toolId;
                document.getElementById('editQuantity').value = quantity;
                document.getElementById('editType').value = type;
                document.getElementById('editEntryDate').value = date;
                document.getElementById('editUnitCost').value = unitCost;
                document.getElementById('editNotes').value = notes;
                
                // Mostrar el modal
                document.getElementById('editEntryModal').style.display = 'flex';
                
                // Re-inicializar los iconos de lucide
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            });
        });
    });

// Cerrar modal de edición
function closeEditModal() {
    document.getElementById('editEntryModal').style.display = 'none';
}

// Cerrar modal al hacer clic fuera
document.addEventListener('click', function(e) {
    const modal = document.getElementById('editEntryModal');
    if (e.target === modal) {
        closeEditModal();
    }
});
</script>
@endsection
