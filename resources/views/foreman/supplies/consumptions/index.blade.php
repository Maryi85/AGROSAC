@extends('foreman.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Gestión de Consumos de Insumos</h2>
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

    <!-- Botones de acción -->
    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('foreman.supplies.index') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 border border-blue-200 rounded">
            <i data-lucide="flask-round" class="w-4 h-4"></i>
            <span>Ver Insumos</span>
        </a>
        <a href="{{ route('foreman.supply-consumptions.create') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Registrar Consumo</span>
        </a>
    </div>

    <!-- Filtros -->
    <div class="mb-4">
        <form action="{{ route('foreman.supply-consumptions.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <!-- Insumo -->
                <div>
                    <label for="supply_id" class="block text-sm mb-1 text-emerald-800">Insumo</label>
                    <select name="supply_id" id="supply_id" 
                            class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="all">Todos los insumos</option>
                        @foreach($supplies as $supply)
                            <option value="{{ $supply->id }}" {{ request('supply_id') == $supply->id ? 'selected' : '' }}>
                                {{ $supply->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Cultivo -->
                <div>
                    <label for="crop_id" class="block text-sm mb-1 text-emerald-800">Cultivo</label>
                    <select name="crop_id" id="crop_id" 
                            class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="all">Todos los cultivos</option>
                        @foreach($crops as $crop)
                            <option value="{{ $crop->id }}" {{ request('crop_id') == $crop->id ? 'selected' : '' }}>
                                {{ $crop->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Lote -->
                <div>
                    <label for="plot_id" class="block text-sm mb-1 text-emerald-800">Lote</label>
                    <select name="plot_id" id="plot_id" 
                            class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="all">Todos los lotes</option>
                        @foreach($plots as $plot)
                            <option value="{{ $plot->id }}" {{ request('plot_id') == $plot->id ? 'selected' : '' }}>
                                {{ $plot->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Fecha Desde -->
                <div>
                    <label for="date_from" class="block text-sm mb-1 text-emerald-800">Fecha Desde</label>
                    <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}"
                           class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <!-- Fecha Hasta -->
                <div>
                    <label for="date_to" class="block text-sm mb-1 text-emerald-800">Fecha Hasta</label>
                    <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}"
                           class="w-full border border-emerald-200 rounded px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>

            <!-- Botones -->
            <div class="mt-4 flex items-center gap-2">
                <button type="submit" class="px-3 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded inline-flex items-center gap-2 transition-colors">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>Filtrar</span>
                </button>
                <a href="{{ route('foreman.supply-consumptions.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 inline-flex items-center gap-2">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    <span>Limpiar</span>
                </a>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Insumo</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cultivo</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lote</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarea</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Costo Total</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Uso</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($consumptions as $consumption)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $consumption->supply->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $consumption->crop->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $consumption->plot->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $consumption->task->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ number_format($consumption->qty, 2) }} {{ $consumption->supply->unit }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            ${{ number_format($consumption->total_cost, 2) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ $consumption->used_at->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-right space-x-1">
                            <div class="inline-flex items-center gap-2">
                                <!-- Ver detalles -->
                                <a href="{{ route('foreman.supply-consumptions.show', $consumption) }}" 
                                   class="p-1 text-emerald-600 hover:text-emerald-900" 
                                   title="Ver detalles">
                                    <i data-lucide="eye" class="w-5 h-5"></i>
                                </a>
                                
                                <!-- Editar -->
                                <a href="{{ route('foreman.supply-consumptions.edit', $consumption) }}" 
                                   class="p-1 text-blue-600 hover:text-blue-900" 
                                   title="Editar">
                                    <i data-lucide="edit" class="w-5 h-5"></i>
                                </a>
                                
                                <!-- Eliminar -->
                                <form action="{{ route('foreman.supply-consumptions.destroy', $consumption) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('¿Está seguro de que desea eliminar este consumo?')" 
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-1 text-red-600 hover:text-red-900" 
                                            title="Eliminar">
                                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="px-4 py-3 border-t border-gray-200">
        {{ $consumptions->links() }}
    </div>
</div>
@endsection