@extends('foreman.layout')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Gestión de Inventario</h2>
        <p class="text-sm text-gray-600 mt-1">Control de insumos y herramientas agrícolas</p>
    </div>
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <i data-lucide="calendar" class="w-4 h-4"></i>
        <span>{{ now()->format('d/m/Y') }}</span>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-8">
    <style>
        .inventory-card {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            border: 1px solid rgba(16, 185, 129, 0.1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }
        
        .inventory-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .section-header {
            background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
            border-bottom: 2px solid #10b981;
        }
        
        .item-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(16, 185, 129, 0.2);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .item-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px -3px rgba(16, 185, 129, 0.15);
            border-color: rgba(16, 185, 129, 0.3);
        }
        
        .item-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #10b981, #059669, #047857);
        }
        
        .status-badge {
            font-weight: 600;
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .status-available {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        
        .status-borrowed {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #92400e;
            border: 1px solid #fde68a;
        }
        
        .status-damaged {
            background: linear-gradient(135deg, #fef2f2, #fecaca);
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        .empty-state {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
        }
        
        .cost-display {
            background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 8px 12px;
        }
    </style>
    <!-- Supplies Section -->
    <div class="inventory-card rounded-xl">
        <div class="section-header p-6 rounded-t-xl">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-emerald-100 rounded-lg">
                    <i data-lucide="flask-round" class="w-5 h-5 text-emerald-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800">Insumos Agrícolas</h3>
            </div>
        </div>
        
        <div class="p-6">
            @if($supplies->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($supplies as $supply)
                        <div class="item-card rounded-xl p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-semibold text-gray-800">{{ $supply->name }}</h4>
                                <span class="text-sm font-medium text-emerald-600 bg-emerald-100 px-3 py-1 rounded-full">{{ $supply->unit }}</span>
                            </div>
                            <div class="space-y-3">
                                <div class="cost-display">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">💰 Costo unitario:</span>
                                        <span class="font-bold text-emerald-700">
                                            ${{ number_format($supply->unit_cost, 2) }}
                                        </span>
                                    </div>
                                </div>
                                @if($supply->description)
                                    <div class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">
                                        📝 {{ $supply->description }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state text-center py-12">
                    <i data-lucide="flask-round" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                    <p class="text-gray-500 font-medium text-lg">No hay insumos registrados</p>
                    <p class="text-sm text-gray-400 mt-2">Los insumos agrícolas aparecerán aquí</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Tools Section -->
    <div class="inventory-card rounded-xl">
        <div class="section-header p-6 rounded-t-xl">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-emerald-100 rounded-lg">
                    <i data-lucide="wrench" class="w-5 h-5 text-emerald-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800">Herramientas Agrícolas</h3>
            </div>
        </div>
        
        <div class="p-6">
            @if($tools->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($tools as $tool)
                        <div class="item-card rounded-xl p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-semibold text-gray-800">{{ $tool->name }}</h4>
                                <span class="status-badge 
                                    {{ $tool->status === 'available' ? 'status-available' : 
                                       ($tool->status === 'borrowed' ? 'status-borrowed' : 'status-damaged') }}">
                                    {{ ucfirst($tool->status) }}
                                </span>
                            </div>
                            <div class="space-y-3">
                                @if($tool->description)
                                    <div class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">
                                        📝 {{ $tool->description }}
                                    </div>
                                @endif
                                <div class="flex items-center justify-between bg-emerald-50 p-3 rounded-lg">
                                    <span class="text-sm text-gray-600">🔧 Estado:</span>
                                    <span class="font-semibold text-emerald-700">{{ ucfirst($tool->status) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state text-center py-12">
                    <i data-lucide="wrench" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                    <p class="text-gray-500 font-medium text-lg">No hay herramientas registradas</p>
                    <p class="text-sm text-gray-400 mt-2">Las herramientas agrícolas aparecerán aquí</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
