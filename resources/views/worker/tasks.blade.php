@extends('worker.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Mis Tareas</h2>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filter Tabs -->
    <div class="bg-white border rounded p-4">
        <div class="flex space-x-1 bg-emerald-50 p-1 rounded-lg">
            <button onclick="filterTasks('all')" class="filter-btn px-4 py-2 rounded-md text-sm font-medium transition-colors bg-emerald-600 text-white" data-filter="all">
                Todas
            </button>
            <button onclick="filterTasks('pending')" class="filter-btn px-4 py-2 rounded-md text-sm font-medium transition-colors text-emerald-700 hover:bg-emerald-100" data-filter="pending">
                Pendientes
            </button>
            <button onclick="filterTasks('completed')" class="filter-btn px-4 py-2 rounded-md text-sm font-medium transition-colors text-emerald-700 hover:bg-emerald-100" data-filter="completed">
                Completadas
            </button>
        </div>
    </div>

    <!-- Tasks List -->
    <div class="space-y-4">
        @forelse($tasks as $task)
            <div class="bg-white border rounded-lg p-6 task-item" data-status="{{ $task->status }}">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-lg font-semibold text-emerald-800">{{ $task->description }}</h3>
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($task->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($task->status) }}
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-emerald-600">
                            <div>
                                <span class="font-medium">Lote:</span> {{ $task->plot->name ?? 'Sin lote' }}
                            </div>
                            <div>
                                <span class="font-medium">Cultivo:</span> {{ $task->crop->name ?? 'Sin cultivo' }}
                            </div>
                            @if($task->scheduled_for)
                                <div>
                                    <span class="font-medium">Fecha programada:</span> 
                                    <span class="{{ $task->scheduled_for < now() && $task->status !== 'completed' ? 'text-red-600' : '' }}">
                                        {{ $task->scheduled_for->format('d/m/Y') }}
                                    </span>
                                </div>
                            @endif
                            @if($task->priority)
                                <div>
                                    <span class="font-medium">Prioridad:</span> 
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        {{ $task->priority === 'high' ? 'bg-red-100 text-red-800' : 
                                           ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        @if($task->notes)
                            <div class="mt-3 p-3 bg-emerald-50 rounded border">
                                <span class="text-sm font-medium text-emerald-700">Notas:</span>
                                <p class="text-sm text-emerald-600 mt-1">{{ $task->notes }}</p>
                            </div>
                        @endif

                        @if($task->status === 'completed' && $task->description)
                            <div class="mt-3 p-3 bg-green-50 rounded border">
                                <span class="text-sm font-medium text-green-700">Descripción:</span>
                                <p class="text-sm text-green-600 mt-1">{{ $task->description }}</p>
                            </div>
                        @endif

                        @if($task->status === 'completed')
                            <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                @if($task->hours)
                                    <div class="p-2 bg-green-50 rounded">
                                        <span class="font-medium text-green-700">Horas trabajadas:</span>
                                        <span class="text-green-600">{{ $task->hours }}</span>
                                    </div>
                                @endif
                                @if($task->kilos)
                                    <div class="p-2 bg-green-50 rounded">
                                        <span class="font-medium text-green-700">Cantidad recolectada:</span>
                                        <span class="text-green-600">{{ $task->kilos }} kg</span>
                                    </div>
                                @endif
                                @if($task->updated_at)
                                    <div class="p-2 bg-green-50 rounded">
                                        <span class="font-medium text-green-700">Completada:</span>
                                        <span class="text-green-600">{{ $task->updated_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    @if($task->status === 'pending')
                        <div class="ml-4">
                            <button onclick="openCompleteModal({{ $task->id }}, '{{ $task->description }}')" 
                                    class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors">
                                <i data-lucide="check" class="w-4 h-4 inline mr-1"></i>
                                Completar
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white border rounded-lg p-8 text-center">
                <i data-lucide="clipboard-check" class="w-12 h-12 text-emerald-400 mx-auto mb-4"></i>
                <h3 class="text-lg font-semibold text-emerald-700 mb-2">No hay tareas asignadas</h3>
                <p class="text-emerald-600">No tienes tareas pendientes o completadas en este momento.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($tasks->hasPages())
        <div class="bg-white border rounded p-4">
            {{ $tasks->links() }}
        </div>
    @endif
</div>

<!-- Complete Task Modal -->
<div id="completeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold text-emerald-700 mb-4">Completar Tarea</h3>
            <form id="completeForm" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-emerald-700 mb-1">Tarea</label>
                        <p id="taskDescription" class="text-sm text-emerald-600 bg-emerald-50 p-2 rounded"></p>
                    </div>
                    
                    <div>
                        <label for="completion_notes" class="block text-sm font-medium text-emerald-700 mb-1">Notas de completado</label>
                        <textarea id="completion_notes" name="completion_notes" rows="3" 
                                  class="w-full border border-emerald-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                  placeholder="Describe el trabajo realizado..."></textarea>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="hours_worked" class="block text-sm font-medium text-emerald-700 mb-1">Horas trabajadas</label>
                            <input type="number" id="hours_worked" name="hours_worked" step="0.5" min="0"
                                   class="w-full border border-emerald-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                   placeholder="0">
                        </div>
                        
                        <div>
                            <label for="quantity_harvested" class="block text-sm font-medium text-emerald-700 mb-1">Cantidad recolectada</label>
                            <input type="number" id="quantity_harvested" name="quantity_harvested" min="0"
                                   class="w-full border border-emerald-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                   placeholder="0">
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeCompleteModal()" 
                            class="px-4 py-2 border border-emerald-300 text-emerald-700 rounded hover:bg-emerald-50">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">
                        Completar Tarea
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function filterTasks(status) {
    // Update button styles
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('bg-emerald-600', 'text-white');
        btn.classList.add('text-emerald-700', 'hover:bg-emerald-100');
    });
    
    document.querySelector(`[data-filter="${status}"]`).classList.add('bg-emerald-600', 'text-white');
    document.querySelector(`[data-filter="${status}"]`).classList.remove('text-emerald-700', 'hover:bg-emerald-100');
    
    // Filter tasks
    const tasks = document.querySelectorAll('.task-item');
    tasks.forEach(task => {
        if (status === 'all' || task.dataset.status === status) {
            task.style.display = 'block';
        } else {
            task.style.display = 'none';
        }
    });
}

function openCompleteModal(taskId, description) {
    document.getElementById('taskDescription').textContent = description;
    document.getElementById('completeForm').action = `/worker/tasks/${taskId}/complete`;
    document.getElementById('completeModal').classList.remove('hidden');
}

function closeCompleteModal() {
    document.getElementById('completeModal').classList.add('hidden');
    document.getElementById('completeForm').reset();
}

// Close modal when clicking outside
document.getElementById('completeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCompleteModal();
    }
});
</script>
@endsection
