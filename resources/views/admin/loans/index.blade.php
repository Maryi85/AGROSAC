@php
    $layout = route_prefix() === 'foreman.' ? 'foreman.layout' : 'admin.layout';
@endphp
@extends($layout)

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Gestión de Solicitudes de Préstamos</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-4">


    @if (session('error'))
        <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- PDF button: full-width on mobile --}}
    <div class="mb-4 flex">
        <a href="{{ route(route_prefix() . 'loans.pdf', request()->query()) }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 border border-red-200 rounded-lg font-medium transition-colors">
            <i data-lucide="file-text" class="w-5 h-5"></i>
            <span>Descargar PDF</span>
        </a>
    </div>

    <!-- Filtros de búsqueda -->
    <div class="mb-4">
        <x-search-bar placeholder="Buscar por herramienta o trabajador..." />
    </div>

    {{-- Tabla Desktop --}}
    <div class="hidden md:block overflow-x-auto">
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
            <tbody class="divide-y divide-gray-100" id="loans-table-body">
                @forelse ($loans as $loan)
                <tr class="hover:bg-gray-50 transition-colors" id="loan-row-{{ $loan->id }}">
                    <td class="py-3 pr-4">
                        <div class="font-medium text-gray-900">{{ $loan->tool->name }}</div>
                        <div class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $loan->tool->category)) }}</div>
                        @if($loan->task)
                            <div class="mt-1 flex items-center gap-1 text-[10px] text-blue-600 font-bold bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100 max-w-fit">
                                <i data-lucide="briefcase" class="w-3 h-3"></i>
                                <span class="truncate">{{ Str::limit($loan->task->description, 20) }}</span>
                            </div>
                        @endif
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
                        <span id="loan-status-badge-{{ $loan->id }}" class="px-2 py-1 text-xs rounded {{ $statusClasses[$loan->status] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ $statuses[$loan->status] }}
                        </span>
                    </td>
                    <td class="py-3 pr-4 text-right">
                        <div class="flex items-center gap-1 justify-end" id="loan-actions-{{ $loan->id }}">
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
                                    data-task-name="{{ $loan->task ? $loan->task->description . ' (' . ($loan->task->plot->name ?? 'Lote General') . ')' : '' }}"
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
                            
                            @if($loan->status === 'rejected')
                                <!-- Eliminar (sólo permitido si está rechazado) -->
                                <form method="POST" action="{{ route(route_prefix() . 'loans.destroy', $loan) }}" class="inline" data-confirm="true" data-message="¿Eliminar este préstamo? Esta acción no se puede deshacer.">
                                    @csrf
                                    @method('DELETE')
                                    <button class="inline-flex items-center justify-center w-8 h-8 border border-red-200 rounded hover:bg-red-50 text-red-600" title="Eliminar">
                                        <i data-lucide="trash" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            @endif
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

    {{-- Mobile Cards --}}
    <div class="md:hidden space-y-3">
        @forelse ($loans as $loan)
        @php
            $statusClasses = ['pending'=>'bg-yellow-100 text-yellow-700','approved'=>'bg-blue-100 text-blue-700','rejected'=>'bg-red-100 text-red-700','out'=>'bg-green-100 text-green-700','returned_by_worker'=>'bg-orange-100 text-orange-700','returned'=>'bg-gray-100 text-gray-700','lost'=>'bg-red-100 text-red-700','damaged'=>'bg-orange-100 text-orange-700'];
        @endphp
        <div class="bg-white border rounded-lg p-4 shadow-sm" id="loan-card-{{ $loan->id }}">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <div class="font-semibold text-gray-900">{{ $loan->tool->name }}</div>
                    <div class="text-xs text-gray-500">{{ $loan->user->name }}</div>
                </div>
                <span id="loan-status-badge-{{ $loan->id }}" class="px-2 py-1 text-xs rounded {{ $statusClasses[$loan->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $statuses[$loan->status] }}</span>
            </div>
            <div class="text-sm text-gray-600 space-y-1 mb-3">
                <div class="flex items-center gap-2"><i data-lucide="calendar" class="w-4 h-4 text-emerald-500"></i><span>{{ $loan->created_at->format('d/m/Y') }}</span></div>
                <div class="flex items-center gap-2"><i data-lucide="box" class="w-4 h-4 text-emerald-500"></i><span>Cantidad: {{ $loan->quantity }}</span></div>
            </div>
            <div class="flex flex-wrap gap-2 border-t pt-3" id="loan-actions-{{ $loan->id }}">
                <button type="button" class="view-loan-btn flex-1 flex items-center justify-center gap-1 py-2 text-blue-600 bg-blue-50 rounded-lg text-sm"
                    data-loan-id="{{ $loan->id }}" data-tool-name="{{ $loan->tool->name }}" data-tool-category="{{ $loan->tool->category }}"
                    data-worker-name="{{ $loan->user->name }}" data-worker-email="{{ $loan->user->email }}" data-quantity="{{ $loan->quantity }}"
                    data-out-at="{{ $loan->out_at ? $loan->out_at->format('d/m/Y H:i') : 'No prestado' }}" data-due-at="{{ $loan->due_at ? $loan->due_at->format('d/m/Y') : 'Sin fecha límite' }}"
                    data-returned-at="{{ $loan->returned_at ? $loan->returned_at->format('d/m/Y H:i') : 'No devuelto' }}" data-status="{{ $loan->status }}"
                    data-condition="{{ $loan->condition_return ?? 'Sin observaciones' }}" data-request-notes="{{ $loan->request_notes ?? 'Sin notas' }}"
                    data-admin-notes="{{ $loan->admin_notes ?? 'Sin observaciones' }}" data-task-name="{{ $loan->task ? $loan->task->description : '' }}">
                    <i data-lucide="eye" class="w-4 h-4"></i><span>Ver</span>
                </button>
                @if($loan->status === 'pending')
                    <button type="button" class="approve-loan-btn flex-1 flex items-center justify-center gap-1 py-2 text-green-700 bg-green-50 rounded-lg text-sm" data-loan-id="{{ $loan->id }}" data-worker-name="{{ $loan->user->name }}" data-tool-name="{{ $loan->tool->name }}"><i data-lucide="check" class="w-4 h-4"></i><span>Aprobar</span></button>
                    <button type="button" class="reject-loan-btn flex-1 flex items-center justify-center gap-1 py-2 text-red-700 bg-red-50 rounded-lg text-sm" data-loan-id="{{ $loan->id }}" data-worker-name="{{ $loan->user->name }}" data-tool-name="{{ $loan->tool->name }}"><i data-lucide="x" class="w-4 h-4"></i><span>Rechazar</span></button>
                @elseif($loan->status === 'returned_by_worker')
                    <button type="button" class="confirm-return-btn flex-1 flex items-center justify-center gap-1 py-2 text-green-700 bg-green-50 rounded-lg text-sm" data-loan-id="{{ $loan->id }}" data-worker-name="{{ $loan->user->name }}" data-tool-name="{{ $loan->tool->name }}"><i data-lucide="check-circle" class="w-4 h-4"></i><span>Confirmar</span></button>
                @endif
                @if($loan->status === 'rejected')
                    <!-- Eliminar -->
                    <form method="POST" action="{{ route(route_prefix() . 'loans.destroy', $loan) }}" class="flex-none" data-confirm="true" data-message="¿Eliminar este préstamo? Esta acción no se puede deshacer.">
                        @csrf
                        @method('DELETE')
                        <button class="inline-flex items-center justify-center w-9 h-9 border border-red-200 rounded hover:bg-red-50 text-red-600" title="Eliminar">
                            <i data-lucide="trash" class="w-4 h-4"></i>
                        </button>
                    </form>
                @endif
            </div>
        </div>
        @empty
        <div class="py-8 text-center text-gray-500 bg-gray-50 rounded-lg border border-dashed text-sm">No hay préstamos registrados</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $loans->links() }}</div>
