@extends('admin.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Gestión de Préstamos</h2>
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

    <!-- Botón para prestar nueva herramienta -->
    <div class="flex justify-between items-center mb-4">
        <div></div>
        <a href="{{ route('admin.loans.create') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Prestar Herramienta</span>
        </a>
    </div>

    <!-- Filtros de búsqueda -->
    <form method="GET" class="mb-4 flex gap-2 items-end">
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Estado</label>
            <select name="status" class="border border-emerald-200 rounded px-3 py-2">
                <option value="all">Todos los estados</option>
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Herramienta</label>
            <select name="tool_id" class="border border-emerald-200 rounded px-3 py-2">
                <option value="all">Todas las herramientas</option>
                @foreach($tools as $tool)
                    <option value="{{ $tool->id }}" {{ request('tool_id') == $tool->id ? 'selected' : '' }}>
                        {{ $tool->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Trabajador</label>
            <select name="user_id" class="border border-emerald-200 rounded px-3 py-2">
                <option value="all">Todos los trabajadores</option>
                @foreach($workers as $worker)
                    <option value="{{ $worker->id }}" {{ request('user_id') == $worker->id ? 'selected' : '' }}>
                        {{ $worker->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded inline-flex items-center gap-2">
            <i data-lucide="search" class="w-4 h-4"></i>
            <span>Filtrar</span>
        </button>
    </form>

    <!-- Tabla de préstamos -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-emerald-800 border-b">
                    <th class="py-3 pr-4">Herramienta</th>
                    <th class="py-3 pr-4">Trabajador</th>
                    <th class="py-3 pr-4">Cantidad</th>
                    <th class="py-3 pr-4">Fecha Préstamo</th>
                    <th class="py-3 pr-4">Fecha Devolución</th>
                    <th class="py-3 pr-4">Estado</th>
                    <th class="py-3 pr-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($loans as $loan)
                <tr class="border-b hover:bg-gray-50" data-loan-id="{{ $loan->id }}">
                    <td class="py-3 pr-4">
                        <div class="font-medium text-gray-900">{{ $loan->tool->name }}</div>
                        <div class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $loan->tool->category)) }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="font-medium text-gray-900">{{ $loan->user->name }}</div>
                        <div class="text-xs text-gray-500">{{ $loan->user->email }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-900 font-medium">{{ $loan->quantity }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-900">{{ $loan->out_at->format('d/m/Y H:i') }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        @if($loan->due_at)
                            <div class="text-sm text-gray-900">{{ $loan->due_at->format('d/m/Y') }}</div>
                            @if($loan->status === 'out' && $loan->due_at->isPast())
                                <div class="text-xs text-red-600">Vencido</div>
                            @endif
                        @else
                            <div class="text-sm text-gray-500">Sin fecha límite</div>
                        @endif
                    </td>
                    <td class="py-3 pr-4">
                        <span class="px-2 py-1 text-xs rounded {{ $loan->status === 'out' ? 'bg-blue-100 text-blue-700' : ($loan->status === 'returned' ? 'bg-emerald-100 text-emerald-700' : ($loan->status === 'lost' ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700')) }}">
                            {{ $statuses[$loan->status] }}
                        </span>
                    </td>
                    <td class="py-3 pr-4 text-right">
                        <div class="flex items-center gap-1 justify-end">
                            <!-- Ver detalles -->
                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 border border-blue-200 rounded hover:bg-blue-50 text-blue-600 view-loan-btn" 
                                    data-loan-id="{{ $loan->id }}"
                                    data-tool-name="{{ $loan->tool->name }}"
                                    data-tool-category="{{ $loan->tool->category }}"
                                    data-worker-name="{{ $loan->user->name }}"
                                    data-worker-email="{{ $loan->user->email }}"
                                    data-quantity="{{ $loan->quantity }}"
                                    data-out-at="{{ $loan->out_at->format('d/m/Y H:i') }}"
                                    data-due-at="{{ $loan->due_at ? $loan->due_at->format('d/m/Y') : 'Sin fecha límite' }}"
                                    data-returned-at="{{ $loan->returned_at ? $loan->returned_at->format('d/m/Y H:i') : 'No devuelto' }}"
                                    data-status="{{ $loan->status }}"
                                    data-condition="{{ $loan->condition_return ?? 'Sin observaciones' }}"
                                    title="Ver detalles">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                            
                            @if($loan->status === 'out')
                                <!-- Devolver -->
                                <form method="POST" action="{{ route('admin.loans.return', $loan) }}" class="inline" data-confirm="true" data-message="¿Marcar como devuelto?">
                                    @csrf
                                    <button class="inline-flex items-center justify-center w-8 h-8 border border-emerald-200 rounded hover:bg-emerald-50 text-emerald-600" title="Devolver">
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                
                                <!-- Marcar como perdido -->
                                <form method="POST" action="{{ route('admin.loans.mark-lost', $loan) }}" class="inline" data-confirm="true" data-message="¿Marcar como perdido? Esta acción no se puede deshacer.">
                                    @csrf
                                    <button class="inline-flex items-center justify-center w-8 h-8 border border-orange-200 rounded hover:bg-orange-50 text-orange-600" title="Marcar como perdido">
                                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                
                                <!-- Marcar como dañado -->
                                <form method="POST" action="{{ route('admin.loans.mark-damaged', $loan) }}" class="inline" data-confirm="true" data-message="¿Marcar como dañado?">
                                    @csrf
                                    <button class="inline-flex items-center justify-center w-8 h-8 border border-red-200 rounded hover:bg-red-50 text-red-600" title="Marcar como dañado">
                                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            @endif
                            
                            <!-- Eliminar -->
                            <form method="POST" action="{{ route('admin.loans.destroy', $loan) }}" class="inline" data-confirm="true" data-message="¿Eliminar este préstamo? Esta acción no se puede deshacer.">
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
                    <td colspan="7" class="py-6 text-center text-emerald-800/70">No hay préstamos registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $loans->links() }}</div>
</div>

<!-- Modal de detalles -->
<div id="viewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-6 w-full max-w-2xl mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-emerald-700">Detalles del Préstamo</h3>
            <button type="button" onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <div class="space-y-6">
            <!-- Información de la Herramienta -->
            <div class="bg-gray-50 rounded p-4">
                <h4 class="text-md font-semibold text-emerald-700 mb-3">Información de la Herramienta</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre</label>
                        <p id="viewToolName" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Categoría</label>
                        <p id="viewToolCategory" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                </div>
            </div>
            
            <!-- Información del Trabajador -->
            <div class="bg-gray-50 rounded p-4">
                <h4 class="text-md font-semibold text-emerald-700 mb-3">Información del Trabajador</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre</label>
                        <p id="viewWorkerName" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <p id="viewWorkerEmail" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cantidad Prestada</label>
                        <p id="viewQuantity" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                </div>
            </div>
            
            <!-- Información del Préstamo -->
            <div class="bg-gray-50 rounded p-4">
                <h4 class="text-md font-semibold text-emerald-700 mb-3">Información del Préstamo</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha de Préstamo</label>
                        <p id="viewOutAt" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha Límite</label>
                        <p id="viewDueAt" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha de Devolución</label>
                        <p id="viewReturnedAt" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estado</label>
                        <p id="viewStatus" class="text-sm mt-1"></p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Condición al Devolver</label>
                        <p id="viewCondition" class="text-sm text-gray-900 mt-1"></p>
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
function openViewModal(id, toolName, toolCategory, workerName, workerEmail, quantity, outAt, dueAt, returnedAt, status, condition) {
    // Llenar los campos del modal de detalles
    document.getElementById('viewToolName').textContent = toolName;
    document.getElementById('viewToolCategory').textContent = toolCategory.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
    document.getElementById('viewWorkerName').textContent = workerName;
    document.getElementById('viewWorkerEmail').textContent = workerEmail;
    document.getElementById('viewQuantity').textContent = quantity;
    document.getElementById('viewOutAt').textContent = outAt;
    document.getElementById('viewDueAt').textContent = dueAt;
    document.getElementById('viewReturnedAt').textContent = returnedAt;
    document.getElementById('viewCondition').textContent = condition;
    
    // Configurar el estado con el badge apropiado
    const statusElement = document.getElementById('viewStatus');
    const statusLabels = {
        'out': 'Prestado',
        'returned': 'Devuelto',
        'lost': 'Perdido',
        'damaged': 'Dañado'
    };
    
    if (status === 'out') {
        statusElement.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">Prestado</span>';
    } else if (status === 'returned') {
        statusElement.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Devuelto</span>';
    } else if (status === 'lost') {
        statusElement.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-orange-100 text-orange-700">Perdido</span>';
    } else {
        statusElement.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">Dañado</span>';
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
    document.querySelectorAll('.view-loan-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-loan-id');
            const toolName = this.getAttribute('data-tool-name');
            const toolCategory = this.getAttribute('data-tool-category');
            const workerName = this.getAttribute('data-worker-name');
            const workerEmail = this.getAttribute('data-worker-email');
            const quantity = this.getAttribute('data-quantity');
            const outAt = this.getAttribute('data-out-at');
            const dueAt = this.getAttribute('data-due-at');
            const returnedAt = this.getAttribute('data-returned-at');
            const status = this.getAttribute('data-status');
            const condition = this.getAttribute('data-condition');
            
            openViewModal(id, toolName, toolCategory, workerName, workerEmail, quantity, outAt, dueAt, returnedAt, status, condition);
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
