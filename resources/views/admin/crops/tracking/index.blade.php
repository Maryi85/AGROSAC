@extends('admin.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Seguimiento de Cultivos</h2>
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

    <!-- Botón para agregar nuevo seguimiento -->
    <div class="mb-6 flex justify-start">
        <a href="{{ route('admin.crop-tracking.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded-lg font-medium transition-colors">
            <i data-lucide="plus" class="w-5 h-5"></i>
            <span>Nuevo Seguimiento</span>
        </a>
    </div>

    <!-- Tabla de seguimientos -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-emerald-800 border-b">
                    <th class="py-3 pr-4">Fecha</th>
                    <th class="py-3 pr-4">Cultivo</th>
                    <th class="py-3 pr-4">Lote</th>
                    <th class="py-3 pr-4">Fase</th>
                    <th class="py-3 pr-4">Fecha de Corte</th>
                    <th class="py-3 pr-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($trackings as $tracking)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 pr-4">
                        <div class="font-medium text-gray-900">{{ $tracking->tracking_date->format('d/m/Y') }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="font-medium text-gray-900">{{ $tracking->crop->name ?? 'Cultivo eliminado' }}</div>
                        <div class="text-xs text-gray-500">{{ $tracking->crop->variety ?? 'Sin variedad' }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">
                            {{ $tracking->plot->name ?? 'Lote eliminado' }}
                        </span>
                    </td>
                    <td class="py-3 pr-4">
                        @if($tracking->phase)
                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">
                                {{ ucfirst($tracking->phase) }}
                            </span>
                        @else
                            <span class="text-gray-400 text-sm">—</span>
                        @endif
                    </td>
                    <td class="py-3 pr-4">
                        @if($tracking->cut_date)
                            <div class="text-sm text-gray-600">{{ $tracking->cut_date->format('d/m/Y') }}</div>
                        @else
                            <span class="text-gray-400 text-sm">—</span>
                        @endif
                    </td>
                    <td class="py-3 pr-4 text-right">
                        <div class="flex items-center gap-1 justify-end">
                            <!-- Ver detalles -->
                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 border border-blue-200 rounded hover:bg-blue-50 text-blue-600 view-tracking-btn" 
                                    data-tracking-id="{{ $tracking->id }}"
                                    data-tracking-date="{{ $tracking->tracking_date->format('d/m/Y') }}"
                                    data-crop-name="{{ $tracking->crop->name ?? 'Cultivo eliminado' }}"
                                    data-plot-name="{{ $tracking->plot->name ?? 'Lote eliminado' }}"
                                    data-phase="{{ $tracking->phase ?? '' }}"
                                    data-cut-date="{{ $tracking->cut_date ? $tracking->cut_date->format('d/m/Y') : '' }}"
                                    data-activities="{{ $tracking->activities ?? '' }}"
                                    title="Ver detalles">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                            
                            <!-- Editar -->
                            <a href="{{ route('admin.crop-tracking.edit', $tracking) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 border border-emerald-200 rounded hover:bg-emerald-50 text-emerald-600" 
                               title="Editar">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                            
                            <!-- Eliminar -->
                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 border border-red-200 rounded hover:bg-red-50 text-red-600 delete-tracking-btn" 
                                    data-tracking-id="{{ $tracking->id }}"
                                    data-crop-name="{{ $tracking->crop->name ?? 'Cultivo eliminado' }}"
                                    title="Eliminar">
                                <i data-lucide="trash" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-6 text-center text-emerald-800/70">No hay seguimientos registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $trackings->links() }}</div>
</div>

<!-- Modal de detalles -->
<div id="viewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-6 w-full max-w-2xl mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Detalles del Seguimiento</h3>
            <button type="button" onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de Seguimiento</label>
                    <p id="viewTrackingDate" class="text-sm text-gray-900"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cultivo</label>
                    <p id="viewCropName" class="text-sm text-gray-900"></p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Lote</label>
                    <p id="viewPlotName" class="text-sm text-gray-900"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fase</label>
                    <p id="viewPhase" class="text-sm text-gray-900"></p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de Corte</label>
                    <p id="viewCutDate" class="text-sm text-gray-900"></p>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Actividades</label>
                <p id="viewActivities" class="text-sm text-gray-900 whitespace-pre-line font-mono"></p>
            </div>
            
        </div>
        
        <div class="flex justify-end gap-2 mt-6">
            <button type="button" onclick="closeViewModal()" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
                Cerrar
            </button>
        </div>
    </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-6 w-full max-w-md mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="p-2 bg-red-100 rounded-full">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Confirmar Eliminación</h3>
        </div>
        
        <p class="text-gray-600 mb-6">
            ¿Está seguro de que desea eliminar el seguimiento del cultivo <span id="deleteCropName" class="font-semibold"></span>? 
            Esta acción no se puede deshacer.
        </p>
        
        <div class="flex justify-end gap-2">
            <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
                Cancelar
            </button>
            <button type="button" onclick="confirmDelete()" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 border border-red-200 rounded transition-colors">
                Eliminar
            </button>
        </div>
    </div>
</div>

<script>
let deleteTrackingId = null;

// Función para abrir el modal de detalles
function openViewModal(trackingId, trackingDate, cropName, plotName, phase, cutDate, activities) {
    document.getElementById('viewTrackingDate').textContent = trackingDate;
    document.getElementById('viewCropName').textContent = cropName;
    document.getElementById('viewPlotName').textContent = plotName;
    document.getElementById('viewPhase').textContent = phase || '—';
    document.getElementById('viewCutDate').textContent = cutDate || '—';
    document.getElementById('viewActivities').textContent = activities || 'Sin actividades registradas';
    
    document.getElementById('viewModal').style.display = 'flex';
}

// Función para cerrar el modal de detalles
function closeViewModal() {
    document.getElementById('viewModal').style.display = 'none';
}

// Función para abrir el modal de confirmación de eliminación
function openDeleteModal(trackingId, cropName) {
    deleteTrackingId = trackingId;
    document.getElementById('deleteCropName').textContent = cropName;
    document.getElementById('deleteModal').style.display = 'flex';
}

// Función para cerrar el modal de confirmación de eliminación
function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    deleteTrackingId = null;
}

// Función para confirmar la eliminación
async function confirmDelete() {
    if (!deleteTrackingId) return;
    
    const deleteButton = document.querySelector('#deleteModal button[onclick="confirmDelete()"]');
    const originalText = deleteButton.innerHTML;
    
    // Mostrar estado de carga
    deleteButton.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 inline mr-2 animate-spin"></i><span>Eliminando...</span>';
    deleteButton.disabled = true;
    
    try {
        const response = await fetch(`/admin/crop-tracking/${deleteTrackingId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (response.ok) {
            // Intentar parsear como JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                const data = await response.json();
                closeDeleteModal();
                showSuccessMessage(data.message || 'Seguimiento de cultivo eliminado correctamente');
                window.location.reload();
            } else {
                // Si no es JSON, probablemente es un redirect HTML
                closeDeleteModal();
                showSuccessMessage('Seguimiento de cultivo eliminado correctamente');
                window.location.reload();
            }
        } else {
            // Intentar obtener el mensaje de error
            try {
                const errorData = await response.json();
                showErrorMessage(errorData.message || 'Error al eliminar el seguimiento');
            } catch (e) {
                showErrorMessage('Error al eliminar el seguimiento');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        showErrorMessage('Error al eliminar el seguimiento: ' + error.message);
    } finally {
        // Restaurar el botón
        deleteButton.innerHTML = originalText;
        deleteButton.disabled = false;
    }
}

// Función para mostrar mensaje de éxito
function showSuccessMessage(message = 'Operación realizada correctamente') {
    const messageElement = document.createElement('div');
    messageElement.className = 'mb-4 p-3 bg-emerald-100 border border-emerald-300 text-emerald-700 rounded';
    messageElement.textContent = message;
    
    const content = document.querySelector('.bg-white.border.rounded.p-4');
    if (content) {
        content.insertBefore(messageElement, content.firstChild);
        
        // Remover el mensaje después de 4 segundos
        setTimeout(() => {
            if (messageElement.parentNode) {
                messageElement.parentNode.removeChild(messageElement);
            }
        }, 4000);
    }
}

// Función para mostrar mensaje de error
function showErrorMessage(message) {
    const messageElement = document.createElement('div');
    messageElement.className = 'mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded';
    messageElement.textContent = message;
    
    const content = document.querySelector('.bg-white.border.rounded.p-4');
    if (content) {
        content.insertBefore(messageElement, content.firstChild);
        
        // Remover el mensaje después de 5 segundos
        setTimeout(() => {
            if (messageElement.parentNode) {
                messageElement.parentNode.removeChild(messageElement);
            }
        }, 5000);
    }
}

// Inicialización cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    // Agregar eventos a los botones de ver detalles
    document.querySelectorAll('.view-tracking-btn').forEach(button => {
        button.addEventListener('click', function() {
            const trackingId = this.getAttribute('data-tracking-id');
            const trackingDate = this.getAttribute('data-tracking-date');
            const cropName = this.getAttribute('data-crop-name');
            const plotName = this.getAttribute('data-plot-name');
            const phase = this.getAttribute('data-phase');
            const cutDate = this.getAttribute('data-cut-date');
            const activities = this.getAttribute('data-activities');
            
            openViewModal(trackingId, trackingDate, cropName, plotName, phase, cutDate, activities);
        });
    });
    
    // Agregar eventos a los botones de eliminar
    document.querySelectorAll('.delete-tracking-btn').forEach(button => {
        button.addEventListener('click', function() {
            const trackingId = this.getAttribute('data-tracking-id');
            const cropName = this.getAttribute('data-crop-name');
            
            openDeleteModal(trackingId, cropName);
        });
    });
});
</script>
@endsection