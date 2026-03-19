@extends('admin.layout')

@section('header')
<h2 class="font-bold text-gray-800">Dashboard Administrador</h2>
<p class="text-gray-600 mt-1">Panel de control general del sistema AGROSAC</p>
@endsection

@section('content')
<style>
    /* Modern Card Styles from Accounting Dashboard */
    .kpi-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.85) 100%);
        border: 2px solid transparent;
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: 18px;
        padding: 2px;
        background: linear-gradient(135deg, var(--gradient-from), var(--gradient-to));
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        opacity: 0;
        transition: opacity 0.4s ease;
    }
    
    .kpi-card:hover::before {
        opacity: 1;
    }
    
    .kpi-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.15);
    }
    
    .kpi-card.workers {
        --gradient-from: #10b981;
        --gradient-to: #059669;
        background: linear-gradient(135deg, #f0fdf9 0%, #ecfdf5 100%);
    }
    
    .kpi-card.tasks {
        --gradient-from: #f59e0b;
        --gradient-to: #d97706;
        background: linear-gradient(135deg, #fffef8 0%, #fffbf0 100%);
    }
    
    .kpi-card.crops {
        --gradient-from: #22c55e;
        --gradient-to: #16a34a;
        background: linear-gradient(135deg, #f7fef9 0%, #f0fdf4 100%);
    }
    
    .kpi-card.balance-positive {
        --gradient-from: #10b981;
        --gradient-to: #059669;
        background: linear-gradient(135deg, #fdfffe 0%, #f0fdf9 100%);
    }
    
    .kpi-card.balance-negative {
        --gradient-from: #ef4444;
        --gradient-to: #dc2626;
        background: linear-gradient(135deg, #fffafa 0%, #fef2f2 100%);
    }
    
    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    .kpi-icon::before {
        content: '';
        position: absolute;
        inset: -4px;
        border-radius: 20px;
        padding: 4px;
        background: linear-gradient(135deg, var(--icon-from), var(--icon-to));
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        opacity: 0.5;
    }
    
    .kpi-icon.workers-icon {
        --icon-from: #10b981;
        --icon-to: #059669;
        background: linear-gradient(135deg, #10b981, #059669);
    }
    
    .kpi-icon.tasks-icon {
        --icon-from: #f59e0b;
        --icon-to: #d97706;
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }
    
    .kpi-icon.crops-icon {
        --icon-from: #22c55e;
        --icon-to: #16a34a;
        background: linear-gradient(135deg, #22c55e, #16a34a);
    }
    
    .kpi-icon.balance-icon-positive {
        --icon-from: #10b981;
        --icon-to: #059669;
        background: linear-gradient(135deg, #10b981, #059669);
    }
    
    .kpi-icon.balance-icon-negative {
        --icon-from: #ef4444;
        --icon-to: #dc2626;
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }
    
    .pulse-dot {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: .5;
        }
    }
</style>
<div class="space-y-6">
    {{-- Sección de KPIs con diseño moderno --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- KPI: Trabajadores Activos --}}
        <div class="kpi-card workers p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="kpi-icon workers-icon">
                    <i data-lucide="users" class="w-6 h-6 text-white"></i>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full pulse-dot"></div>
                    <span class="text-xs font-medium text-emerald-700">Activo</span>
                </div>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-600 mb-1">Trabajadores Activos</p>
                <p id="kpi-active-users" class="text-2xl font-black text-emerald-700">{{ $activeUsers }}</p>
                <p id="kpi-total-users" class="text-xs text-gray-500 mt-1">Total: {{ $totalUsers }}</p>
            </div>
        </div>

        {{-- KPI: Tareas Pendientes --}}
        <div class="kpi-card tasks p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="kpi-icon tasks-icon">
                    <i data-lucide="clipboard-check" class="w-6 h-6 text-white"></i>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 bg-amber-500 rounded-full pulse-dot"></div>
                    <span class="text-xs font-medium text-amber-700">Activo</span>
                </div>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-600 mb-1">Tareas Pendientes</p>
                <p id="kpi-pending-tasks" class="text-2xl font-black text-amber-700">{{ $pendingTasks }}</p>
                <p id="kpi-completed-tasks" class="text-xs text-gray-500 mt-1">Completadas: {{ $completedTasks }}</p>
            </div>
        </div>

        {{-- KPI: Cultivos Activos --}}
        <div class="kpi-card crops p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="kpi-icon crops-icon">
                    <i data-lucide="sprout" class="w-6 h-6 text-white"></i>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 bg-green-500 rounded-full pulse-dot"></div>
                    <span class="text-xs font-medium text-green-700">Activo</span>
                </div>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-600 mb-1">Cultivos Activos</p>
                <p id="kpi-active-crops" class="text-2xl font-black text-green-700">{{ $activeCrops }}</p>
                <p id="kpi-total-crops" class="text-xs text-gray-500 mt-1">Total: {{ $totalCrops }}</p>
            </div>
        </div>

        {{-- KPI: Balance del Mes --}}
        <div class="kpi-card {{ $currentMonthBalance >= 0 ? 'balance-positive' : 'balance-negative' }} p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="kpi-icon {{ $currentMonthBalance >= 0 ? 'balance-icon-positive' : 'balance-icon-negative' }}">
                    <i data-lucide="{{ $currentMonthBalance >= 0 ? 'dollar-sign' : 'trending-down' }}" class="w-6 h-6 text-white"></i>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 {{ $currentMonthBalance >= 0 ? 'bg-emerald-500' : 'bg-red-500' }} rounded-full pulse-dot"></div>
                    <span class="text-xs font-medium {{ $currentMonthBalance >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                        {{ $currentMonthBalance >= 0 ? 'Ganancia' : 'Pérdida' }}
                    </span>
                </div>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-600 mb-1">Balance del Mes</p>
                <p id="kpi-month-balance" class="text-2xl font-black {{ $currentMonthBalance >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                    {{ $currentMonthBalance >= 0 ? '+' : '' }}$ {{ number_format($currentMonthBalance, 0) }}
                </p>
                <p class="text-xs text-gray-500 mt-1">{{ now()->format('F Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Grid Principal: Mapa + Insumos Bajos --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {{-- Mapa de Ubicación (60%) --}}
        <div class="lg:col-span-3 bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 rounded-lg">
                        <i data-lucide="map-pin" class="w-5 h-5 text-emerald-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Ubicación Finca</h3>
                </div>
                <a href="{{ route('admin.farm-settings.edit') }}" class="text-sm text-emerald-600 hover:text-emerald-700 inline-flex items-center gap-1">
                    <i data-lucide="settings" class="w-4 h-4"></i>
                </a>
            </div>
            @if($farmSettings->latitude && $farmSettings->longitude)
            <div class="mb-3">
                <p class="text-xs text-gray-600"><strong>Nombre:</strong> {{ $farmSettings->name }}</p>
                @if($farmSettings->address)
                    <p class="text-xs text-gray-600"><strong>Dirección:</strong> {{ $farmSettings->address }}</p>
                @endif
            </div>
            <div id="farmMap" style="width: 100%; height: 350px; border-radius: 8px; overflow: hidden;"></div>
            @else
            <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                <i data-lucide="map-pin" class="w-12 h-12 text-gray-400 mx-auto mb-2"></i>
                <p class="text-gray-600 text-sm font-medium mb-2">No configurado</p>
                <a href="{{ route('admin.farm-settings.edit') }}" class="inline-flex items-center gap-2 px-3 py-2 text-xs bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded-lg transition-colors">
                    <i data-lucide="settings" class="w-3 h-3"></i>
                    <span>Configurar</span>
                </a>
            </div>
            @endif
        </div>

        {{-- Insumos con Stock Bajo (40%) --}}
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Insumos con Stock Bajo</h3>
                </div>
                <a href="{{ route('admin.supplies.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700 inline-flex items-center gap-1">
                    <span>Ver todos</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
            @if($lowStockSupplies->count() > 0)
            <div class="space-y-2">
                @foreach($lowStockSupplies as $supply)
                <div class="flex items-center justify-between p-3 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800 text-sm">{{ $supply->name }}</p>
                        <p class="text-xs text-gray-600">
                            Stock: <span class="font-bold text-red-600">{{ number_format($supply->current_stock, 0) }}</span> {{ $supply->unit }}
                            <span class="text-gray-500">/ Mínimo: {{ number_format($supply->min_stock, 0) }}</span>
                        </p>
                    </div>
                    <div class="text-right ml-3">
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-red-200 text-red-800 rounded-full">
                            <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                            Bajo
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8 bg-emerald-50 rounded-lg border border-emerald-200">
                <i data-lucide="check-circle" class="w-12 h-12 text-emerald-600 mx-auto mb-2"></i>
                <p class="text-emerald-700 font-medium">¡Excelente! Todos los insumos tienen stock adecuado</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Grid Inferior: Gráfico Financiero + Cultivos Activos --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Gráfico de Gastos vs Ingresos --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i data-lucide="pie-chart" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Finanzas del Mes</h3>
                        <p class="text-xs text-gray-500">{{ now()->format('F Y') }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.ledger.dashboard') }}" class="text-sm text-emerald-600 hover:text-emerald-700 inline-flex items-center gap-1">
                    <span>Ver detalles</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
            <div class="flex justify-center items-center" style="height: 280px;">
                @if($currentMonthIncome > 0 || $currentMonthExpenses > 0)
                <canvas id="financialChart"></canvas>
                @else
                <div class="text-center">
                    <i data-lucide="bar-chart-2" class="w-16 h-16 text-gray-300 mx-auto mb-3"></i>
                    <p class="text-gray-500">No hay datos financieros este mes</p>
                </div>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-gray-200">
                <div class="text-center">
                    <p class="text-xs text-gray-600 mb-1">Ingresos</p>
                    <p class="text-lg font-bold text-emerald-600">$ {{ number_format($currentMonthIncome, 0) }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-600 mb-1">Gastos</p>
                    <p class="text-lg font-bold text-amber-600">$ {{ number_format($currentMonthExpenses, 0) }}</p>
                </div>
            </div>
        </div>

        {{-- Lista de Cultivos (Scrollable) --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i data-lucide="sprout" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Cultivos Activos</h3>
                </div>
                <a href="{{ route('admin.crops.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700 inline-flex items-center gap-1">
                    <span>Ver todos</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
            @if($activeCropsWithPlot->count() > 0)
            <div class="space-y-2 max-h-80 overflow-y-auto">
                @foreach($activeCropsWithPlot as $crop)
                <div class="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800 text-sm">{{ $crop->name }}</p>
                        <p class="text-xs text-gray-600">
                            @if($crop->variety)
                                <span class="text-green-700">{{ $crop->variety }}</span> •
                            @endif
                            Lote: <span class="font-medium">{{ $crop->plot->name ?? 'N/A' }}</span>
                        </p>
                    </div>
                    <div class="ml-3">
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-200 text-green-800 rounded-full">
                            <i data-lucide="leaf" class="w-3 h-3 mr-1"></i>
                            Activo
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12 bg-gray-50 rounded-lg border border-gray-200">
                <i data-lucide="sprout" class="w-12 h-12 text-gray-400 mx-auto mb-2"></i>
                <p class="text-gray-600 text-sm">No hay cultivos activos</p>
                <a href="{{ route('admin.crops.create') }}" class="inline-flex items-center gap-2 px-3 py-2 mt-3 text-xs bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded-lg transition-colors">
                    <i data-lucide="plus" class="w-3 h-3"></i>
                    <span>Registrar Cultivo</span>
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Mapbox --}}
@if(config('services.mapbox.token') && $farmSettings->latitude && $farmSettings->longitude)
<link href="https://api.mapbox.com/mapbox-gl-js/v3.0.1/mapbox-gl.css" rel="stylesheet">
<script src="https://api.mapbox.com/mapbox-gl-js/v3.0.1/mapbox-gl.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mapboxToken = '{{ config('services.mapbox.token') }}';
        const farmMapContainer = document.getElementById('farmMap');
        
        if (!mapboxToken) {
            console.error('Mapbox token no configurado');
            if (farmMapContainer) {
                farmMapContainer.innerHTML = '<div class="p-4 bg-yellow-50 border border-yellow-200 rounded"><p class="text-yellow-800 text-xs">Error: No se ha configurado el token de Mapbox.</p></div>';
            }
            return;
        }

        if (!farmMapContainer) {
            console.error('Contenedor del mapa no encontrado');
            return;
        }

        mapboxgl.accessToken = mapboxToken;
        
        try {
            const farmLocation = [{{ $farmSettings->longitude }}, {{ $farmSettings->latitude }}];
            const farmBoundary = @json($farmSettings->boundary ?? null);
            
            const map = new mapboxgl.Map({
                container: 'farmMap',
                style: 'mapbox://styles/mapbox/streets-v12',
                center: farmLocation,
                zoom: 15
            });

            map.addControl(new mapboxgl.NavigationControl());

            const marker = new mapboxgl.Marker()
                .setLngLat(farmLocation)
                .setPopup(
                    new mapboxgl.Popup({ offset: 25 })
                        .setHTML(`
                            <div style="padding: 8px;">
                                <h3 style="margin: 0 0 5px 0; font-weight: bold; font-size: 14px;">{{ $farmSettings->name }}</h3>
                                @if($farmSettings->address)
                                    <p style="margin: 0; color: #666; font-size: 11px;">{{ $farmSettings->address }}</p>
                                @endif
                            </div>
                        `)
                )
                .addTo(map);

            marker.togglePopup();

            map.on('load', () => {
                if (farmBoundary && farmBoundary.type === 'Polygon') {
                    map.addSource('farm-boundary', {
                        'type': 'geojson',
                        'data': {
                            'type': 'Feature',
                            'geometry': farmBoundary
                        }
                    });

                    map.addLayer({
                        'id': 'farm-boundary-fill',
                        'type': 'fill',
                        'source': 'farm-boundary',
                        'layout': {},
                        'paint': {
                            'fill-color': '#10b981',
                            'fill-opacity': 0.2
                        }
                    });

                    map.addLayer({
                        'id': 'farm-boundary-stroke',
                        'type': 'line',
                        'source': 'farm-boundary',
                        'layout': {},
                        'paint': {
                            'line-color': '#10b981',
                            'line-width': 3,
                            'line-dasharray': [2, 2]
                        }
                    });

                    const coordinates = farmBoundary.coordinates[0];
                    const bounds = coordinates.reduce((bounds, coord) => {
                        return bounds.extend(coord);
                    }, new mapboxgl.LngLatBounds(coordinates[0], coordinates[0]));
                    
                    map.fitBounds(bounds, {
                        padding: 50
                    });
                }
            });

        } catch (error) {
            console.error('Error inicializando mapa:', error);
            if (farmMapContainer) {
                farmMapContainer.innerHTML = '<div class="p-4 bg-red-50 border border-red-200 rounded"><p class="text-red-800 text-xs">Error al cargar el mapa.</p></div>';
            }
        }
    });
</script>
@endif

{{-- Chart.js para el gráfico financiero --}}
@if($currentMonthIncome > 0 || $currentMonthExpenses > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('financialChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Ingresos', 'Gastos'],
                    datasets: [{
                        data: [{{ $currentMonthIncome }}, {{ $currentMonthExpenses }}],
                        backgroundColor: ['#10b981', '#f59e0b'],
                        borderColor: ['#059669', '#d97706'],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += '$ ' + context.parsed.toFixed(0);
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endif

{{-- Inicializar iconos Lucide --}}
<script>
    if (window.lucide) {
        window.lucide.createIcons();
    }

    // Polling para actualización en tiempo real
    function refreshDashboardStats() {
        fetch('{{ route('admin.data') }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const stats = data.stats;
                    
                    // Actualizar KPIs
                    if (document.getElementById('kpi-active-users')) 
                        document.getElementById('kpi-active-users').textContent = stats.activeUsers;
                    
                    if (document.getElementById('kpi-pending-tasks')) 
                        document.getElementById('kpi-pending-tasks').textContent = stats.pendingTasks;
                    
                    if (document.getElementById('kpi-completed-tasks')) 
                        document.getElementById('kpi-completed-tasks').textContent = 'Completadas: ' + stats.completedTasks;
                    
                    if (document.getElementById('kpi-active-crops')) 
                        document.getElementById('kpi-active-crops').textContent = stats.activeCrops;
                    
                    if (document.getElementById('kpi-month-balance')) {
                        const balance = stats.currentMonthBalance;
                        const el = document.getElementById('kpi-month-balance');
                        el.textContent = (balance >= 0 ? '+' : '') + '$ ' + balance.toLocaleString('en-US', {minimumFractionDigits: 0});
                        el.className = 'text-2xl font-black ' + (balance >= 0 ? 'text-emerald-700' : 'text-red-700');
                    }
                }
            })
            .catch(error => console.error('Error al actualizar stats:', error));
    }

    setInterval(refreshDashboardStats, 5000);
</script>
@endpush
