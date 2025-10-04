@extends('foreman.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Gestión de Préstamos</h2>
    <a href="{{ route('foreman.loans.create') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Prestar Herramienta</span>
    </a>
</div>
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
                            <button class="view-loan-btn inline-flex items-center justify-center w-8 h-8 border border-blue-200 rounded hover:bg-blue-50 text-blue-600" 
                                    title="Ver detalles"
                                    data-loan-id="{{ $loan->id }}">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                            
                            @if($loan->status === 'out')
                                <!-- Devolver -->
                                <button class="return-loan-btn inline-flex items-center justify-center w-8 h-8 border border-emerald-200 rounded hover:bg-emerald-50 text-emerald-600" 
                                        title="Devolver"
                                        data-loan-id="{{ $loan->id }}">
                                    <i data-lucide="check" class="w-4 h-4"></i>
                                </button>
                                
                                <!-- Marcar como perdido -->
                                <button class="mark-lost-btn inline-flex items-center justify-center w-8 h-8 border border-orange-200 rounded hover:bg-orange-50 text-orange-600" 
                                        title="Marcar como perdido"
                                        data-loan-id="{{ $loan->id }}">
                                    <i data-lucide="x-circle" class="w-4 h-4"></i>
                                </button>
                                
                                <!-- Marcar como dañado -->
                                <button class="mark-damaged-btn inline-flex items-center justify-center w-8 h-8 border border-red-200 rounded hover:bg-red-50 text-red-600" 
                                        title="Marcar como dañado"
                                        data-loan-id="{{ $loan->id }}">
                                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                                </button>
                            @endif
                            
                            <!-- Eliminar -->
                            <button class="delete-loan-btn inline-flex items-center justify-center w-8 h-8 border border-red-200 rounded hover:bg-red-50 text-red-600" 
                                    title="Eliminar"
                                    data-loan-id="{{ $loan->id }}">
                                <i data-lucide="trash" class="w-4 h-4"></i>
                            </button>
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
<div id="loanDetailsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-emerald-700">Detalles del Préstamo</h3>
            <button type="button" onclick="closeLoanDetailsModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <div id="loanDetailsContent" class="space-y-6">
            <!-- Contenido se carga dinámicamente -->
        </div>
    </div>
</div>

<script>
// Agregar event listeners para los botones
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.view-loan-btn').forEach(button => {
        button.addEventListener('click', function() {
            const loanId = this.getAttribute('data-loan-id');
            openLoanDetailsModal(loanId);
        });
    });

    document.querySelectorAll('.return-loan-btn').forEach(button => {
        button.addEventListener('click', function() {
            const loanId = this.getAttribute('data-loan-id');
            returnLoan(loanId);
        });
    });

    document.querySelectorAll('.mark-lost-btn').forEach(button => {
        button.addEventListener('click', function() {
            const loanId = this.getAttribute('data-loan-id');
            markAsLost(loanId);
        });
    });

    document.querySelectorAll('.mark-damaged-btn').forEach(button => {
        button.addEventListener('click', function() {
            const loanId = this.getAttribute('data-loan-id');
            markAsDamaged(loanId);
        });
    });

    document.querySelectorAll('.delete-loan-btn').forEach(button => {
        button.addEventListener('click', function() {
            const loanId = this.getAttribute('data-loan-id');
            deleteLoan(loanId);
        });
    });
});

