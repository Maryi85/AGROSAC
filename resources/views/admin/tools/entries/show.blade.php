@php
    $layout = route_prefix() === 'foreman.' ? 'foreman.layout' : 'admin.layout';
@endphp
@extends($layout)

@section('title', 'Detalles de Entrada de Herramienta')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Detalles de Entrada de Herramienta</h2>
    <div class="flex items-center gap-2">
        <a href="{{ route(route_prefix() . 'tool-entries.edit', $entry) }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded transition-colors">
            <i data-lucide="pencil" class="w-4 h-4"></i>
            <span>Editar</span>
        </a>
        <a href="{{ route(route_prefix() . 'tool-entries.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-emerald-300 rounded text-emerald-700 hover:bg-emerald-100">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Volver</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Información de la entrada -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Información de la Entrada</h3>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Herramienta</label>
                <p class="mt-1 text-sm text-gray-900">{{ $entry->tool->name }}</p>
                <p class="text-xs text-gray-500">{{ $entry->tool->category }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cantidad</label>
                    <p class="mt-1 text-sm text-gray-900 font-semibold">{{ number_format($entry->quantity) }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Tipo</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1 {{ $entry->type === 'purchase' ? 'bg-blue-100 text-blue-800' : ($entry->type === 'donation' ? 'bg-green-100 text-green-800' : ($entry->type === 'transfer' ? 'bg-purple-100 text-purple-800' : 'bg-orange-100 text-orange-800')) }}">
                        @switch($entry->type)
                            @case('purchase') Compra @break
                            @case('donation') Donación @break
                            @case('transfer') Transferencia @break
                            @case('repair') Reparación @break
                        @endswitch
                    </span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Fecha de Entrada</label>
                <p class="mt-1 text-sm text-gray-900">{{ $entry->entry_date->format('d/m/Y') }}</p>
            </div>

            @if($entry->supplier)
            <div>
                <label class="block text-sm font-medium text-gray-700">Proveedor</label>
                <p class="mt-1 text-sm text-gray-900">{{ $entry->supplier }}</p>
            </div>
            @endif

            @if($entry->invoice_number)
            <div>
                <label class="block text-sm font-medium text-gray-700">Número de Factura</label>
                <p class="mt-1 text-sm text-gray-900">{{ $entry->invoice_number }}</p>
            </div>
            @endif

            @if($entry->notes)
            <div>
                <label class="block text-sm font-medium text-gray-700">Notas</label>
                <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $entry->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Información financiera -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Información Financiera</h3>
        
        <div class="space-y-4">
            @if($entry->unit_cost)
            <div>
                <label class="block text-sm font-medium text-gray-700">Costo Unitario</label>
                <p class="mt-1 text-sm text-gray-900 font-semibold">${{ number_format($entry->unit_cost, 2) }}</p>
            </div>
            @endif

            @if($entry->total_cost)
            <div>
                <label class="block text-sm font-medium text-gray-700">Costo Total</label>
                <p class="mt-1 text-2xl font-bold text-emerald-600">${{ number_format($entry->total_cost, 2) }}</p>
            </div>
            @endif

            @if(!$entry->unit_cost && !$entry->total_cost)
            <div class="text-center py-8">
                <i data-lucide="dollar-sign" class="w-12 h-12 text-gray-400 mx-auto mb-2"></i>
                <p class="text-sm text-gray-500">No hay información de costos</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Información del registro -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 lg:col-span-2">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Información del Registro</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Registrado por</label>
                <p class="mt-1 text-sm text-gray-900">{{ $entry->createdBy->name }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Fecha de Registro</label>
                <p class="mt-1 text-sm text-gray-900">{{ $entry->created_at->format('d/m/Y H:i') }}</p>
            </div>

            @if($entry->updated_at != $entry->created_at)
            <div>
                <label class="block text-sm font-medium text-gray-700">Última Actualización</label>
                <p class="mt-1 text-sm text-gray-900">{{ $entry->updated_at->format('d/m/Y H:i') }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Acciones -->
<div class="flex justify-end gap-4 pt-6 border-t">
    <a href="{{ route(route_prefix() . 'tool-entries.index') }}" 
       class="px-6 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition-colors">
        Volver al Listado
    </a>
    <a href="{{ route(route_prefix() . 'tool-entries.edit', $entry) }}" 
       class="px-6 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded transition-colors">
        <i data-lucide="pencil" class="w-4 h-4 inline mr-2"></i>
        Editar Entrada
    </a>
</div>
@endsection
