@extends('foreman.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Detalles de la Tarea</h2>
    <div class="flex space-x-2">
        <a href="{{ route('foreman.tasks.edit', $task) }}" class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700 transition-colors">
            <i data-lucide="edit" class="w-4 h-4 inline mr-2"></i>
            Editar
        </a>
        <a href="{{ route('foreman.tasks.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
            Volver
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Información Principal -->
    <div class="bg-white border rounded p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-emerald-700">Información General</h3>
            <div class="flex items-center space-x-2">
                <span class="px-3 py-1 text-sm rounded-full 
                    {{ $task->status === 'approved' ? 'bg-green-100 text-green-800' : 
                       ($task->status === 'completed' ? 'bg-blue-100 text-blue-800' : 
                       ($task->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 
                       ($task->status === 'pending' ? 'bg-gray-100 text-gray-800' : 
                       ($task->status === 'invalid' ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800')))) }}">
                    {{ ucfirst($task->status) }}
                </span>
                <span class="px-3 py-1 text-sm rounded-full bg-emerald-100 text-emerald-800">
                    {{ ucfirst($task->type) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-emerald-700 mb-1">Descripción</label>
                    <p class="text-emerald-800">{{ $task->description }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-emerald-700 mb-1">Trabajador Asignado</label>
                    <p class="text-emerald-800">{{ $task->assignee->name ?? 'Sin asignar' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-emerald-700 mb-1">Lote</label>
                    <p class="text-emerald-800">{{ $task->plot->name ?? 'Sin lote asignado' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-emerald-700 mb-1">Cultivo</label>
                    <p class="text-emerald-800">{{ $task->crop->name ?? 'Sin cultivo asignado' }}</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-emerald-700 mb-1">Fecha Programada</label>
                    <p class="text-emerald-800">{{ $task->scheduled_for ? $task->scheduled_for->format('d/m/Y') : 'Sin fecha programada' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-emerald-700 mb-1">Horas Trabajadas</label>
                    <p class="text-emerald-800">{{ $task->hours > 0 ? $task->hours . ' horas' : 'No registradas' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-emerald-700 mb-1">Kilos Producidos</label>
                    <p class="text-emerald-800">{{ $task->kilos > 0 ? $task->kilos . ' kg' : 'No registrados' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-emerald-700 mb-1">Fecha de Creación</label>
                    <p class="text-emerald-800">{{ $task->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Información de Aprobación -->
    @if($task->status === 'approved' && $task->approver)
        <div class="bg-white border rounded p-6">
            <h3 class="text-lg font-semibold text-emerald-700 mb-4">Información de Aprobación</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-emerald-700 mb-1">Aprobado por</label>
                    <p class="text-emerald-800">{{ $task->approver->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-emerald-700 mb-1">Fecha de Aprobación</label>
                    <p class="text-emerald-800">{{ $task->approved_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Acciones -->
    <div class="bg-white border rounded p-6">
        <h3 class="text-lg font-semibold text-emerald-700 mb-4">Acciones</h3>
        <div class="flex flex-wrap gap-3">
            @if($task->status === 'completed')
                <form method="POST" action="{{ route('foreman.tasks.approve', $task) }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition-colors"
                            onclick="return confirm('¿Estás seguro de aprobar esta tarea?')">
                        <i data-lucide="check" class="w-4 h-4 inline mr-2"></i>
                        Aprobar Tarea
                    </button>
                </form>
            @endif
            
            @if($task->status !== 'approved' && $task->status !== 'invalid')
                <form method="POST" action="{{ route('foreman.tasks.invalidate', $task) }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition-colors"
                            onclick="return confirm('¿Estás seguro de marcar esta tarea como inválida?')">
                        <i data-lucide="x" class="w-4 h-4 inline mr-2"></i>
                        Marcar como Inválida
                    </button>
                </form>
            @endif

            @if($task->status === 'pending' || $task->status === 'in_progress')
                <button type="button" onclick="showCompleteModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                    <i data-lucide="check-circle" class="w-4 h-4 inline mr-2"></i>
                    Marcar como Completada
                </button>
            @endif

            <form method="POST" action="{{ route('foreman.tasks.destroy', $task) }}" class="inline" 
                  onsubmit="return confirm('¿Estás seguro de eliminar esta tarea? Esta acción no se puede deshacer.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition-colors">
                    <i data-lucide="trash-2" class="w-4 h-4 inline mr-2"></i>
                    Eliminar Tarea
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal para completar tarea -->
<div id="completeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-emerald-700 mb-4">Completar Tarea</h3>
        <form method="POST" action="{{ route('foreman.tasks.complete', $task) }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="modal_hours" class="block text-sm font-medium text-emerald-700 mb-1">Horas Trabajadas *</label>
                    <input type="number" name="hours" id="modal_hours" step="0.5" min="0" max="24" required 
                           class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label for="modal_kilos" class="block text-sm font-medium text-emerald-700 mb-1">Kilos Producidos</label>
                    <input type="number" name="kilos" id="modal_kilos" step="0.1" min="0" 
                           class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>
            <div class="flex items-center justify-end space-x-3 mt-6">
                <button type="button" onclick="hideCompleteModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                    Completar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showCompleteModal() {
    document.getElementById('completeModal').classList.remove('hidden');
    document.getElementById('completeModal').classList.add('flex');
}

function hideCompleteModal() {
    document.getElementById('completeModal').classList.add('hidden');
    document.getElementById('completeModal').classList.remove('flex');
}
</script>
@endsection
