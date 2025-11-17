@extends('worker.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Detalles del Préstamo</h2>
    <a href="{{ route('worker.loans.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-emerald-300 rounded text-emerald-700 hover:bg-emerald-100">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Volver</span>
    </a>
</div>
@endsection

@section('content')
<div class="bg-white border rounded p-6">
    <!-- Información del préstamo -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Herramienta -->
        <div class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900">Información de la Herramienta</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <p class="text-sm text-gray-900">{{ $loan->tool->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Categoría</label>
                    <p class="text-sm text-gray-900">{{ $loan->tool->category }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cantidad Solicitada</label>
                    <p class="text-sm text-gray-900 font-semibold">{{ $loan->quantity }}</p>
                </div>
            </div>
        </div>

        <!-- Estado y fechas -->
        <div class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900">Estado y Fechas</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                    @php
                        $statusClasses = [
                            'pending' => 'bg-yellow-100 text-yellow-700',
                            'approved' => 'bg-blue-100 text-blue-700',
                            'rejected' => 'bg-red-100 text-red-700',
                            'out' => 'bg-green-100 text-green-700',
                            'returned_by_worker' => 'bg-orange-100 text-orange-700',
                            'returned' => 'bg-gray-100 text-gray-700',
                            'lost' => 'bg-red-100 text-red-700',
                            'damaged' => 'bg-orange-100 text-orange-700',
                        ];
                        $statusLabels = [
                            'pending' => 'Pendiente',
                            'approved' => 'Aprobado',
                            'rejected' => 'Rechazado',
                            'out' => 'Prestado',
                            'returned_by_worker' => 'Devuelto (Pendiente)',
                            'returned' => 'Devuelto y Confirmado',
                            'lost' => 'Perdido',
                            'damaged' => 'Dañado',
                        ];
                    @endphp
                    <span class="inline-block px-3 py-1 text-sm rounded {{ $statusClasses[$loan->status] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ $statusLabels[$loan->status] ?? $loan->status }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de Solicitud</label>
                    <p class="text-sm text-gray-900">{{ $loan->created_at->format('d/m/Y H:i') }}</p>
                </div>
                @if($loan->due_at)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de Vencimiento</label>
                    <p class="text-sm text-gray-900 {{ $loan->due_at->isPast() && $loan->status === 'out' ? 'text-red-600 font-semibold' : '' }}">
                        {{ $loan->due_at->format('d/m/Y H:i') }}
                        @if($loan->due_at->isPast() && $loan->status === 'out')
                            <span class="text-red-500">(Vencido)</span>
                        @endif
                    </p>
                </div>
                @endif
                @if($loan->out_at)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de Préstamo</label>
                    <p class="text-sm text-gray-900">{{ $loan->out_at->format('d/m/Y H:i') }}</p>
                </div>
                @endif
                @if($loan->returned_at)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de Devolución</label>
                    <p class="text-sm text-gray-900">{{ $loan->returned_at->format('d/m/Y H:i') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Notas y observaciones -->
    <div class="space-y-6">
        @if($loan->request_notes)
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-3">Notas de la Solicitud</h3>
            <div class="p-4 bg-gray-50 rounded border">
                <p class="text-sm text-gray-700">{{ $loan->request_notes }}</p>
            </div>
        </div>
        @endif

        @if($loan->admin_notes)
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-3">Observaciones del Administrador</h3>
            <div class="p-4 bg-blue-50 rounded border">
                <p class="text-sm text-gray-700">{{ $loan->admin_notes }}</p>
                @if($loan->approvedBy)
                    <p class="text-xs text-gray-500 mt-2">
                        Por: {{ $loan->approvedBy->name }} - {{ $loan->approved_at->format('d/m/Y H:i') }}
                    </p>
                @endif
            </div>
        </div>
        @endif

        @if($loan->condition_return)
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-3">Condición al Devolver</h3>
            <div class="p-4 bg-gray-50 rounded border">
                @php
                    $conditionLabels = [
                        'good' => 'Buen estado',
                        'damaged' => 'Dañado',
                        'lost' => 'Perdido',
                    ];
                @endphp
                <p class="text-sm text-gray-700">
                    {{ $conditionLabels[$loan->condition_return] ?? $loan->condition_return }}
                </p>
                @if($loan->returnedBy)
                    <p class="text-xs text-gray-500 mt-2">
                        Devuelto por: {{ $loan->returnedBy->name }} - {{ $loan->returned_at->format('d/m/Y H:i') }}
                    </p>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Acciones -->
    <div class="flex justify-end gap-4 pt-6 border-t mt-6">
        <a href="{{ route('worker.loans.index') }}" 
           class="px-6 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition-colors">
            Volver a la Lista
        </a>
        
        @if($loan->status === 'out')
            <a href="{{ route('worker.loans.return-form', $loan) }}" 
               class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
                Devolver Herramienta
            </a>
        @endif
    </div>
</div>
@endsection
