@extends('foreman.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Gestión de Tareas</h2>
</div>
@endsection

@section('content')
<div x-data="{ 
    selectedTask: null, 
    showModal: false,
    allSupplies: {{ Js::from($allSupplies) }},
    statusLabels: {{ Js::from($statuses) }},
    
    openTask(task) {
        this.selectedTask = task;
        this.showModal = true;
        this.$nextTick(() => {
            if (window.lucide) window.lucide.createIcons();
        });
    }
}" @keydown.escape.window="showModal = false">

    <div class="bg-white border rounded p-4">
        <!-- Botón para asignar nueva tarea -->
        <div class="mb-6 flex flex-wrap justify-between items-center gap-3">
            <a href="{{ route('foreman.tasks.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded-lg font-medium transition-colors">
                <i data-lucide="plus" class="w-5 h-5"></i>
                <span>Asignar Tarea</span>
            </a>
            <a href="{{ route('foreman.tasks.pdf', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 border border-red-200 rounded-lg font-medium transition-colors">
                <i data-lucide="file-text" class="w-5 h-5"></i>
                <span>Descargar PDF</span>
            </a>
        </div>

        <!-- Filtros -->
        <form method="GET" class="mb-4 flex flex-wrap gap-2">
            <input type="text" name="q" value="{{ $search }}" placeholder="Buscar por descripción o trabajador" class="border border-emerald-200 rounded px-3 py-2 flex-1 min-w-0" style="min-width: 150px;" />
            <select name="status" class="border border-emerald-200 rounded px-3 py-2 w-full sm:w-auto">
                <option value="">Todos los estados</option>
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" {{ $status === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button class="px-3 py-2 border border-emerald-300 rounded text-emerald-700 hover:bg-emerald-100 inline-flex items-center gap-2 w-full sm:w-auto justify-center">
                <i data-lucide="search" class="w-4 h-4"></i>
                <span>Filtrar</span>
            </button>
        </form>

        <!-- Tabla de tareas (Desktop) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-emerald-800 border-b">
                        <th class="py-3 pr-4">Tipo</th>
                        <th class="py-3 pr-4">Descripción</th>
                        <th class="py-3 pr-4">Trabajador</th>
                        <th class="py-3 pr-4">Lote</th>
                        <th class="py-3 pr-4">Fecha</th>
                        <th class="py-3 pr-4 text-center">Insumos</th>
                        <th class="py-3 pr-4">Estado</th>
                        <th class="py-3 pr-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tasks as $task)
                    <tr id="task-row-{{ $task->id }}" class="border-b hover:bg-gray-50"
                        data-price-hour="{{ (int)$task->price_per_hour }}"
                        data-price-day="{{ (int)$task->price_per_day }}"
                        data-price-kg="{{ (int)$task->price_per_kg }}"
                        data-hours="{{ $task->hours }}"
                        data-kilos="{{ $task->kilos }}"
                        data-total-payment="{{ $task->total_payment }}"
                        data-estimated-hours="{{ $task->estimated_hours ?? ((int)$task->price_per_hour > 0 ? ($task->total_payment / (int)$task->price_per_hour) : 0) }}"
                        data-estimated-total="{{ $task->estimated_total_payment ?? $task->total_payment }}"
                        data-creator-name="{{ $task->creator->name ?? 'Administración' }}"
                        data-raw-status="{{ $task->status }}"
                        data-notes="{{ addslashes($task->notes ?? '') }}">
                        <td class="py-3 pr-4">
                            <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">
                                {{ ucfirst($task->type) }}
                            </span>
                        </td>
                        <td class="py-3 pr-4 max-w-xs">
                            <div class="truncate" title="{{ $task->description }}">
                                {{ $task->description }}
                            </div>
                        </td>
                        <td class="py-3 pr-4">{{ $task->assignee->name ?? '—' }}</td>
                        <td class="py-3 pr-4">{{ $task->plot->name ?? '—' }}</td>
                        <td class="py-3 pr-4">{{ $task->scheduled_for?->format('d/m/Y') ?? '—' }}</td>
                        <td class="py-3 pr-4 text-center">
                            @if(!empty($task->supplies_data))
                                <span class="px-2 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-lg border border-emerald-100">
                                    {{ count($task->supplies_data) }} items
                                </span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="py-3 pr-4">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'in_progress' => 'bg-blue-100 text-blue-700',
                                    'completed' => 'bg-green-100 text-green-700',
                                    'approved' => 'bg-emerald-100 text-emerald-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                    'invalid' => 'bg-gray-100 text-gray-700',
                                ];
                            @endphp
                            <span id="status-badge-{{ $task->id }}" class="status-badge px-2 py-1 text-xs rounded {{ $statusColors[$task->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $statuses[$task->status] ?? ucfirst($task->status) }}
                            </span>
                        </td>
                        <td class="py-3 pr-4 text-right">
                            <div class="flex items-center gap-1 justify-end">
                                <!-- Ver detalles -->
                                <button @click="openTask({{ Js::from($task) }})" class="inline-flex items-center gap-1 px-2 py-1 border rounded hover:bg-blue-50 text-blue-600 shadow-sm transition-all" title="Ver detalles">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                                
                                <!-- Editar -->
                                @if(in_array($task->status, ['pending', 'in_progress']))
                                    <a href="{{ route('foreman.tasks.edit', $task) }}" class="inline-flex items-center gap-1 px-2 py-1 border rounded hover:bg-emerald-50 text-emerald-600 shadow-sm transition-all" title="Editar">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                @endif
                                
                                <!-- Aprobar -->
                                @if($task->status === 'completed')
                                    <form method="POST" action="{{ route('foreman.tasks.approve', $task) }}" class="ajax-task-form inline" data-confirm="true" data-message="¿Aprobar esta tarea?">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 px-2 py-1 border rounded hover:bg-green-50 text-green-600 shadow-sm transition-all" title="Aprobar">
                                            <i data-lucide="check" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                <!-- Invalidar -->
                                @if($task->status === 'completed')
                                    <form method="POST" action="{{ route('foreman.tasks.invalidate', $task) }}" class="ajax-task-form inline" data-confirm="true" data-message="¿Marcar esta tarea como inválida?">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 px-2 py-1 border rounded hover:bg-red-50 text-red-600 shadow-sm transition-all" title="Invalidar">
                                            <i data-lucide="x" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                <!-- Eliminar -->
                                @if(in_array($task->status, ['pending', 'in_progress']))
                                    <form method="POST" action="{{ route('foreman.tasks.destroy', $task) }}" class="ajax-task-form inline" data-confirm="true" data-message="¿Eliminar esta tarea?">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex items-center gap-1 px-2 py-1 border rounded hover:bg-red-50 text-red-600 shadow-sm transition-all" title="Eliminar">
                                            <i data-lucide="trash" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-6 text-center text-emerald-800/70">No hay tareas registradas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="grid grid-cols-1 gap-4 md:hidden">
            @forelse ($tasks as $task)
            <div id="task-card-{{ $task->id }}" class="bg-white border rounded-lg p-4 shadow-sm relative">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700 font-medium">
                            {{ ucfirst($task->type) }}
                        </span>
                        <h3 class="font-semibold text-gray-900 mt-1">{{ $task->description }}</h3>
                    </div>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-700',
                            'in_progress' => 'bg-blue-100 text-blue-700',
                            'completed' => 'bg-green-100 text-green-700',
                            'approved' => 'bg-emerald-100 text-emerald-700',
                            'rejected' => 'bg-red-100 text-red-700',
                            'invalid' => 'bg-gray-100 text-gray-700',
                        ];
                    @endphp
                    <span id="mobile-status-badge-{{ $task->id }}" class="status-badge px-2 py-1 text-xs rounded {{ $statusColors[$task->status] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ $statuses[$task->status] ?? ucfirst($task->status) }}
                    </span>
                </div>
                
                <div class="space-y-1 text-sm text-gray-600 mb-3">
                    <div class="flex items-center gap-2">
                        <i data-lucide="user" class="w-4 h-4 text-emerald-600"></i>
                        <span>{{ $task->assignee->name ?? '—' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-4 h-4 text-emerald-600"></i>
                        <span>{{ $task->plot->name ?? '—' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="calendar" class="w-4 h-4 text-emerald-600"></i>
                        <span>{{ $task->scheduled_for?->format('d/m/Y') ?? '—' }}</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 border-t pt-3">
                     <button @click="openTask({{ Js::from($task) }})" class="p-2 text-blue-600 bg-blue-50 rounded-lg shadow-sm" title="Ver detalles">
                        <i data-lucide="eye" class="w-5 h-5"></i>
                    </button>
                    
                    @if(in_array($task->status, ['pending', 'in_progress']))
                        <a href="{{ route('foreman.tasks.edit', $task) }}" class="p-2 text-emerald-600 bg-emerald-50 rounded-lg shadow-sm" title="Editar">
                            <i data-lucide="pencil" class="w-5 h-5"></i>
                        </a>
                    @endif
                    
                    @if($task->status === 'completed')
                        <form method="POST" action="{{ route('foreman.tasks.approve', $task) }}" class="inline" data-confirm="true" data-message="¿Aprobar esta tarea?">
                            @csrf
                            <button class="p-2 text-green-600 bg-green-50 rounded-lg shadow-sm" title="Aprobar">
                                <i data-lucide="check" class="w-5 h-5"></i>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('foreman.tasks.invalidate', $task) }}" class="inline" data-confirm="true" data-message="¿Marcar esta tarea como inválida?">
                            @csrf
                            <button class="p-2 text-red-600 bg-red-50 rounded-lg shadow-sm" title="Invalidar">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </form>
                    @endif
                    
                    @if(in_array($task->status, ['pending', 'in_progress']))
                        <form method="POST" action="{{ route('foreman.tasks.destroy', $task) }}" class="ajax-task-form inline" data-confirm="true" data-message="¿Eliminar esta tarea?">
                            @csrf
                            @method('DELETE')
                            <button class="p-2 text-red-600 bg-red-50 rounded-lg shadow-sm" title="Eliminar">
                                <i data-lucide="trash" class="w-5 h-5"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="py-8 text-center text-gray-500 bg-gray-50 rounded-lg border border-dashed text-sm">
                No hay tareas registradas
            </div>
            @endforelse
        </div>

        <div class="mt-4">{{ $tasks->links() }}</div>
    </div>

    <!-- Modal de Detalles -->
    <div x-show="showModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showModal = false"></div>

            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <template x-if="selectedTask">
                    <div class="relative">
                        <!-- Header -->
                        <div class="flex items-center justify-between p-6 border-b border-gray-100 bg-emerald-50/30">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-emerald-100 text-emerald-700 rounded-xl">
                                    <i data-lucide="clipboard-list" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-gray-900 leading-none" x-text="'Tarea #' + selectedTask.id"></h3>
                                    <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mt-1" x-text="selectedTask.type"></p>
                                </div>
                            </div>
                            <button @click="showModal = false" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all">
                                <i data-lucide="x" class="w-6 h-6"></i>
                            </button>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <!-- Columna Principal -->
                                <div class="lg:col-span-2 space-y-6">
                                    <!-- Descripción -->
                                    <div class="bg-gray-50/50 rounded-2xl p-5 border border-gray-100">
                                        <div class="flex items-center gap-2 mb-3 text-emerald-700">
                                            <i data-lucide="align-left" class="w-4 h-4"></i>
                                            <span class="text-xs font-black uppercase tracking-widest">Descripción</span>
                                        </div>
                                        <p class="text-gray-700 text-sm italic leading-relaxed" x-text="selectedTask.description || 'Sin descripción'"></p>
                                    </div>

                                    <!-- Insumos -->
                                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                                        <div class="p-4 border-b border-gray-50 flex items-center gap-2">
                                            <div class="p-1.5 bg-emerald-50 rounded-lg text-emerald-600">
                                                <i data-lucide="package" class="w-4 h-4"></i>
                                            </div>
                                            <h4 class="text-sm font-bold text-gray-900">Insumos Requeridos</h4>
                                        </div>
                                        <div class="max-h-[300px] overflow-y-auto">
                                            <table class="w-full text-left border-collapse">
                                                <thead class="bg-gray-50/50 uppercase text-[9px] font-black text-gray-400 tracking-widest">
                                                    <tr>
                                                        <th class="px-5 py-3">Insumo</th>
                                                        <th class="px-5 py-3 text-right">Cantidad</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-50 px-5">
                                                    <template x-if="selectedTask.supplies_data && selectedTask.supplies_data.length > 0">
                                                        <template x-for="item in selectedTask.supplies_data" :key="item.supply_id">
                                                            <tr class="hover:bg-gray-50/30 transition-colors">
                                                                <td class="px-5 py-3">
                                                                    <div class="font-bold text-gray-900 text-sm" x-text="allSupplies[item.supply_id] ? allSupplies[item.supply_id].name : 'Insumo #' + item.supply_id"></div>
                                                                    <div class="text-[9px] text-gray-400 font-bold uppercase tracking-wider" x-text="allSupplies[item.supply_id] ? allSupplies[item.supply_id].category : 'General'"></div>
                                                                </td>
                                                                <td class="px-5 py-3 text-right">
                                                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-black" x-text="item.quantity + ' ' + (allSupplies[item.supply_id] ? allSupplies[item.supply_id].unit : '')"></span>
                                                                </td>
                                                            </tr>
                                                        </template>
                                                    </template>
                                                    <template x-if="!selectedTask.supplies_data || selectedTask.supplies_data.length === 0">
                                                        <tr>
                                                            <td colspan="2" class="px-5 py-8 text-center text-gray-400 text-xs font-medium italic">Sin insumos registrados</td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Ubicación -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                                            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                                                <i data-lucide="map-pin" class="w-5 h-5"></i>
                                            </div>
                                            <div>
                                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Lote</p>
                                                <p class="text-sm font-bold text-gray-900" x-text="selectedTask.plot ? selectedTask.plot.name : 'Sin lote'"></p>
                                            </div>
                                        </div>
                                        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                                            <div class="p-3 bg-orange-50 text-orange-600 rounded-xl">
                                                <i data-lucide="sprout" class="w-5 h-5"></i>
                                            </div>
                                            <div>
                                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Cultivo</p>
                                                <p class="text-sm font-bold text-gray-900" x-text="selectedTask.crop ? selectedTask.crop.name : 'Sin cultivo'"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Información de Pago (Añadido) -->
                                    <div class="pt-6 border-t border-gray-100" x-show="selectedTask.price_per_hour > 0">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Información de Pago</p>
                                        
                                        <!-- En progreso o pendiente: Vista Simple -->
                                        <template x-if="['pending', 'in_progress'].includes(selectedTask.status)">
                                            <div class="grid grid-cols-2 gap-4 bg-emerald-50/50 p-6 rounded-2xl border border-emerald-100/50 shadow-sm">
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest mb-1">Precio por hora</p>
                                                    <p class="text-lg font-black text-gray-900" x-text="'$' + (new Intl.NumberFormat('es-CO').format(parseFloat(selectedTask.price_per_hour) || 0))"></p>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest mb-1">Horas estimadas</p>
                                                    <p class="text-lg font-black text-gray-900" x-text="(parseFloat(selectedTask.estimated_hours) || (parseFloat(selectedTask.total_payment) / parseFloat(selectedTask.price_per_hour)) || 0).toFixed(0) + 'h'"></p>
                                                </div>
                                                <div class="col-span-2 pt-4 border-t border-gray-100">
                                                    <p class="text-[10px] text-emerald-600 uppercase font-black tracking-widest mb-1">Total estimado</p>
                                                    <p class="text-2xl font-black text-emerald-700" x-text="'$' + (new Intl.NumberFormat('es-CO').format(parseFloat(selectedTask.estimated_total_payment) || parseFloat(selectedTask.total_payment) || 0))"></p>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Completada o Aprobada: Comparativa -->
                                        <template x-if="['completed', 'approved'].includes(selectedTask.status)">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <!-- Asignación -->
                                                <div class="bg-gray-50/50 p-5 rounded-2xl border border-gray-100">
                                                    <div class="flex items-center gap-2 mb-4">
                                                        <div class="p-1.5 bg-gray-200/50 rounded-lg text-gray-500">
                                                            <i data-lucide="settings" class="w-4 h-4"></i>
                                                        </div>
                                                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest" x-text="'Establecido por ' + (selectedTask.creator ? selectedTask.creator.name : 'Administración')"></p>
                                                    </div>
                                                    <div class="space-y-3">
                                                        <div class="flex justify-between items-center text-xs">
                                                            <span class="text-gray-400 font-bold uppercase tracking-wider">Precio/h</span>
                                                            <span class="text-gray-900 font-bold" x-text="'$' + (new Intl.NumberFormat('es-CO').format(parseFloat(selectedTask.price_per_hour) || 0))"></span>
                                                        </div>
                                                        <div class="flex justify-between items-center text-xs">
                                                            <span class="text-gray-400 font-bold uppercase tracking-wider">Horas Est.</span>
                                                            <span class="text-gray-900 font-bold" x-text="(parseFloat(selectedTask.estimated_hours) || (parseFloat(selectedTask.total_payment) / parseFloat(selectedTask.price_per_hour)) || 0).toFixed(0) + 'h'"></span>
                                                        </div>
                                                        <div class="pt-3 border-t border-gray-200/50 flex justify-between items-center">
                                                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Total Est.</span>
                                                            <span class="text-base font-black text-gray-900" x-text="'$' + (new Intl.NumberFormat('es-CO').format(parseFloat(selectedTask.estimated_total_payment) || parseFloat(selectedTask.total_payment) || 0))"></span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Registro -->
                                                <div class="bg-emerald-50/30 p-5 rounded-2xl border border-emerald-100 shadow-sm shadow-emerald-50">
                                                    <div class="flex items-center gap-2 mb-4">
                                                        <div class="p-1.5 bg-emerald-100 rounded-lg text-emerald-600">
                                                            <i data-lucide="user" class="w-4 h-4"></i>
                                                        </div>
                                                        <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Registro del Trabajador</p>
                                                    </div>
                                                    <div class="space-y-3">
                                                        <div class="flex justify-between items-center text-xs">
                                                            <span class="text-emerald-600/60 font-bold uppercase tracking-wider">Precio/h</span>
                                                            <span class="text-emerald-900 font-bold" x-text="'$' + (new Intl.NumberFormat('es-CO').format(parseFloat(selectedTask.price_per_hour) || 0))"></span>
                                                        </div>
                                                        <div class="flex justify-between items-center text-xs">
                                                            <span class="text-emerald-600/60 font-bold uppercase tracking-wider">Horas Reg.</span>
                                                            <span class="text-emerald-900 font-bold" x-text="(parseFloat(selectedTask.hours) || 0) + 'h'"></span>
                                                        </div>
                                                        <div class="pt-3 border-t border-emerald-100 flex justify-between items-center">
                                                            <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">Total Reg.</span>
                                                            <span class="text-base font-black text-emerald-700" x-text="'$' + (new Intl.NumberFormat('es-CO').format((parseFloat(selectedTask.hours) || 0) * (parseFloat(selectedTask.price_per_hour) || 0)))"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Columna de Estado y Datos -->
                                <div class="space-y-6">
                                    <!-- Estado -->
                                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-center">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Estado de la Tarea</p>
                                        <div class="flex items-center justify-center">
                                            <template x-if="selectedTask.status === 'pending'">
                                                <span class="px-4 py-1.5 rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200 text-sm font-black">Pendiente</span>
                                            </template>
                                            <template x-if="selectedTask.status === 'in_progress'">
                                                <span class="px-4 py-1.5 rounded-full bg-blue-100 text-blue-800 border border-blue-200 text-sm font-black">En Progreso</span>
                                            </template>
                                            <template x-if="selectedTask.status === 'completed'">
                                                <span class="px-4 py-1.5 rounded-full bg-green-100 text-green-800 border border-green-200 text-sm font-black">Completada</span>
                                            </template>
                                            <template x-if="selectedTask.status === 'approved'">
                                                <span class="px-4 py-1.5 rounded-full bg-emerald-600 text-white shadow-sm text-sm font-black">Aprobada</span>
                                            </template>
                                            <template x-if="selectedTask.status === 'invalid'">
                                                <span class="px-4 py-1.5 rounded-full bg-gray-100 text-gray-500 border border-gray-200 text-sm font-black text-strike">Inválida</span>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Info Trabajador -->
                                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-10 h-10 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center font-black text-sm" x-text="selectedTask.assignee ? selectedTask.assignee.name.substring(0,1) : '?'"></div>
                                            <div class="overflow-hidden">
                                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Asignado a</p>
                                                <p class="text-sm font-bold text-gray-900 truncate" x-text="selectedTask.assignee ? selectedTask.assignee.name : 'Sin asignar'"></p>
                                            </div>
                                        </div>
                                        <div class="pt-4 border-t border-gray-50 space-y-3">
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="text-gray-400 font-bold uppercase tracking-wider">Fecha</span>
                                                <span class="text-gray-900 font-bold" x-text="selectedTask.scheduled_for ? new Date(selectedTask.scheduled_for).toLocaleDateString() : '—'"></span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="text-gray-400 font-bold uppercase tracking-wider">Horas Rep.</span>
                                                <span class="text-gray-900 font-bold" x-text="(selectedTask.hours || 0) + 'h'"></span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="text-gray-400 font-bold uppercase tracking-wider">Kilos Rep.</span>
                                                <span class="text-gray-900 font-bold" x-text="(selectedTask.kilos || 0) + 'kg'"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Resumen de Pago (Sidebar) -->
                                    <template x-if="selectedTask.price_per_hour > 0">
                                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative">
                                            <div class="flex items-center gap-2 mb-4">
                                                <div class="p-1.5 bg-emerald-50 rounded-lg text-emerald-600">
                                                    <i data-lucide="wallet" class="w-4 h-4"></i>
                                                </div>
                                                <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest" x-text="selectedTask.status === 'approved' ? 'Total Liquidado' : 'Total Estimado'"></p>
                                            </div>
                                            
                                            <div class="relative z-10">
                                                <p class="text-3xl font-black text-gray-900 leading-none mb-2" x-text="'$' + (new Intl.NumberFormat('es-CO').format(selectedTask.status === 'approved' ? selectedTask.total_payment : (selectedTask.status === 'completed' ? (selectedTask.hours * selectedTask.price_per_hour) : selectedTask.total_payment)))"></p>
                                                <template x-if="selectedTask.status !== 'approved'">
                                                    <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest italic">* Sujeto a aprobación</p>
                                                </template>
                                                <template x-if="selectedTask.status === 'approved'">
                                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest italic">Aprobado por <span x-text="selectedTask.approver ? selectedTask.approver.name : 'Admin'"></span></p>
                                                </template>
                                            </div>

                                            <!-- Decoration -->
                                            <div class="absolute -right-4 -bottom-4 opacity-[0.03] rotate-12">
                                                <i data-lucide="wallet" class="w-24 h-24 text-gray-900"></i>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="p-6 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                             <div class="flex gap-2">
                                <template x-if="selectedTask.status === 'completed'">
                                    <div class="flex gap-2">
                                        <form method="POST" :action="'{{ url('admin/tasks') }}/' + selectedTask.id + '/approve'" class="ajax-task-form" data-confirm="true">
                                            @csrf
                                            <button class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all flex items-center gap-2">
                                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                                Aprobar
                                            </button>
                                        </form>
                                        <form method="POST" :action="'{{ url('admin/tasks') }}/' + selectedTask.id + '/invalidate'" class="ajax-task-form" data-confirm="true">
                                            @csrf
                                            <button class="px-5 py-2.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                                                <i data-lucide="slash" class="w-4 h-4"></i>
                                                Invalidar
                                            </button>
                                        </form>
                                    </div>
                                </template>
                                
                                <template x-if="['pending', 'in_progress'].includes(selectedTask.status)">
                                    <div class="flex gap-2">
                                        <a :href="'{{ url('admin/tasks') }}/' + selectedTask.id + '/edit'" class="px-5 py-2.5 bg-white border border-emerald-200 text-emerald-700 hover:bg-emerald-50 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                            Editar
                                        </a>
                                    </div>
                                </template>
                             </div>

                            <button @click="showModal = false" class="px-6 py-2.5 bg-gray-900 hover:bg-black text-white rounded-xl text-xs font-bold transition-all">
                                Entendido
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusColors = {
        'pending': 'bg-yellow-100 text-yellow-700',
        'in_progress': 'bg-blue-100 text-blue-700',
        'completed': 'bg-green-100 text-green-700',
        'approved': 'bg-emerald-100 text-emerald-700',
        'rejected': 'bg-red-100 text-red-700',
        'invalid': 'bg-gray-100 text-gray-700',
    };

    const statusLabels = @json($statuses);

    // Delegate form submission to handle dynamic Alpine forms
    document.addEventListener('submit', function(e) {
        const form = e.target.closest('.ajax-task-form');
        if (!form) return;

        e.preventDefault();
        
        const message = form.dataset.message || '¿Estás seguro de realizar esta acción?';
        
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
            if (result.isConfirmed) {
                const url = form.getAttribute('action');
                const method = form.querySelector('input[name="_method"]')?.value || form.getAttribute('method');
                const formData = new FormData(form);

                fetch(url, {
                    method: method === 'DELETE' ? 'POST' : method,
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: '¡Éxito!',
                            text: data.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        const taskId = data.id || (data.task ? data.task.id : null);
                        
                        if (method === 'DELETE') {
                            // Remove the row/card from the table
                            const row = document.getElementById(`task-row-${taskId}`);
                            const card = document.getElementById(`task-card-${taskId}`);
                            if (row) row.remove();
                            if (card) card.remove();
                            
                            // Close modal if the deleted task was open
                            const alpineElement = document.querySelector('[x-data]');
                            if (alpineElement && alpineElement.__x) {
                                const alpineData = alpineElement.__x.$data;
                                if (alpineData.selectedTask && alpineData.selectedTask.id == taskId) {
                                    alpineData.showModal = false;
                                }
                            }

                        } else if (data.task) {
                            const task = data.task;
                            // Update logic
                            const badge = document.getElementById(`status-badge-${task.id}`);
                            const mobileBadge = document.getElementById(`mobile-status-badge-${task.id}`);
                            
                            [badge, mobileBadge].forEach(el => {
                                if (el) {
                                    el.className = `status-badge px-2 py-1 text-xs rounded ${statusColors[task.status] || 'bg-gray-100 text-gray-700'}`;
                                    el.textContent = statusLabels[task.status] || task.status;
                                }
                            });
                            
                            // Update Alpine data for modal
                            const alpineElement = document.querySelector('[x-data]');
                            if (alpineElement && alpineElement.__x) {
                                const alpineData = alpineElement.__x.$data;
                                if (alpineData.selectedTask && alpineData.selectedTask.id == task.id) {
                                    alpineData.selectedTask.status = task.status;
                                    // Optional: Hide modal or update its view
                                    if (task.status === 'approved' || task.status === 'invalid') {
                                        alpineData.showModal = false;
                                    }
                                }
                            }

                            // Refetch icons for any new content if necessary
                            if (window.lucide) lucide.createIcons();
                        }
                    } else {
                        Swal.fire('Error', data.message || 'Error al procesar la solicitud', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Ocurrió un error inesperado al conectar con el servidor', 'error');
                });
            }
        });
    });
});
</script>
@endpush
@endsection

