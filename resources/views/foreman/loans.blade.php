@extends('foreman.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Préstamos</h2>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Loans List -->
    <div class="bg-white border rounded">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-emerald-700">Lista de Préstamos</h3>
        </div>
        
        <div class="p-6">
            @if($loans->count() > 0)
                <div class="space-y-4">
                    @foreach($loans as $loan)
                        <div class="border border-emerald-200 rounded p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h4 class="font-medium text-emerald-800">{{ $loan->tool->name ?? 'Herramienta no disponible' }}</h4>
                                    <div class="text-sm text-emerald-600 mt-1">
                                        <span class="inline-flex items-center">
                                            <i data-lucide="user" class="w-4 h-4 mr-1"></i>
                                            {{ $loan->borrower->name ?? 'Sin asignar' }}
                                        </span>
                                        <span class="mx-2">•</span>
                                        <span class="inline-flex items-center">
                                            <i data-lucide="calendar" class="w-4 h-4 mr-1"></i>
                                            Prestado: {{ $loan->created_at->format('d/m/Y') }}
                                        </span>
                                        @if($loan->expected_return_date)
                                            <span class="mx-2">•</span>
                                            <span class="inline-flex items-center">
                                                <i data-lucide="calendar-days" class="w-4 h-4 mr-1"></i>
                                                Devolución: {{ \Carbon\Carbon::parse($loan->expected_return_date)->format('d/m/Y') }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($loan->notes)
                                        <div class="text-xs text-emerald-500 mt-2">
                                            {{ $loan->notes }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="px-3 py-1 text-sm rounded-full 
                                        {{ $loan->status === 'returned' ? 'bg-green-100 text-green-800' : 
                                           ($loan->status === 'active' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($loan->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-6">
                    {{ $loans->links() }}
                </div>
            @else
                <div class="text-center py-8">
                    <i data-lucide="arrow-left-right" class="w-12 h-12 text-emerald-300 mx-auto mb-4"></i>
                    <p class="text-emerald-600">No hay préstamos registrados</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