// Funciones para el modal de detalles
async function openLoanDetailsModal(loanId) {
    try {
        // Mostrar loading
        document.getElementById('loanDetailsContent').innerHTML = `
            <div class="flex items-center justify-center py-8">
                <i data-lucide="loader-2" class="w-6 h-6 animate-spin text-emerald-600"></i>
                <span class="ml-2 text-emerald-600">Cargando detalles...</span>
            </div>
        `;
        
        document.getElementById('loanDetailsModal').style.display = 'flex';
        
        const response = await fetch(`/foreman/loans/${loanId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            renderLoanDetails(data.loan);
        } else {
            document.getElementById('loanDetailsContent').innerHTML = `
                <div class="text-center py-8 text-red-600">
                    <i data-lucide="alert-circle" class="w-8 h-8 mx-auto mb-2"></i>
                    <p>Error al cargar los detalles del préstamo</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('loanDetailsContent').innerHTML = `
            <div class="text-center py-8 text-red-600">
                <i data-lucide="alert-circle" class="w-8 h-8 mx-auto mb-2"></i>
                <p>Error de conexión. Inténtalo de nuevo.</p>
            </div>
        `;
    }
}

function renderLoanDetails(loan) {
    const statusClass = getLoanStatusClass(loan.status);
    const statusText = getLoanStatusText(loan.status);
    
    document.getElementById('loanDetailsContent').innerHTML = `
        <!-- Información de la Herramienta -->
        <div class="bg-gray-50 rounded p-4">
            <h4 class="text-md font-semibold text-emerald-700 mb-3">Información de la Herramienta</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <p class="text-sm text-gray-900 mt-1">${loan.tool_name}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Categoría</label>
                    <p class="text-sm text-gray-900 mt-1">${loan.tool_category.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</p>
                </div>
            </div>
        </div>
        
        <!-- Información del Trabajador -->
        <div class="bg-gray-50 rounded p-4">
            <h4 class="text-md font-semibold text-emerald-700 mb-3">Información del Trabajador</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <p class="text-sm text-gray-900 mt-1">${loan.worker_name}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <p class="text-sm text-gray-900 mt-1">${loan.worker_email}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cantidad Prestada</label>
                    <p class="text-sm text-gray-900 mt-1">${loan.quantity}</p>
                </div>
            </div>
        </div>
        
        <!-- Información del Préstamo -->
        <div class="bg-gray-50 rounded p-4">
            <h4 class="text-md font-semibold text-emerald-700 mb-3">Información del Préstamo</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de Préstamo</label>
                    <p class="text-sm text-gray-900 mt-1">${loan.out_at}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha Límite</label>
                    <p class="text-sm text-gray-900 mt-1">${loan.due_at}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de Devolución</label>
                    <p class="text-sm text-gray-900 mt-1">${loan.returned_at}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                    <p class="text-sm mt-1">
                        <span class="px-2 py-1 text-xs rounded ${statusClass}">${statusText}</span>
                    </p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Condición al Devolver</label>
                    <p class="text-sm text-gray-900 mt-1">${loan.condition}</p>
                </div>
            </div>
        </div>
    `;
    
    // Reinicializar iconos de Lucide
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function getLoanStatusClass(status) {
    switch(status) {
        case 'out': return 'bg-blue-100 text-blue-700';
        case 'returned': return 'bg-emerald-100 text-emerald-700';
        case 'lost': return 'bg-orange-100 text-orange-700';
        case 'damaged': return 'bg-red-100 text-red-700';
        default: return 'bg-gray-100 text-gray-700';
    }
}

function getLoanStatusText(status) {
    switch(status) {
        case 'out': return 'Prestado';
        case 'returned': return 'Devuelto';
        case 'lost': return 'Perdido';
        case 'damaged': return 'Dañado';
        default: return 'Desconocido';
    }
}

function closeLoanDetailsModal() {
    document.getElementById('loanDetailsModal').style.display = 'none';
}

// Funciones para acciones de préstamo
async function returnLoan(loanId) {
    if (!confirm('¿Estás seguro de marcar como devuelto este préstamo?')) {
        return;
    }
    
    try {
        const response = await fetch(`/foreman/loans/${loanId}/return`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Recargar la página para actualizar la tabla
            window.location.reload();
            
            // Mostrar notificación
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message || 'Error al devolver la herramienta', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error de conexión. Inténtalo de nuevo.', 'error');
    }
}

async function markAsLost(loanId) {
    if (!confirm('¿Estás seguro de marcar como perdido este préstamo? Esta acción no se puede deshacer.')) {
        return;
    }
    
    try {
        const response = await fetch(`/foreman/loans/${loanId}/mark-lost`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Recargar la página para actualizar la tabla
            window.location.reload();
            
            // Mostrar notificación
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message || 'Error al marcar como perdido', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error de conexión. Inténtalo de nuevo.', 'error');
    }
}

async function markAsDamaged(loanId) {
    if (!confirm('¿Estás seguro de marcar como dañado este préstamo?')) {
        return;
    }
    
    try {
        const response = await fetch(`/foreman/loans/${loanId}/mark-damaged`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Recargar la página para actualizar la tabla
            window.location.reload();
            
            // Mostrar notificación
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message || 'Error al marcar como dañado', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error de conexión. Inténtalo de nuevo.', 'error');
    }
}

async function deleteLoan(loanId) {
    if (!confirm('¿Estás seguro de eliminar este préstamo? Esta acción no se puede deshacer.')) {
        return;
    }
    
    try {
        const response = await fetch(`/foreman/loans/${loanId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Recargar la página para actualizar la tabla
            window.location.reload();
            
            // Mostrar notificación
            showNotification(data.message, 'success');
        } else {
            showNotification('Error al eliminar el préstamo', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error de conexión. Inténtalo de nuevo.', 'error');
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Cerrar modal al hacer clic fuera de él
document.getElementById('loanDetailsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeLoanDetailsModal();
    }
});
</script>
@endsection
