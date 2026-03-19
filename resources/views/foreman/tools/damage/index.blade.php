@extends('foreman.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Daños y Pérdidas de Herramientas</h2>
</div>
@endsection

@section('content')
<!-- Filtros -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex flex-wrap items-end justify-between gap-4 mb-4">
        <h3 class="text-lg font-semibold text-emerald-700 w-full sm:w-auto">Buscar Herramientas</h3>
        <a href="{{ route('foreman.tool-damage.create') }}" 
           class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3 bg-orange-100 hover:bg-orange-200 text-orange-700 border border-orange-200 rounded font-medium transition-colors w-full sm:w-auto">
            <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            <span>Registrar Daño/Pérdida</span>
        </a>
    </div>
    
    <div class="mb-4">
        <x-search-bar placeholder="Buscar por nombre de herramienta..." />
    </div>
</div>

<!-- Tabla de herramientas -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-emerald-800 border-b bg-gray-50">
                    <th class="py-3 px-4">Foto</th>
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
                        @if($tool->photo)
                            <img src="{{ storage_asset($tool->photo) }}" alt="{{ $tool->name }}" class="w-10 h-10 rounded-full object-cover border border-gray-200">
                        @else
                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                <i data-lucide="image" class="w-5 h-5"></i>
                            </div>
                        @endif
                    </td>
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
                            <a href="{{ route('foreman.tool-damage.show', $tool) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 border border-blue-200 rounded hover:bg-blue-50 text-blue-600" 
                               title="Ver historial de daños/pérdidas">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            <!-- Editar último registro -->
                            @if($latestDamageEntry)
                            <a href="{{ route('foreman.tool-damage.edit', $latestDamageEntry->id) }}" 
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
                            <a href="{{ route('foreman.tool-damage.create', ['tool_id' => $tool->id]) }}" 
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