</div>

<!-- Modal de detalles -->
<div id="viewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-5 sm:p-6 w-full max-w-2xl mx-4 max-h-[92vh] overflow-y-auto">
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
                    <div id="viewTaskRow" class="md:col-span-2" style="display:none;">
                        <label class="block text-sm font-medium text-gray-700">Tarea Relacionada</label>
                        <div class="mt-1 flex items-center gap-2 p-3 bg-blue-50 border border-blue-100 rounded-lg">
                            <i data-lucide="briefcase" class="w-4 h-4 text-blue-600"></i>
                            <p id="viewTaskName" class="text-sm font-semibold text-blue-900"></p>
                        </div>
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
    <div class="bg-white border rounded p-5 sm:p-6 w-full max-w-md mx-4 max-h-[92vh] overflow-y-auto">
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
    <div class="bg-white border rounded p-5 sm:p-6 w-full max-w-md mx-4 max-h-[92vh] overflow-y-auto">
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
    <div class="bg-white border rounded p-5 sm:p-6 w-full max-w-md mx-4 max-h-[92vh] overflow-y-auto">
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
// === MODAL DE DETALLES (VIEW) ===
function openViewModal(id, toolName, toolCategory, workerName, workerEmail, quantity, outAt, dueAt, returnedAt, status, condition, taskName) {
    document.getElementById('viewToolName').textContent = toolName;
    document.getElementById('viewToolCategory').textContent = toolCategory.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
    document.getElementById('viewWorkerName').textContent = workerName;
    document.getElementById('viewWorkerEmail').textContent = workerEmail;
    document.getElementById('viewQuantity').textContent = quantity;
    document.getElementById('viewOutAt').textContent = outAt;
    document.getElementById('viewDueAt').textContent = dueAt;
    document.getElementById('viewReturnedAt').textContent = returnedAt;
    document.getElementById('viewCondition').textContent = condition || 'Sin observaciones';

    const taskRow = document.getElementById('viewTaskRow');
    if (taskName) {
        document.getElementById('viewTaskName').textContent = taskName;
        taskRow.style.display = 'block';
    } else {
        taskRow.style.display = 'none';
    }

    const statusElement = document.getElementById('viewStatus');
    const statusMap = {
        'pending': '<span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700">Pendiente</span>',
        'approved': '<span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">Aprobado</span>',
        'rejected': '<span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">Rechazado</span>',
        'out': '<span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">Prestado</span>',
        'returned_by_worker': '<span class="px-2 py-1 text-xs rounded bg-orange-100 text-orange-700">Devuelto por Trabajador</span>',
        'returned': '<span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Devuelto y Confirmado</span>',
        'lost': '<span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">Perdido</span>',
        'damaged': '<span class="px-2 py-1 text-xs rounded bg-orange-100 text-orange-700">Dañado</span>',
    };
    statusElement.innerHTML = statusMap[status] || '<span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">' + status + '</span>';

    document.getElementById('viewModal').style.display = 'flex';
}

