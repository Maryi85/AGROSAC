@extends('admin.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Detalles de Daños y Pérdidas</h2>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.tool-damage.create', ['tool_id' => $tool->id]) }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-orange-100 hover:bg-orange-200 text-orange-700 border border-orange-200 rounded transition-colors">
            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
            <span>Registrar Nuevo</span>
        </a>
        <a href="{{ route('admin.tool-damage.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-200 rounded transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Volver</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Información de la herramienta -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Información de la Herramienta</h3>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre</label>
                <p class="mt-1 text-sm text-gray-900">{{ $tool->name }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Categoría</label>
                <p class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $tool->category)) }}</p>
            </div>

            @if($tool->brand)
            <div>
                <label class="block text-sm font-medium text-gray-700">Marca</label>
                <p class="mt-1 text-sm text-gray-900">{{ $tool->brand }}</p>
            </div>
            @endif

            @if($tool->model)
            <div>
                <label class="block text-sm font-medium text-gray-700">Modelo</label>
                <p class="mt-1 text-sm text-gray-900">{{ $tool->model }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Resumen de inventario -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Resumen de Inventario</h3>
        
        <div class="space-y-4">
            <div class="flex justify-between items-center p-3 bg-blue-50 rounded">
                <span class="text-blue-800 font-medium">Total Entradas:</span>
                <span class="text-blue-900 font-bold text-lg">{{ $tool->total_entries }}</span>
            </div>

            <div class="flex justify-between items-center p-3 bg-green-50 rounded">
                <span class="text-green-800 font-medium">Disponibles:</span>
                <span class="text-green-900 font-bold text-lg">{{ $tool->available_qty }}</span>
            </div>

            <div class="flex justify-between items-center p-3 bg-orange-50 rounded">
                <span class="text-orange-800 font-medium">Dañadas:</span>
                <span class="text-orange-900 font-bold text-lg">{{ $tool->damaged_qty }}</span>
            </div>

            <div class="flex justify-between items-center p-3 bg-red-50 rounded">
                <span class="text-red-800 font-medium">Perdidas:</span>
                <span class="text-red-900 font-bold text-lg">{{ $tool->lost_qty }}</span>
            </div>
        </div>
    </div>

    <!-- Estado actual -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Estado Actual</h3>
        
        <div class="space-y-4">
            @php
                $status = 'operational';
                if ($tool->lost_qty > 0) $status = 'lost';
                elseif ($tool->damaged_qty > 0) $status = 'damaged';
                elseif ($tool->available_qty == 0) $status = 'empty';
                
                $statusClasses = [
                    'operational' => 'bg-green-100 text-green-800 border-green-200',
                    'damaged' => 'bg-orange-100 text-orange-800 border-orange-200',
                    'lost' => 'bg-red-100 text-red-800 border-red-200',
                    'empty' => 'bg-gray-100 text-gray-800 border-gray-200',
                ];
                $statusLabels = [
                    'operational' => 'Operacional',
                    'damaged' => 'Dañada',
                    'lost' => 'Perdida',
                    'empty' => 'Vacía',
                ];
            @endphp
            
            <div class="p-4 border rounded-lg {{ $statusClasses[$status] }}">
                <div class="flex items-center">
                    <i data-lucide="{{ $status === 'operational' ? 'check-circle' : ($status === 'damaged' ? 'alert-triangle' : ($status === 'lost' ? 'x-circle' : 'minus-circle')) }}" class="w-6 h-6 mr-3"></i>
                    <div>
                        <p class="font-semibold">{{ $statusLabels[$status] }}</p>
                        <p class="text-sm opacity-75">
                            @if($status === 'operational')
                                Todas las herramientas están en buen estado
                            @elseif($status === 'damaged')
                                Hay herramientas dañadas que requieren atención
                            @elseif($status === 'lost')
                                Se han reportado pérdidas de herramientas
                            @else
                                No hay herramientas disponibles
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Historial de daños y pérdidas -->
<div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b bg-gray-50">
        <h3 class="text-lg font-medium text-gray-900">Historial de Daños y Pérdidas</h3>
    </div>
    
    @if($tool->entries->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-emerald-800 border-b bg-gray-50">
                    <th class="py-3 px-4">Fecha</th>
                    <th class="py-3 px-4">Tipo</th>
                    <th class="py-3 px-4">Cantidad Dañada</th>
                    <th class="py-3 px-4">Cantidad Perdida</th>
                    <th class="py-3 px-4">Notas</th>
                    <th class="py-3 px-4">Evidencia</th>
                    <th class="py-3 px-4">Registrado por</th>
                    <th class="py-3 px-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tool->entries as $entry)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4">
                        <div class="font-medium text-gray-900">{{ $entry->entry_date->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $entry->created_at->format('H:i') }}</div>
                    </td>
                    <td class="py-3 px-4">
                        @if($entry->damaged_qty > 0)
                            <span class="px-2 py-1 text-xs rounded bg-orange-100 text-orange-700">
                                Daño
                            </span>
                        @elseif($entry->lost_qty > 0)
                            <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">
                                Pérdida
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">
                                Entrada
                            </span>
                        @endif
                    </td>
                    <td class="py-3 px-4">
                        <div class="font-semibold {{ $entry->damaged_qty > 0 ? 'text-orange-600' : 'text-gray-500' }}">
                            {{ $entry->damaged_qty }}
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="font-semibold {{ $entry->lost_qty > 0 ? 'text-red-600' : 'text-gray-500' }}">
                            {{ $entry->lost_qty }}
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="text-sm text-gray-600 max-w-xs truncate" title="{{ $entry->damage_notes ?? $entry->loss_notes ?? $entry->notes ?? '' }}">
                            {{ $entry->damage_notes ?? $entry->loss_notes ?? $entry->notes ?? '—' }}
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        @if($entry->damage_photo)
                            <a href="{{ asset('storage/' . $entry->damage_photo) }}" target="_blank" title="Ver foto completa">
                                <img src="{{ asset('storage/' . $entry->damage_photo) }}" alt="Foto evidencia" class="h-10 w-10 object-cover rounded border border-gray-200">
                            </a>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="py-3 px-4">
                        <div class="text-sm text-gray-600">{{ $entry->createdBy->name }}</div>
                    </td>
                    <td class="py-3 px-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('admin.tool-damage.show', $tool->id) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 border border-blue-200 rounded hover:bg-blue-50 text-blue-600"
                               title="Ver detalles">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ route('admin.tool-damage.edit', $entry->id) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 border border-emerald-200 rounded hover:bg-emerald-50 text-emerald-700"
                               title="Editar">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="px-6 py-8 text-center text-gray-500">
        <i data-lucide="info" class="w-12 h-12 mx-auto mb-2 text-gray-400"></i>
        <p>No hay registros de daños o pérdidas para esta herramienta</p>
    </div>
    @endif
</div>

<!-- Acciones -->
<div class="flex justify-end gap-4 pt-6 border-t mt-6">
    <a href="{{ route('admin.tool-damage.index') }}" 
       class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-200 rounded transition-colors">
        Volver al Listado
    </a>
    <a href="{{ route('admin.tool-damage.create', ['tool_id' => $tool->id]) }}" 
       class="px-6 py-2 bg-orange-100 hover:bg-orange-200 text-orange-700 border border-orange-200 rounded transition-colors">
        <i data-lucide="alert-triangle" class="w-4 h-4 inline mr-2"></i>
        Registrar Daño/Pérdida
    </a>
</div>
@endsection
