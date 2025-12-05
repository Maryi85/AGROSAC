@php
    $layout = route_prefix() === 'foreman.' ? 'foreman.layout' : 'admin.layout';
@endphp
@extends($layout)

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Gestión de Solicitudes de Préstamos</h2>
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

    <!-- Botón de descarga PDF -->
    <div class="mb-4 flex justify-end">
        <a href="{{ route(route_prefix() . 'loans.pdf', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 border border-red-200 rounded-lg font-medium transition-colors">
            <i data-lucide="file-text" class="w-5 h-5"></i>
            <span>Descargar PDF</span>
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
        <button type="submit" class="px-3 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded inline-flex items-center gap-2 transition-colors">
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
                    <th class="py-3 pr-4">Fecha Solicitud</th>
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
                        <div class="text-sm text-gray-900">{{ $loan->created_at->format('d/m/Y H:i') }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        @if($loan->out_at)
                            <div class="text-sm text-gray-900">{{ $loan->out_at->format('d/m/Y H:i') }}</div>
                        @else
                            <div class="text-sm text-gray-500">—</div>
                        @endif
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
                        @php
                            $statusClasses = [
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'approved' => 'bg-blue-100 text-blue-700',
                                'rejected' => 'bg-red-100 text-red-700',
                                'out' => 'bg-green-100 text-green-700',
                                'returned_by_worker' => 'bg-orange-100 text-orange-700',
                                'returned' => 'bg-gray-100 text-gray-700',
                                'lost' => 'bg-red-100 text-red-700',
                                'damaged' => 'bg-orange-100 text-orange-700',
                            ];
                        @endphp
                        <span class="px-2 py-1 text-xs rounded {{ $statusClasses[$loan->status] ?? 'bg-gray-100 text-gray-700' }}">
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
                                    data-out-at="{{ $loan->out_at ? $loan->out_at->format('d/m/Y H:i') : 'No prestado' }}"
                                    data-due-at="{{ $loan->due_at ? $loan->due_at->format('d/m/Y') : 'Sin fecha límite' }}"
                                    data-returned-at="{{ $loan->returned_at ? $loan->returned_at->format('d/m/Y H:i') : 'No devuelto' }}"
                                    data-status="{{ $loan->status }}"
                                    data-condition="{{ $loan->condition_return ?? 'Sin observaciones' }}"
                                    data-request-notes="{{ $loan->request_notes ?? 'Sin notas' }}"
                                    data-admin-notes="{{ $loan->admin_notes ?? 'Sin observaciones' }}"
                                    title="Ver detalles">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                            
                            @if($loan->status === 'pending')
                                <!-- Aprobar -->
                                <button type="button" class="inline-flex items-center justify-center w-8 h-8 border border-green-200 rounded hover:bg-green-50 text-green-600 approve-loan-btn" 
                                        data-loan-id="{{ $loan->id }}"
                                        data-worker-name="{{ $loan->user->name }}"
                                        data-tool-name="{{ $loan->tool->name }}"
                                        title="Aprobar">
                                    <i data-lucide="check" class="w-4 h-4"></i>
                                </button>
                                
                                <!-- Rechazar -->
                                <button type="button" class="inline-flex items-center justify-center w-8 h-8 border border-red-200 rounded hover:bg-red-50 text-red-600 reject-loan-btn" 
                                        data-loan-id="{{ $loan->id }}"
                                        data-worker-name="{{ $loan->user->name }}"
                                        data-tool-name="{{ $loan->tool->name }}"
                                        title="Rechazar">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            @elseif($loan->status === 'approved')
                                <!-- Aceptar préstamo (Procesar préstamo aprobado) -->
                                <form method="POST" action="{{ route(route_prefix() . 'loans.process-approved', $loan) }}" class="inline" data-confirm="true" data-message="¿Aceptar y procesar este préstamo aprobado?">
                                    @csrf
                                    <button class="inline-flex items-center justify-center w-8 h-8 border border-blue-200 rounded hover:bg-blue-50 text-blue-600" title="Aceptar Préstamo">
                                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            @elseif($loan->status === 'returned_by_worker')
                                <!-- Aceptar devolución (Confirmar devolución) -->
                                <button type="button" class="inline-flex items-center justify-center w-8 h-8 border border-green-200 rounded hover:bg-green-50 text-green-600 confirm-return-btn" 
                                        data-loan-id="{{ $loan->id }}"
                                        data-worker-name="{{ $loan->user->name }}"
                                        data-tool-name="{{ $loan->tool->name }}"
                                        title="Aceptar Devolución">
                                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                                </button>
                            @elseif($loan->status === 'out')
                                <!-- Devolver -->
                                <form method="POST" action="{{ route(route_prefix() . 'loans.return', $loan) }}" class="inline" data-confirm="true" data-message="¿Marcar como devuelto?">
                                    @csrf
                                    <button class="inline-flex items-center justify-center w-8 h-8 border border-emerald-200 rounded hover:bg-emerald-50 text-emerald-600" title="Devolver">
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                
                                <!-- Marcar como perdido -->
                                <form method="POST" action="{{ route(route_prefix() . 'loans.mark-lost', $loan) }}" class="inline" data-confirm="true" data-message="¿Marcar como perdido? Esta acción no se puede deshacer.">
                                    @csrf
                                    <button class="inline-flex items-center justify-center w-8 h-8 border border-orange-200 rounded hover:bg-orange-50 text-orange-600" title="Marcar como perdido">
                                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                
                                <!-- Marcar como dañado -->
                                <form method="POST" action="{{ route(route_prefix() . 'loans.mark-damaged', $loan) }}" class="inline" data-confirm="true" data-message="¿Marcar como dañado?">
                                    @csrf
                                    <button class="inline-flex items-center justify-center w-8 h-8 border border-red-200 rounded hover:bg-red-50 text-red-600" title="Marcar como dañado">
                                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            @endif
                            
                            <!-- Eliminar -->
                            <form method="POST" action="{{ route(route_prefix() . 'loans.destroy', $loan) }}" class="inline" data-confirm="true" data-message="¿Eliminar este préstamo? Esta acción no se puede deshacer.">
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
            <button type="button" onclick="closeViewModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-200 rounded transition-colors">
                <i data-lucide="x" class="w-4 h-4 inline mr-2"></i>
                Cerrar
            </button>
        </div>
    </div>
</div>

<!-- Modal de aprobación -->
<div id="approveModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-6 w-full max-w-md mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="p-2 bg-green-100 rounded-full">
                <i data-lucide="check" class="w-5 h-5 text-green-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Aprobar Préstamo</h3>
        </div>
        
        <form id="approveForm" method="POST" data-confirm="true" data-message="¿Está seguro de que desea aprobar este préstamo?" data-type="question">
            @csrf
            <div class="mb-4">
                <p class="text-gray-600 mb-4">
                    ¿Está seguro de que desea aprobar este préstamo?
                </p>
                <div class="p-3 bg-gray-50 rounded text-sm">
                    <p><strong>Trabajador:</strong> <span id="approveWorkerName"></span></p>
                    <p><strong>Herramienta:</strong> <span id="approveToolName"></span></p>
                </div>
            </div>
            
            <div class="mb-4">
                <label for="approveAdminNotes" class="block text-sm font-medium text-gray-700 mb-2">
                    Notas del administrador (opcional)
                </label>
                <textarea name="admin_notes" id="approveAdminNotes" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-emerald-500"
                          placeholder="Observaciones sobre la aprobación..."></textarea>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeApproveModal()" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 rounded text-white hover:bg-green-700">
                    Aprobar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de rechazo -->
<div id="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-6 w-full max-w-md mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="p-2 bg-red-100 rounded-full">
                <i data-lucide="x" class="w-5 h-5 text-red-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Rechazar Préstamo</h3>
        </div>
        
        <form id="rejectForm" method="POST" data-confirm="true" data-message="¿Está seguro de que desea rechazar este préstamo?" data-type="warning">
            @csrf
            <div class="mb-4">
                <p class="text-gray-600 mb-4">
                    ¿Está seguro de que desea rechazar este préstamo?
                </p>
                <div class="p-3 bg-gray-50 rounded text-sm">
                    <p><strong>Trabajador:</strong> <span id="rejectWorkerName"></span></p>
                    <p><strong>Herramienta:</strong> <span id="rejectToolName"></span></p>
                </div>
            </div>
            
            <div class="mb-4">
                <label for="rejectAdminNotes" class="block text-sm font-medium text-gray-700 mb-2">
                    Motivo del rechazo <span class="text-red-500">*</span>
                </label>
                <textarea name="admin_notes" id="rejectAdminNotes" rows="3" required
                          class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-emerald-500"
                          placeholder="Explique el motivo del rechazo..."></textarea>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 border border-red-200 rounded transition-colors">
                    Rechazar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de confirmación de devolución -->
<div id="confirmReturnModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-6 w-full max-w-md mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="p-2 bg-green-100 rounded-full">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Aceptar Devolución</h3>
        </div>
        
        <form id="confirmReturnForm" method="POST" data-confirm="true" data-message="¿Confirma que ha recibido la herramienta devuelta?" data-type="question">
            @csrf
            <div class="mb-4">
                <p class="text-gray-600 mb-4">
                    ¿Confirma que ha recibido la herramienta devuelta?
                </p>
                <div class="p-3 bg-gray-50 rounded text-sm">
                    <p><strong>Trabajador:</strong> <span id="confirmWorkerName"></span></p>
                    <p><strong>Herramienta:</strong> <span id="confirmToolName"></span></p>
                </div>
            </div>
            
            <div class="mb-4">
                <label for="confirmAdminNotes" class="block text-sm font-medium text-gray-700 mb-2">
                    Notas del administrador (opcional)
                </label>
                <textarea name="admin_notes" id="confirmAdminNotes" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-emerald-500"
                          placeholder="Observaciones sobre la devolución..."></textarea>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeConfirmReturnModal()" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 rounded text-white hover:bg-green-700">
                    Confirmar
                </button>
            </div>
        </form>
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

// Funciones para el modal de aprobación
function openApproveModal(loanId, workerName, toolName) {
    document.getElementById('approveWorkerName').textContent = workerName;
    document.getElementById('approveToolName').textContent = toolName;
    const routePrefix = '{{ route_prefix() }}';
    const prefix = routePrefix === 'foreman.' ? 'foreman' : 'admin';
    document.getElementById('approveForm').action = `/${prefix}/loans/${loanId}/approve`;
    document.getElementById('approveModal').style.display = 'flex';
}

function closeApproveModal() {
    document.getElementById('approveModal').style.display = 'none';
    document.getElementById('approveAdminNotes').value = '';
}

// Funciones para el modal de rechazo
function openRejectModal(loanId, workerName, toolName) {
    document.getElementById('rejectWorkerName').textContent = workerName;
    document.getElementById('rejectToolName').textContent = toolName;
    const routePrefix = '{{ route_prefix() }}';
    const prefix = routePrefix === 'foreman.' ? 'foreman' : 'admin';
    document.getElementById('rejectForm').action = `/${prefix}/loans/${loanId}/reject`;
    document.getElementById('rejectModal').style.display = 'flex';
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
    document.getElementById('rejectAdminNotes').value = '';
}

// Funciones para el modal de confirmación de devolución
function openConfirmReturnModal(loanId, workerName, toolName) {
    document.getElementById('confirmWorkerName').textContent = workerName;
    document.getElementById('confirmToolName').textContent = toolName;
    const routePrefix = '{{ route_prefix() }}';
    const prefix = routePrefix === 'foreman.' ? 'foreman' : 'admin';
    document.getElementById('confirmReturnForm').action = `/${prefix}/loans/${loanId}/confirm-return`;
    document.getElementById('confirmReturnModal').style.display = 'flex';
}

function closeConfirmReturnModal() {
    document.getElementById('confirmReturnModal').style.display = 'none';
    document.getElementById('confirmAdminNotes').value = '';
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

    // Agregar eventos a los botones de aprobar
    document.querySelectorAll('.approve-loan-btn').forEach(button => {
        button.addEventListener('click', function() {
            const loanId = this.getAttribute('data-loan-id');
            const workerName = this.getAttribute('data-worker-name');
            const toolName = this.getAttribute('data-tool-name');
            
            openApproveModal(loanId, workerName, toolName);
        });
    });

    // Agregar eventos a los botones de rechazar
    document.querySelectorAll('.reject-loan-btn').forEach(button => {
        button.addEventListener('click', function() {
            const loanId = this.getAttribute('data-loan-id');
            const workerName = this.getAttribute('data-worker-name');
            const toolName = this.getAttribute('data-tool-name');
            
            openRejectModal(loanId, workerName, toolName);
        });
    });

    // Agregar eventos a los botones de confirmar devolución
    document.querySelectorAll('.confirm-return-btn').forEach(button => {
        button.addEventListener('click', function() {
            const loanId = this.getAttribute('data-loan-id');
            const workerName = this.getAttribute('data-worker-name');
            const toolName = this.getAttribute('data-tool-name');
            
            openConfirmReturnModal(loanId, workerName, toolName);
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
