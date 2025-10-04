@extends('admin.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Gestión Contable</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-4">
    @if (session('status'))
        <div class="mb-4 p-3 bg-emerald-100 border border-emerald-300 text-emerald-700 rounded">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Botones de acción -->
    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('admin.ledger.dashboard') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
            <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.ledger.create') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Nuevo Movimiento</span>
        </a>
    </div>

    <!-- Filtros de búsqueda -->
    <form method="GET" class="mb-4 flex gap-2 items-end flex-wrap">
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Tipo</label>
            <select name="type" class="border border-emerald-200 rounded px-3 py-2">
                <option value="all">Todos los tipos</option>
                @foreach($types as $key => $label)
                    <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Categoría</label>
            <select name="category" class="border border-emerald-200 rounded px-3 py-2">
                <option value="all">Todas las categorías</option>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Cultivo</label>
            <select name="crop_id" class="border border-emerald-200 rounded px-3 py-2">
                <option value="all">Todos los cultivos</option>
                @foreach($crops as $crop)
                    <option value="{{ $crop->id }}" {{ request('crop_id') == $crop->id ? 'selected' : '' }}>
                        {{ $crop->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Lote</label>
            <select name="plot_id" class="border border-emerald-200 rounded px-3 py-2">
                <option value="all">Todos los lotes</option>
                @foreach($plots as $plot)
                    <option value="{{ $plot->id }}" {{ request('plot_id') == $plot->id ? 'selected' : '' }}>
                        {{ $plot->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Desde</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="border border-emerald-200 rounded px-3 py-2" />
        </div>
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Hasta</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="border border-emerald-200 rounded px-3 py-2" />
        </div>
        <button type="submit" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded inline-flex items-center gap-2">
            <i data-lucide="search" class="w-4 h-4"></i>
            <span>Filtrar</span>
        </button>
    </form>

    <!-- Tabla de movimientos -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-emerald-800 border-b">
                    <th class="py-3 pr-4">Fecha</th>
                    <th class="py-3 pr-4">Tipo</th>
                    <th class="py-3 pr-4">Categoría</th>
                    <th class="py-3 pr-4">Monto</th>
                    <th class="py-3 pr-4">Cultivo</th>
                    <th class="py-3 pr-4">Lote</th>
                    <th class="py-3 pr-4">Referencia</th>
                    <th class="py-3 pr-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($entries as $entry)
                <tr class="border-b hover:bg-gray-50" data-entry-id="{{ $entry->id }}">
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-900">{{ $entry->occurred_at->format('d/m/Y') }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="px-2 py-1 text-xs rounded {{ $entry->type === 'income' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                            {{ $entry->type === 'income' ? 'Ingreso' : 'Gasto' }}
                        </span>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-900">{{ $categories[$entry->category] }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm font-medium {{ $entry->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $entry->type === 'income' ? '+' : '-' }}${{ number_format($entry->amount, 2) }}
                        </div>
                    </td>
                    <td class="py-3 pr-4">
                        @if($entry->crop)
                            <div class="text-sm text-gray-900">{{ $entry->crop->name }}</div>
                        @else
                            <div class="text-sm text-gray-500">—</div>
                        @endif
                    </td>
                    <td class="py-3 pr-4">
                        @if($entry->plot)
                            <div class="text-sm text-gray-900">{{ $entry->plot->name }}</div>
                        @else
                            <div class="text-sm text-gray-500">—</div>
                        @endif
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-900">{{ $entry->reference ?? '—' }}</div>
                    </td>
                    <td class="py-3 pr-4 text-right">
                        <div class="flex items-center gap-1 justify-end">
                            <!-- Ver detalles -->
                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 border border-blue-200 rounded hover:bg-blue-50 text-blue-600 view-entry-btn" 
                                    data-entry-id="{{ $entry->id }}"
                                    data-type="{{ $entry->type }}"
                                    data-category="{{ $entry->category }}"
                                    data-amount="{{ $entry->amount }}"
                                    data-occurred-at="{{ $entry->occurred_at->format('d/m/Y') }}"
                                    data-crop-name="{{ $entry->crop ? $entry->crop->name : 'No especificado' }}"
                                    data-plot-name="{{ $entry->plot ? $entry->plot->name : 'No especificado' }}"
                                    data-reference="{{ $entry->reference ?? 'Sin referencia' }}"
                                    data-created="{{ $entry->created_at->format('d/m/Y H:i') }}"
                                    title="Ver detalles">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                            
                            <!-- Editar -->
                            <a href="{{ route('admin.ledger.edit', $entry) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 border border-emerald-200 rounded hover:bg-emerald-50 text-emerald-600" 
                               title="Editar">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                            
                            <!-- Eliminar -->
                            <form method="POST" action="{{ route('admin.ledger.destroy', $entry) }}" class="inline" data-confirm="true" data-message="¿Eliminar este movimiento contable? Esta acción no se puede deshacer.">
                                @csrf
                                @method('DELETE')
                                <button class="inline-flex items-center justify-center w-8 h-8 border border-red-200 rounded hover:bg-red-50 text-red-600" title="Eliminar">
                                    <i data-lucide="trash" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-6 text-center text-emerald-800/70">No hay movimientos contables registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $entries->links() }}</div>
</div>

<!-- Modal de detalles -->
<div id="viewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-6 w-full max-w-2xl mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-emerald-700">Detalles del Movimiento</h3>
            <button type="button" onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <div class="space-y-6">
            <!-- Información Básica -->
            <div class="bg-gray-50 rounded p-4">
                <h4 class="text-md font-semibold text-emerald-700 mb-3">Información Básica</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo</label>
                        <p id="viewType" class="text-sm mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Categoría</label>
                        <p id="viewCategory" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Monto</label>
                        <p id="viewAmount" class="text-sm font-medium mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha</label>
                        <p id="viewOccurredAt" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                </div>
            </div>
            
            <!-- Información de Asociación -->
            <div class="bg-gray-50 rounded p-4">
                <h4 class="text-md font-semibold text-emerald-700 mb-3">Información de Asociación</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cultivo</label>
                        <p id="viewCropName" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Lote</label>
                        <p id="viewPlotName" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Referencia</label>
                        <p id="viewReference" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                </div>
            </div>
            
            <!-- Información del Sistema -->
            <div class="bg-gray-50 rounded p-4">
                <h4 class="text-md font-semibold text-emerald-700 mb-3">Información del Sistema</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Registrado</label>
                        <p id="viewCreated" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Botón de cerrar -->
        <div class="mt-6 flex justify-end">
            <button type="button" onclick="closeViewModal()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded">
                <i data-lucide="x" class="w-4 h-4 inline mr-2"></i>
                Cerrar
            </button>
        </div>
    </div>
</div>

<script>
// Función para abrir el modal de detalles
function openViewModal(id, type, category, amount, occurredAt, cropName, plotName, reference, created) {
    // Llenar los campos del modal de detalles
    document.getElementById('viewOccurredAt').textContent = occurredAt;
    document.getElementById('viewCategory').textContent = category;
    document.getElementById('viewCropName').textContent = cropName;
    document.getElementById('viewPlotName').textContent = plotName;
    document.getElementById('viewReference').textContent = reference;
    document.getElementById('viewCreated').textContent = created;
    
    // Configurar el tipo con el badge apropiado
    const typeElement = document.getElementById('viewType');
    if (type === 'income') {
        typeElement.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Ingreso</span>';
    } else {
        typeElement.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">Gasto</span>';
    }
    
    // Configurar el monto con el color apropiado
    const amountElement = document.getElementById('viewAmount');
    const formattedAmount = '$' + parseFloat(amount).toFixed(2);
    if (type === 'income') {
        amountElement.innerHTML = '<span class="text-emerald-600 font-semibold">+' + formattedAmount + '</span>';
    } else {
        amountElement.innerHTML = '<span class="text-red-600 font-semibold">-' + formattedAmount + '</span>';
    }
    
    // Mostrar el modal
    document.getElementById('viewModal').style.display = 'flex';
}

// Función para cerrar el modal de detalles
function closeViewModal() {
    document.getElementById('viewModal').style.display = 'none';
}

// Inicialización cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    // Asegurar que el modal esté oculto por defecto
    const viewModal = document.getElementById('viewModal');
    if (viewModal) {
        viewModal.style.display = 'none';
    }
    
    // Agregar eventos a los botones de ver detalles
    document.querySelectorAll('.view-entry-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-entry-id');
            const type = this.getAttribute('data-type');
            const category = this.getAttribute('data-category');
            const amount = this.getAttribute('data-amount');
            const occurredAt = this.getAttribute('data-occurred-at');
            const cropName = this.getAttribute('data-crop-name');
            const plotName = this.getAttribute('data-plot-name');
            const reference = this.getAttribute('data-reference');
            const created = this.getAttribute('data-created');
            
            openViewModal(id, type, category, amount, occurredAt, cropName, plotName, reference, created);
        });
    });
    
    // Agregar evento de tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeViewModal();
        }
    });
    
    // Agregar evento de clic fuera del modal
    viewModal.addEventListener('click', function(e) {
        if (e.target === viewModal) {
            closeViewModal();
        }
    });
});
</script>
@endsection
