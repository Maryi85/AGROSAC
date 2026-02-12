@props(['placeholder' => 'Buscar...', 'action' => null, 'withForm' => true])

@if($withForm)
    <form method="GET" action="{{ $action }}" {{ $attributes->merge(['class' => 'relative w-full md:max-w-md']) }}>
        {{-- 
            Lógica de Preservación:
            Mantener otros parámetros de la URL excepto 'search' y 'page'.
        --}}
        @foreach(request()->query() as $key => $value)
            @if(!in_array($key, ['search', 'page']))
                @if(is_array($value))
                    @foreach($value as $subKey => $subValue)
                        <input type="hidden" name="{{ $key }}[{{ $subKey }}]" value="{{ $subValue }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endif
        @endforeach
@else
    <div {{ $attributes->merge(['class' => 'relative w-full']) }}>
@endif

    <div class="relative">
        {{-- Input de Búsqueda --}}
        <input type="text" 
               name="search" 
               value="{{ request('search') }}" 
               placeholder="{{ $placeholder }}" 
               class="w-full border border-emerald-200 rounded-lg py-2 pl-10 pr-10 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-gray-700 placeholder-gray-400"
        >
        
        {{-- Icono de Lupa (Izquierda) --}}
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>

        {{-- Botón de Limpiar 'X' (Derecha) - Solo aparece si hay búsqueda activa --}}
        @if(request('search'))
            <a href="{{ request()->url() . '?' . http_build_query(request()->except(['search', 'page'])) }}" 
               class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-500 cursor-pointer transition-colors"
               title="Limpiar búsqueda">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>
        @endif
    </div>

@if($withForm)
    </form>
@else
    </div>
@endif
