@extends('admin.layout')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Dashboard Administrador</h2>
        <p class="text-sm text-gray-600 mt-1">Panel de control general del sistema AGROSAC</p>
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
        .admin-card {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            border: 1px solid rgba(16, 185, 129, 0.1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }
        
        .admin-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .metric-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(16, 185, 129, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #10b981, #059669, #047857);
        }
        
        .metric-icon {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 4px 14px 0 rgba(16, 185, 129, 0.3);
        }
        
        .activity-card {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            border: 1px solid rgba(16, 185, 129, 0.15);
            position: relative;
        }
        
        .activity-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #10b981, #059669);
        }
        
        .status-badge {
            font-weight: 600;
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .status-completed {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        
        .status-pending {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #92400e;
            border: 1px solid #fde68a;
        }
        
        .status-inactive {
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
            color: #374151;
            border: 1px solid #e5e7eb;
        }
        
        .empty-state {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
        }
    </style>
    <!-- Welcome Section -->
    <div class="admin-card rounded-xl p-6">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl">
                <i data-lucide="shield-check" class="w-6 h-6 text-white"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Bienvenido, {{ auth()->user()->name ?? 'Admin' }}</h3>
                <p class="text-sm text-gray-600">Panel de control general del sistema AGROSAC</p>
            </div>
        </div>
    </div>

    <!-- Main Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Usuarios Activos -->
        <div class="metric-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Usuarios Activos</p>
                    <p class="text-3xl font-bold text-emerald-600 mb-2">{{ $activeUsers }}</p>
                    <p class="text-xs text-gray-500">👥 Total: {{ $totalUsers }}</p>
                </div>
                <div class="p-4 metric-icon rounded-2xl">
                    <i data-lucide="users" class="w-7 h-7 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Lotes Registrados -->
        <div class="metric-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Lotes Registrados</p>
                    <p class="text-3xl font-bold text-emerald-600 mb-2">{{ $totalPlots }}</p>
                    <p class="text-xs text-gray-500">🌱 Activos: {{ $activePlots }}</p>
                </div>
                <div class="p-4 metric-icon rounded-2xl">
                    <i data-lucide="grid-3x3" class="w-7 h-7 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Cultivos Activos -->
        <div class="metric-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Cultivos Activos</p>
                    <p class="text-3xl font-bold text-emerald-600 mb-2">{{ $activeCrops }}</p>
                    <p class="text-xs text-gray-500">🌾 Total: {{ $totalCrops }}</p>
                </div>
                <div class="p-4 metric-icon rounded-2xl">
                    <i data-lucide="sprout" class="w-7 h-7 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Tareas Pendientes -->
        <div class="metric-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Tareas Pendientes</p>
                    <p class="text-3xl font-bold text-emerald-600 mb-2">{{ $pendingTasks }}</p>
                    <p class="text-xs text-gray-500">✅ Completadas: {{ $completedTasks }}</p>
                </div>
                <div class="p-4 metric-icon rounded-2xl">
                    <i data-lucide="clipboard-check" class="w-7 h-7 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Préstamos Activos -->
        <div class="metric-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Préstamos Activos</p>
                    <p class="text-2xl font-bold text-emerald-600 mb-2">{{ $activeLoans }}</p>
                    <p class="text-xs text-gray-500">🔄 En curso</p>
                </div>
                <div class="p-3 metric-icon rounded-xl">
                    <i data-lucide="arrow-left-right" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Insumos Disponibles -->
        <div class="metric-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Insumos Disponibles</p>
                    <p class="text-2xl font-bold text-emerald-600 mb-2">{{ $availableSupplies }}</p>
                    <p class="text-xs text-gray-500">🧪 En stock</p>
                </div>
                <div class="p-3 metric-icon rounded-xl">
                    <i data-lucide="flask-round" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Balance Neto -->
        <div class="metric-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Balance Neto</p>
                    <p class="text-2xl font-bold {{ $netProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }} mb-2">
                        ${{ number_format($netProfit, 2) }}
                    </p>
                    <p class="text-xs text-gray-500">{{ $netProfit >= 0 ? '💰 Ganancia' : '📉 Pérdida' }}</p>
                </div>
                <div class="p-3 metric-icon rounded-xl">
                    <i data-lucide="banknote" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Tareas Recientes -->
        <div class="activity-card rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 rounded-lg">
                        <i data-lucide="clipboard-check" class="w-5 h-5 text-emerald-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Tareas Recientes</h3>
                </div>
            </div>
            @if($recentTasks->count() > 0)
                <div class="space-y-4">
                    @foreach($recentTasks as $task)
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-emerald-50 to-emerald-100 rounded-xl transition-all duration-200 hover:shadow-md">
                            <div>
                                <div class="text-sm font-semibold text-gray-800 mb-1">{{ $task->description }}</div>
                                <div class="text-xs text-gray-600">
                                    {{ $task->plot->name ?? 'Sin lote' }} • 
                                    {{ $task->assignee->name ?? 'Sin asignar' }}
                                </div>
                            </div>
                            <span class="status-badge 
                                {{ $task->status === 'completed' ? 'status-completed' : 
                                   ($task->status === 'pending' ? 'status-pending' : 'status-inactive') }}">
                                {{ ucfirst($task->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state text-center py-8">
                    <i data-lucide="clipboard-check" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                    <p class="text-gray-500 font-medium">No hay tareas recientes</p>
                    <p class="text-sm text-gray-400 mt-1">Las tareas aparecerán aquí cuando se creen</p>
                </div>
            @endif
        </div>

        <!-- Movimientos Recientes -->
        <div class="activity-card rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 rounded-lg">
                        <i data-lucide="banknote" class="w-5 h-5 text-emerald-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Movimientos Recientes</h3>
                </div>
            </div>
            @if($recentLedgerEntries->count() > 0)
                <div class="space-y-4">
                    @foreach($recentLedgerEntries as $entry)
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-emerald-50 to-emerald-100 rounded-xl transition-all duration-200 hover:shadow-md">
                            <div>
                                <div class="text-sm font-semibold text-gray-800 mb-1">{{ $entry->category }}</div>
                                <div class="text-xs text-gray-600">
                                    {{ $entry->crop->name ?? 'Sin cultivo' }} • 
                                    {{ $entry->occurred_at->format('d/m/Y') }}
                                </div>
                            </div>
                            <span class="text-sm font-bold {{ $entry->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $entry->type === 'income' ? '+' : '-' }}${{ number_format($entry->amount, 2) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state text-center py-8">
                    <i data-lucide="banknote" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                    <p class="text-gray-500 font-medium">No hay movimientos recientes</p>
                    <p class="text-sm text-gray-400 mt-1">Los movimientos financieros aparecerán aquí</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Mapa de Ubicación de la Finca -->
    <div class="admin-card rounded-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-emerald-100 rounded-lg">
                    <i data-lucide="map-pin" class="w-5 h-5 text-emerald-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800">Ubicación de la Finca</h3>
            </div>
            <a href="{{ route('admin.farm-settings.edit') }}" class="text-sm text-emerald-600 hover:text-emerald-700 inline-flex items-center gap-1">
                <i data-lucide="settings" class="w-4 h-4"></i>
                <span>Configurar</span>
            </a>
        </div>
        @if($farmSettings->latitude && $farmSettings->longitude)
        <div class="mb-2">
            <p class="text-sm text-gray-600"><strong>Nombre:</strong> {{ $farmSettings->name }}</p>
            @if($farmSettings->address)
                <p class="text-sm text-gray-600"><strong>Dirección:</strong> {{ $farmSettings->address }}</p>
            @endif
            <p class="text-sm text-gray-600">
                <strong>Coordenadas:</strong> {{ number_format($farmSettings->latitude, 6) }}, {{ number_format($farmSettings->longitude, 6) }}
            </p>
        </div>
        <div id="farmMap" style="width: 100%; height: 400px; border-radius: 8px; overflow: hidden;"></div>
        @else
        <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
            <i data-lucide="map-pin" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
            <p class="text-gray-600 font-medium mb-2">No se ha configurado la ubicación de la finca</p>
            <a href="{{ route('admin.farm-settings.edit') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded-lg transition-colors">
                <i data-lucide="settings" class="w-4 h-4"></i>
                <span>Configurar Ubicación</span>
            </a>
        </div>
        @endif
    </div>
  </div>
@endsection

@push('scripts')
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
                farmMapContainer.innerHTML = '<div class="p-4 bg-yellow-50 border border-yellow-200 rounded"><p class="text-yellow-800">Error: No se ha configurado el token de Mapbox. Verifica tu archivo .env</p></div>';
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

            // Agregar controles de navegación
            map.addControl(new mapboxgl.NavigationControl());

            // Crear marcador
            const marker = new mapboxgl.Marker()
                .setLngLat(farmLocation)
                .setPopup(
                    new mapboxgl.Popup({ offset: 25 })
                        .setHTML(`
                            <div style="padding: 10px;">
                                <h3 style="margin: 0 0 5px 0; font-weight: bold;">{{ $farmSettings->name }}</h3>
                                @if($farmSettings->address)
                                    <p style="margin: 0; color: #666;">{{ $farmSettings->address }}</p>
                                @endif
                                <p style="margin: 5px 0 0 0; color: #666; font-size: 12px;">
                                    Lat: {{ number_format($farmSettings->latitude, 6) }}, 
                                    Lng: {{ number_format($farmSettings->longitude, 6) }}
                                </p>
                            </div>
                        `)
                )
                .addTo(map);

            // Abrir popup al hacer clic en el marcador
            marker.togglePopup();

            // Agregar el polígono de la finca si existe
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

                    // Ajustar el mapa para mostrar el polígono
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
                farmMapContainer.innerHTML = '<div class="p-4 bg-red-50 border border-red-200 rounded"><p class="text-red-800">Error al cargar el mapa. Por favor, recarga la página.</p></div>';
            }
        }
    });
</script>
@endif
@endpush


