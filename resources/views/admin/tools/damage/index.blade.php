@extends('admin.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Daños y Pérdidas de Herramientas</h2>
</div>
@endsection

@section('content')
<!-- Resumen de totales -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i data-lucide="wrench" class="w-8 h-8 text-blue-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-blue-800">Total Herramientas</p>
                <p class="text-2xl font-bold text-blue-900">{{ $totalTools }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i data-lucide="package" class="w-8 h-8 text-green-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">Total Entradas</p>
                <p class="text-2xl font-bold text-green-900">{{ $totalEntries }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i data-lucide="check-circle" class="w-8 h-8 text-emerald-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-emerald-800">Disponibles</p>
                <p class="text-2xl font-bold text-emerald-900">{{ $totalAvailable }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i data-lucide="alert-triangle" class="w-8 h-8 text-orange-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-orange-800">Dañadas</p>
                <p class="text-2xl font-bold text-orange-900">{{ $totalDamaged }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i data-lucide="x-circle" class="w-8 h-8 text-red-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">Perdidas</p>
                <p class="text-2xl font-bold text-red-900">{{ $totalLost }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label for="tool_id" class="block text-sm font-medium text-gray-700 mb-2">Herramienta</label>
            <select name="tool_id" id="tool_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="all">Todas las herramientas</option>
                @foreach($allTools as $tool)
                    <option value="{{ $tool->id }}" {{ request('tool_id') == $tool->id ? 'selected' : '' }}>
                        {{ $tool->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
            <select name="status" id="status" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex items-end">
            <div class="flex gap-2 w-full">
                <a href="{{ route('admin.tool-damage.index') }}" 
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-200 rounded transition-colors">
                    Limpiar
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded transition-colors">
                    Filtrar
                </button>
                <a href="{{ route('admin.tool-damage.create') }}" 
                   class="px-4 py-2 bg-orange-100 hover:bg-orange-200 text-orange-700 border border-orange-200 rounded transition-colors">
                    <i data-lucide="alert-triangle" class="w-4 h-4 inline mr-1"></i>
                    Registrar Daño/Pérdida
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Tabla de herramientas -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-emerald-800 border-b bg-gray-50">
                    <th class="py-3 px-4">Herramienta</th>
                    <th class="py-3 px-4">Total Entradas</th>
                    <th class="py-3 px-4">Disponible</th>
                    <th class="py-3 px-4">Dañadas</th>
                    <th class="py-3 px-4">Perdidas</th>
                    <th class="py-3 px-4">Estado</th>
                    <th class="py-3 px-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tools as $tool)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4">
                        <div class="font-medium text-gray-900">{{ $tool->name }}</div>
                        <div class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $tool->category)) }}</div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="font-semibold text-gray-900">{{ $tool->total_entries }}</div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="font-semibold {{ $tool->available_qty > 0 ? 'text-green-600' : 'text-gray-500' }}">
                            {{ $tool->available_qty }}
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="font-semibold {{ $tool->damaged_qty > 0 ? 'text-orange-600' : 'text-gray-500' }}">
                            {{ $tool->damaged_qty }}
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="font-semibold {{ $tool->lost_qty > 0 ? 'text-red-600' : 'text-gray-500' }}">
                            {{ $tool->lost_qty }}
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        @php
                            $status = 'operational';
                            if ($tool->lost_qty > 0) $status = 'lost';
                            elseif ($tool->damaged_qty > 0) $status = 'damaged';
                            elseif ($tool->available_qty == 0) $status = 'empty';
                            
                            $statusClasses = [
                                'operational' => 'bg-green-100 text-green-700',
                                'damaged' => 'bg-orange-100 text-orange-700',
                                'lost' => 'bg-red-100 text-red-700',
                                'empty' => 'bg-gray-100 text-gray-700',
                            ];
                            $statusLabels = [
                                'operational' => 'Operacional',
                                'damaged' => 'Dañada',
                                'lost' => 'Perdida',
                                'empty' => 'Vacía',
                            ];
                        @endphp
                        <span class="px-2 py-1 text-xs rounded {{ $statusClasses[$status] }}">
                            {{ $statusLabels[$status] }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-right">
                        @php
                            $latestDamageEntry = $tool->entries
                                ->filter(function($entry){
                                    return ($entry->damaged_qty > 0) || ($entry->lost_qty > 0);
                                })
                                ->sortByDesc('entry_date')
                                ->first();
                        @endphp
                        <div class="flex items-center gap-1 justify-end">
                            <!-- Ver historial -->
                            <a href="{{ route('admin.tool-damage.show', $tool) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 border border-blue-200 rounded hover:bg-blue-50 text-blue-600" 
                               title="Ver historial de daños/pérdidas">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            <!-- Editar último registro -->
                            @if($latestDamageEntry)
                            <a href="{{ route('admin.tool-damage.edit', $latestDamageEntry->id) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 border border-emerald-200 rounded hover:bg-emerald-50 text-emerald-700" 
                               title="Editar último daño/pérdida">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                            @else
                            <span class="inline-flex items-center justify-center w-8 h-8 border border-gray-200 rounded text-gray-300 cursor-not-allowed" title="Sin registros para editar">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </span>
                            @endif
                            <!-- Registrar daño/pérdida -->
                            <a href="{{ route('admin.tool-damage.create', ['tool_id' => $tool->id]) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 border border-orange-200 rounded hover:bg-orange-50 text-orange-600" 
                               title="Registrar daño o pérdida">
                                <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-6 text-center text-emerald-800/70">No hay herramientas con daños o pérdidas</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    @if($tools->hasPages())
    <div class="px-6 py-4 border-t bg-gray-50">
        {{ $tools->links() }}
    </div>
    @endif
</div>
@endsection
