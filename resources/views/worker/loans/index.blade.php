@extends('worker.layout')

@section('header')
{{-- Responsive: header wraps on mobile --}}
<div class="flex flex-wrap items-center justify-between gap-2">
    <h2 class="text-lg font-semibold text-emerald-700">Mis Préstamos de Herramientas</h2>
    <a href="{{ route('worker.loans.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Solicitar Préstamo</span>
    </a>
</div>
@endsection

@section('content')
<div class="bg-white border rounded p-4">
    {{-- Responsive: filter stacks on mobile --}}
    <div class="mb-6 flex flex-col sm:flex-row gap-2 sm:items-end">
        <div class="flex-1">
            <label class="block text-sm mb-1 text-emerald-800">Filtrar por estado</label>
            <select class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-emerald-500" onchange="filterByStatus(this.value)">
                <option value="all">Todos los estados</option>
                <option value="pending">Pendiente</option>
                <option value="approved">Aprobado</option>
                <option value="rejected">Rechazado</option>
                <option value="out">Prestado</option>
                <option value="returned">Devuelto</option>
            </select>
        </div>
    </div>

    <!-- Tabla de préstamos -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-emerald-800 border-b">
                    <th class="py-3 pr-4">Herramienta</th>
                    <th class="py-3 pr-4">Cantidad</th>
                    <th class="py-3 pr-4">Estado</th>
                    <th class="py-3 pr-4">Fecha Solicitud</th>
                    <th class="py-3 pr-4">Fecha Vencimiento</th>
                    <th class="py-3 pr-4">Notas</th>
                    <th class="py-3 pr-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($loans as $loan)
                <tr id="loan-row-{{ $loan->id }}" class="border-b hover:bg-gray-50" data-status="{{ $loan->status }}">
                    <td class="py-3 pr-4">
                        <div class="flex items-center gap-3">
                            @if($loan->tool->photo)
                                <img src="{{ storage_asset($loan->tool->photo) }}" alt="Foto" class="h-10 w-10 object-cover rounded border border-gray-200">
                            @else
                                <div class="h-10 w-10 rounded border border-gray-200 bg-gray-50 flex items-center justify-center text-xs text-gray-400">Sin foto</div>
                            @endif
                            <div>
                                <div class="font-medium text-gray-900">{{ $loan->tool->name }}</div>
                                <div class="text-xs text-gray-500">{{ $loan->tool->category }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="font-medium text-gray-900">{{ $loan->quantity }}</span>
                    </td>
                    <td class="py-3 pr-4">
                        @php
                            $statusClasses = [
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'approved' => 'bg-blue-100 text-blue-700',
                                'rejected' => 'bg-red-100 text-red-700',
                                'out' => 'bg-blue-100 text-blue-700',
                                'returned_by_worker' => 'bg-emerald-100 text-emerald-700',
                                'returned' => 'bg-emerald-100 text-emerald-700',
                                'lost' => 'bg-orange-100 text-orange-700',
                                'damaged' => 'bg-red-100 text-red-700',
                            ];
                            $statusLabels = [
                                'pending' => 'Pendiente',
                                'approved' => 'Aprobado',
                                'rejected' => 'Rechazado',
                                'out' => 'Prestado',
                                'returned_by_worker' => 'Devuelto (Pendiente)',
                                'returned' => 'Devuelto y Confirmado',
                                'lost' => 'Perdido',
                                'damaged' => 'Dañado',
                            ];
                        @endphp
                        <span id="loan-status-badge-{{ $loan->id }}" class="px-2 py-1 text-xs rounded {{ $statusClasses[$loan->status] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ $statusLabels[$loan->status] ?? $loan->status }}
                        </span>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-600">{{ $loan->created_at->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $loan->created_at->format('H:i') }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        @if($loan->due_at)
                            <div class="text-sm text-gray-600">{{ $loan->due_at->format('d/m/Y') }}</div>
                            @if($loan->due_at->isPast() && $loan->status === 'out')
                                <div class="text-xs text-red-500">Vencido</div>
                            @endif
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-600 max-w-xs truncate" title="{{ $loan->request_notes }}">
                            {{ $loan->request_notes ?? '—' }}
                        </div>
                    </td>
                    <td class="py-3 pr-4 text-right">
                        <div class="flex items-center gap-1 justify-end">
                            <!-- Ver detalles -->
                            <a href="{{ route('worker.loans.show', $loan) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 border border-blue-200 rounded hover:bg-blue-50 text-blue-600" 
                               title="Ver detalles">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            
                            <!-- Devolver (solo si está prestado) -->
                            @if($loan->status === 'out')
                                <button onclick="openReturnModal({{ $loan->id }}, '{{ $loan->tool->name }}')" 
                                        class="inline-flex items-center justify-center gap-2 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md font-medium text-xs transition-colors shadow-sm" 
                                        title="Devolver Herramienta">
                                    <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
                                    <span>Devolver Herramienta</span>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-6 text-center text-emerald-800/70">No tienes préstamos registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $loans->links() }}</div>
</div>

<!-- Modal de Devolución -->
<div id="returnModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md max-h-[92vh] overflow-y-auto animate-in fade-in zoom-in duration-200">
        <div class="p-4 border-b bg-emerald-50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-emerald-800">Devolver Herramienta</h3>
            <button onclick="closeReturnModal()" class="text-emerald-600 hover:text-emerald-800">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <form id="returnForm" method="POST" class="p-6 space-y-4">
            @csrf
            <p class="text-sm text-gray-600">
                Estás devolviendo: <span id="returnToolName" class="font-bold text-gray-800"></span>
            </p>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado de la herramienta</label>
                <div class="space-y-2">
                    <label class="flex items-center p-2 border rounded hover:bg-gray-50 cursor-pointer">
                        <input type="radio" name="condition_return" value="good" checked class="text-emerald-600">
                        <span class="ml-2 text-sm">Buen estado</span>
                    </label>
                    <label class="flex items-center p-2 border rounded hover:bg-gray-50 cursor-pointer">
                        <input type="radio" name="condition_return" value="damaged" class="text-orange-600">
                        <span class="ml-2 text-sm">Dañado</span>
                    </label>
                    <label class="flex items-center p-2 border rounded hover:bg-gray-50 cursor-pointer">
                        <input type="radio" name="condition_return" value="lost" class="text-red-600">
                        <span class="ml-2 text-sm">Extraviado</span>
                    </label>
                </div>
            </div>
            
            <div>
                <label for="return_notes" class="block text-sm font-medium text-gray-700 mb-1">Notas (Opcional)</label>
                <textarea name="return_notes" id="return_notes" rows="2" class="w-full px-3 py-2 border rounded text-sm focus:ring-emerald-500" placeholder="Alguna observación sobre el estado..."></textarea>
            </div>
            
            {{-- Responsive: full-width on mobile --}}
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-2">
                <button type="button" onclick="closeReturnModal()" class="w-full sm:w-auto px-4 py-2 text-gray-600 hover:bg-gray-100 rounded text-center">Cancelar</button>
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-center">Confirmar Devolución</button>
            </div>
        </form>
    </div>
</div>

<script>
function filterByStatus(status) {
    const rows = document.querySelectorAll('tbody tr[data-status]');
    rows.forEach(row => {
        if (status === 'all' || row.getAttribute('data-status') === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function openReturnModal(loanId, toolName) {
    document.getElementById('returnToolName').textContent = toolName;
    document.getElementById('returnForm').action = `/worker/loans/${loanId}/return`;
    document.getElementById('returnModal').style.display = 'flex';
}

function closeReturnModal() {
    document.getElementById('returnModal').style.display = 'none';
    document.getElementById('returnForm').reset();
}

document.getElementById('returnForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = this;
    const formData = new FormData(form);
    
    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            if (window.Swal) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                alert(data.message);
            }
            closeReturnModal();
            
            // Actualizar fila dinámicamente
            const loanId = form.action.split('/').slice(-2, -1)[0];
            const badge = document.getElementById(`loan-status-badge-${loanId}`);
            if (badge) {
                badge.className = 'px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700';
                badge.textContent = 'Devuelto (Pendiente)';
            }
            
            // Opcional: Recargar después de un momento
            setTimeout(() => location.reload(), 2000);
        } else {
            alert(data.message || 'Error al procesar la devolución');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error de conexión');
    }
});
</script>
@endsection