function closeViewModal() {
    document.getElementById('viewModal').style.display = 'none';
}

// === MODAL DE APROBACIÓN ===
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

// === MODAL DE RECHAZO ===
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

// === MODAL DE CONFIRMACIÓN DE DEVOLUCIÓN ===
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

// === STATUS HELPERS ===
function getLoanStatusClass(status) {
    const map = {
        'pending': 'bg-yellow-100 text-yellow-700',
        'approved': 'bg-blue-100 text-blue-700',
        'rejected': 'bg-red-100 text-red-700',
        'out': 'bg-green-100 text-green-700',
        'returned_by_worker': 'bg-orange-100 text-orange-700',
        'returned': 'bg-gray-100 text-gray-700',
        'lost': 'bg-red-100 text-red-700',
        'damaged': 'bg-orange-100 text-orange-700',
    };
    return map[status] || 'bg-gray-100 text-gray-700';
}

function getLoanStatusText(status) {
    const map = {
        'pending': 'Pendiente',
        'approved': 'Aprobado',
        'rejected': 'Rechazado',
        'out': 'Prestado',
        'returned_by_worker': 'Devuelto por Trabajador',
        'returned': 'Devuelto y Confirmado',
        'lost': 'Perdido',
        'damaged': 'Dañado',
    };
    return map[status] || status;
}

