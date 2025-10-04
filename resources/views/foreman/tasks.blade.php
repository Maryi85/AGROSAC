@extends('foreman.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Gestionar Tareas</h2>
    <a href="{{ route('foreman.tasks.index') }}" class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700 transition-colors">
        <i data-lucide="settings" class="w-4 h-4 inline mr-2"></i>
        Administrar Tareas
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Tasks List -->
    <div class="bg-white border rounded">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-emerald-700">Lista de Tareas</h3>
        </div>
        
        <div class="p-6">
            @if($tasks->count() > 0)
                <div class="space-y-4">
                    @foreach($tasks as $task)
                        <div class="border border-emerald-200 rounded p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h4 class="font-medium text-emerald-800">{{ $task->description }}</h4>
                                    <div class="text-sm text-emerald-600 mt-1">
                                        <span class="inline-flex items-center">
                                            <i data-lucide="map-pin" class="w-4 h-4 mr-1"></i>
                                            {{ $task->plot->name ?? 'Sin lote asignado' }}
                                        </span>
                                        <span class="mx-2">•</span>
                                        <span class="inline-flex items-center">
                                            <i data-lucide="user" class="w-4 h-4 mr-1"></i>
                                            {{ $task->assignee->name ?? 'Sin asignar' }}
                                        </span>
                                        <span class="mx-2">•</span>
                                        <span class="inline-flex items-center">
                                            <i data-lucide="calendar" class="w-4 h-4 mr-1"></i>
                                            {{ $task->created_at->format('d/m/Y') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="px-3 py-1 text-sm rounded-full 
                                        {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                           ($task->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst($task->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-6">
                    {{ $tasks->links() }}
                </div>
            @else
                <div class="text-center py-8">
                    <i data-lucide="clipboard-list" class="w-12 h-12 text-emerald-300 mx-auto mb-4"></i>
                    <p class="text-emerald-600">No hay tareas registradas</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
