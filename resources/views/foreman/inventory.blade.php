@extends('foreman.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Inventario</h2>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Supplies Section -->
    <div class="bg-white border rounded">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-emerald-700">Insumos</h3>
        </div>
        
        <div class="p-6">
            @if($supplies->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($supplies as $supply)
                        <div class="border border-emerald-200 rounded p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium text-emerald-800">{{ $supply->name }}</h4>
                                <span class="text-sm text-emerald-600">{{ $supply->unit }}</span>
                            </div>
                            <div class="text-sm text-emerald-600">
                                <div class="flex items-center justify-between">
                                    <span>Costo unitario:</span>
                                    <span class="font-medium text-emerald-700">
                                        ${{ number_format($supply->unit_cost, 2) }}
                                    </span>
                                </div>
                                @if($supply->description)
                                    <div class="mt-2 text-xs text-emerald-500">
                                        {{ $supply->description }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i data-lucide="flask-round" class="w-12 h-12 text-emerald-300 mx-auto mb-4"></i>
                    <p class="text-emerald-600">No hay insumos registrados</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Tools Section -->
    <div class="bg-white border rounded">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-emerald-700">Herramientas</h3>
        </div>
        
        <div class="p-6">
            @if($tools->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($tools as $tool)
                        <div class="border border-emerald-200 rounded p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium text-emerald-800">{{ $tool->name }}</h4>
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $tool->status === 'available' ? 'bg-green-100 text-green-800' : 
                                       ($tool->status === 'borrowed' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($tool->status) }}
                                </span>
                            </div>
                            <div class="text-sm text-emerald-600">
                                @if($tool->description)
                                    <div class="mb-2">{{ $tool->description }}</div>
                                @endif
                                <div class="flex items-center justify-between">
                                    <span>Estado:</span>
                                    <span class="font-medium">{{ ucfirst($tool->status) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i data-lucide="wrench" class="w-12 h-12 text-emerald-300 mx-auto mb-4"></i>
                    <p class="text-emerald-600">No hay herramientas registradas</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
