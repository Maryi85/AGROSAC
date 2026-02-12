@extends('foreman.layout')

@section('title', 'Entradas de Herramientas')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Entradas de Herramientas</h2>
</div>
@endsection

@section('content')
<!-- Filtros -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex items-end justify-between gap-4 mb-4">
        <h3 class="text-lg font-semibold text-emerald-700">Filtrar Entradas</h3>
        <a href="{{ route('foreman.tool-entries.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded-lg font-medium transition-colors">
            <i data-lucide="plus" class="w-5 h-5"></i>
            <span>Nueva Entrada</span>
        </a>
    </div>
    
    <div class="mb-4">
        <x-search-bar placeholder="Buscar por herramienta..." />
    </div>
</div>

<!-- Tabla de entradas -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-emerald-800 border-b bg-gray-50">
                    <th class="py-3 px-4">Fecha</th>
                    <th class="py-3 px-4">Foto</th>
                    <th class="py-3 px-4">Herramienta</th>
                    <th class="py-3 px-4">Tipo</th>
                    <th class="py-3 px-4">Cantidad</th>
                    <th class="py-3 px-4">Costo Unitario</th>
                    <th class="py-3 px-4">Total</th>
                    <th class="py-3 px-4">Registrado por</th>
                    <th class="py-3 px-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($entries as $entry)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4">
                        <div class="font-medium text-gray-900">{{ $entry->entry_date->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $entry->created_at->format('H:i') }}</div>
                    </td>
                    <td class="py-3 px-4">
                        @if($entry->tool->photo)
                            <img src="{{ asset('storage/' . $entry->tool->photo) }}" alt="{{ $entry->tool->name }}" class="w-12 h-12 rounded-full object-cover border border-gray-200">
                        @else
                            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                <i data-lucide="image" class="w-5 h-5"></i>
                            </div>
                        @endif
                    </td>
                    <td class="py-3 px-4">
                        <div class="font-medium text-gray-900">{{ $entry->tool->name }}</div>
                        <div class="text-xs text-gray-500">{{ $entry->tool->category }}</div>
                    </td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-1 text-xs rounded {{ $entry->type === 'purchase' ? 'bg-blue-100 text-blue-700' : ($entry->type === 'donation' ? 'bg-green-100 text-green-700' : ($entry->type === 'transfer' ? 'bg-purple-100 text-purple-700' : 'bg-orange-100 text-orange-700')) }}">
                            {{ $types[$entry->type] }}
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <div class="font-medium text-gray-900">{{ number_format($entry->quantity) }}</div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="text-sm text-gray-600">
                            @if($entry->unit_cost)
                                ${{ number_format($entry->unit_cost, 2) }}
                            @else
                                —
                            @endif
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="font-semibold text-gray-900">
                            @if($entry->total_cost)
                                ${{ number_format($entry->total_cost, 2) }}
                            @else
                                —
                            @endif
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="text-sm text-gray-600">{{ $entry->createdBy->name }}</div>
                    </td>
                    <td class="py-3 px-4 text-right">
                        <div class="flex items-center gap-1 justify-end">
                            <!-- Ver detalles -->
                            <a href="{{ route('foreman.tool-entries.show', $entry) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 border border-blue-200 rounded hover:bg-blue-50 text-blue-600" 
                               title="Ver detalles">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            
                            <!-- Editar -->
                            <a href="{{ route('foreman.tool-entries.edit', $entry) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 border border-emerald-200 rounded hover:bg-emerald-50 text-emerald-600" 
                               title="Editar">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                            
                            <!-- Eliminar -->
                            <form method="POST" action="{{ route('foreman.tool-entries.destroy', $entry) }}" class="inline" 
                                  onsubmit="return confirm('¿Eliminar esta entrada? Esta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center justify-center w-8 h-8 border border-red-200 rounded hover:bg-red-50 text-red-600" 
                                        title="Eliminar">
                                    <i data-lucide="trash" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="py-6 text-center text-emerald-800/70">No hay entradas registradas</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    @if($entries->hasPages())
    <div class="px-6 py-4 border-t bg-gray-50">
        {{ $entries->links() }}
    </div>
    @endif
</div>
@endsection
