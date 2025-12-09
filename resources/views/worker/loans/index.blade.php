@extends('worker.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Mis Préstamos de Herramientas</h2>
    <a href="{{ route('worker.loans.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Solicitar Préstamo</span>
    </a>
</div>
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

    <!-- Filtros -->
    <div class="mb-6 flex gap-4 items-end">
        <div class="flex-1">
            <label class="block text-sm mb-1 text-emerald-800">Filtrar por estado</label>
            <select class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-emerald-500" onchange="filterByStatus(this.value)">
                <option value="all">Todos los estados</option>
                <option value="pending">Pendiente</option>
                <option value="approved">Aprobado</option>
                <option value="rejected">Rechazado</option>
                <option value="out">Prestado</option>
                <option value="returned">Devuelto</option>
            </select>
        </div>
    </div>

    <!-- Tabla de préstamos -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-emerald-800 border-b">
                    <th class="py-3 pr-4">Herramienta</th>
                    <th class="py-3 pr-4">Cantidad</th>
                    <th class="py-3 pr-4">Estado</th>
                    <th class="py-3 pr-4">Fecha Solicitud</th>
                    <th class="py-3 pr-4">Fecha Vencimiento</th>
                    <th class="py-3 pr-4">Notas</th>
                    <th class="py-3 pr-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($loans as $loan)
                <tr class="border-b hover:bg-gray-50" data-status="{{ $loan->status }}">
                    <td class="py-3 pr-4">
                        <div class="flex items-center gap-3">
                            @if($loan->tool->photo)
                                <img src="{{ asset('storage/' . $loan->tool->photo) }}" alt="Foto" class="h-10 w-10 object-cover rounded border border-gray-200">
                            @else
                                <div class="h-10 w-10 rounded border border-gray-200 bg-gray-50 flex items-center justify-center text-xs text-gray-400">Sin foto</div>
                            @endif
                            <div>
                                <div class="font-medium text-gray-900">{{ $loan->tool->name }}</div>
                                <div class="text-xs text-gray-500">{{ $loan->tool->category }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="font-medium text-gray-900">{{ $loan->quantity }}</span>
                    </td>
                    <td class="py-3 pr-4">
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
                        <span class="px-2 py-1 text-xs rounded {{ $statusClasses[$loan->status] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ $statusLabels[$loan->status] ?? $loan->status }}
                        </span>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-600">{{ $loan->created_at->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $loan->created_at->format('H:i') }}</div>
                    </td>
                    <td class="py-3 pr-4">
                        @if($loan->due_at)
                            <div class="text-sm text-gray-600">{{ $loan->due_at->format('d/m/Y') }}</div>
                            @if($loan->due_at->isPast() && $loan->status === 'out')
                                <div class="text-xs text-red-500">Vencido</div>
                            @endif
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-600 max-w-xs truncate" title="{{ $loan->request_notes }}">
                            {{ $loan->request_notes ?? '—' }}
                        </div>
                    </td>
                    <td class="py-3 pr-4 text-right">
                        <div class="flex items-center gap-1 justify-end">
                            <!-- Ver detalles -->
                            <a href="{{ route('worker.loans.show', $loan) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 border border-blue-200 rounded hover:bg-blue-50 text-blue-600" 
                               title="Ver detalles">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            
                            <!-- Devolver (solo si está prestado) -->
                            @if($loan->status === 'out')
                                <a href="{{ route('worker.loans.return-form', $loan) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 border border-green-200 rounded hover:bg-green-50 text-green-600" 
                                   title="Devolver">
                                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-6 text-center text-emerald-800/70">No tienes préstamos registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $loans->links() }}</div>
</div>

<script>
function filterByStatus(status) {
    const rows = document.querySelectorAll('tbody tr[data-status]');
    rows.forEach(row => {
        if (status === 'all' || row.getAttribute('data-status') === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
@endsection