// === AJAX HANDLER ===
const handleLoanAction = async (form) => {
    const formData = new FormData(form);
    const url = form.action;
    const methodInput = form.querySelector('input[name="_method"]');
    const method = methodInput ? methodInput.value : form.method;

    try {
        const response = await fetch(url, {
            method: method.toUpperCase() === 'DELETE' ? 'POST' : method,
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();

        if (data.success) {
            if (window.Swal) {
                Swal.fire({ title: '¡Éxito!', text: data.message, icon: 'success', timer: 1800, showConfirmButton: false });
            }

            if (data.id) {
                // Eliminar fila
                document.getElementById(`loan-row-${data.id}`)?.remove();
                document.getElementById(`loan-card-${data.id}`)?.remove();
            } else if (data.loan) {
                // Actualizar badge de estado
                const badge1 = document.getElementById(`loan-status-badge-${data.loan.id}`);
                if (badge1) {
                    badge1.className = `px-2 py-1 text-xs rounded ${getLoanStatusClass(data.loan.status)}`;
                    badge1.textContent = getLoanStatusText(data.loan.status);
                }
                setTimeout(() => location.reload(), 1800);
            }
        } else {
            if (window.Swal) {
                Swal.fire('Error', data.message || 'Error al procesar la solicitud', 'error');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        if (window.Swal) Swal.fire('Error', 'Error de red o del servidor', 'error');
    }
};

// === DOM READY ===
document.addEventListener('DOMContentLoaded', function() {
    // Delegated click handler for all action buttons
    document.addEventListener('click', function(e) {
        const viewBtn = e.target.closest('.view-loan-btn');
        if (viewBtn) {
            e.preventDefault();
            const d = viewBtn.dataset;
            openViewModal(d.loanId, d.toolName, d.toolCategory, d.workerName, d.workerEmail,
                d.quantity, d.outAt, d.dueAt, d.returnedAt, d.status, d.condition, d.taskName);
        }

        const approveBtn = e.target.closest('.approve-loan-btn');
        if (approveBtn) {
            e.preventDefault();
            openApproveModal(approveBtn.dataset.loanId, approveBtn.dataset.workerName, approveBtn.dataset.toolName);
        }

        const rejectBtn = e.target.closest('.reject-loan-btn');
        if (rejectBtn) {
            e.preventDefault();
            openRejectModal(rejectBtn.dataset.loanId, rejectBtn.dataset.workerName, rejectBtn.dataset.toolName);
        }

        const confirmBtn = e.target.closest('.confirm-return-btn');
        if (confirmBtn) {
            e.preventDefault();
            openConfirmReturnModal(confirmBtn.dataset.loanId, confirmBtn.dataset.workerName, confirmBtn.dataset.toolName);
        }
    });

    // Intercept all loan forms for AJAX + confirmation
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form.getAttribute('action')?.includes('loans')) return;
        if (form.id === 'search-form') return;

        e.preventDefault();
        e.stopImmediatePropagation();

        const message = form.getAttribute('data-message') || '¿Confirmar acción?';
        const confirmAction = form.getAttribute('data-confirm') === 'true';

        const doSubmit = () => {
            // Close the modal AFTER capturing data (handleLoanAction reads form at call time)
            const formId = form.id;
            handleLoanAction(form).then(() => {
                if (formId === 'approveForm') closeApproveModal();
                if (formId === 'rejectForm') closeRejectModal();
                if (formId === 'confirmReturnForm') closeConfirmReturnModal();
            });
        };

        if (confirmAction) {
            if (window.Swal) {
                Swal.fire({
                    title: '¿Confirmar acción?',
                    text: message,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#ef4444',
                    confirmButtonText: 'Sí, confirmar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) doSubmit();
                });
            } else if (confirm(message)) {
                doSubmit();
            }
        } else {
            doSubmit();
        }
    }, true);

    // Close modals on backdrop click
    window.addEventListener('click', (e) => {
        if (e.target.id === 'viewModal') closeViewModal();
        if (e.target.id === 'approveModal') closeApproveModal();
        if (e.target.id === 'rejectModal') closeRejectModal();
        if (e.target.id === 'confirmReturnModal') closeConfirmReturnModal();
    });

    // Close modals on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeViewModal();
            closeApproveModal();
            closeRejectModal();
            closeConfirmReturnModal();
        }
    });
});
</script>
@endsection
